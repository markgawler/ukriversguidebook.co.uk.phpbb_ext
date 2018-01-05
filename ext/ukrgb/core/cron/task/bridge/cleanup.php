<?php
/**
 * User: Mark Gawler
 * Date: 04/01/18
 */

namespace ukrgb\core\cron\task\bridge;

class cleanup  extends \phpbb\cron\task\base
{
    protected $config;
    protected $db;
    protected $ukrgb_images_table;

    /**
     * Constructor - cleanup
     *
     * @param \phpbb\config\config $config              The config
     * @param \phpbb\db\driver\driver_interface $db     The db connection
     * @param $ukrgb_images_table
     */
    public function __construct(
        \phpbb\config\config $config,
        \phpbb\db\driver\driver_interface $db,
        $ukrgb_images_table)
    {
        $this->config = $config;
        $this->db = $db;
        $this->ukrgb_images_table = $ukrgb_images_table;
    }
    /**
     * Runs this cron task.
     *
     */
    public function run()
    {
        error_log('Cron Run - cleanup');

        $this->config->set('ukrgb_cleanup_last_gc', time());

        $imageQueue = new \ukrgb\core\model\image_client(
            $this->config,
            $this->db,
            $this->ukrgb_images_table);
        $imageQueue->runTask();
    }
    /**
     * Returns whether this cron task can run, given current board configuration.
     *
     * @return bool
     */
    public function is_runnable()
    {
        return $this->config['ukrgb_image_sqs_enabled'];
    }
    /**
     * Returns whether this cron task should run now, because enough time
     * has passed since it was last run.
     *
     * @return bool
     */
    public function should_run()
    {
        return ((time() - $this->config['ukrgb_cleanup_last_gc']) >  $this->config['ukrgb_cleanup_gc']);
    }
}