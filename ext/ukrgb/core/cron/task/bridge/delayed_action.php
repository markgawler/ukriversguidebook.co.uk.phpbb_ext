<?php
/**
 *
 * @package delayed_action
 * @copyright (c) 2017 Mark Gawler
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace ukrgb\core\cron\task\bridge;

class delayed_action extends \phpbb\cron\task\base
{
	protected $config;
	protected $db;
	protected $ukrgb_fb_posts_table;
	protected $ukrgb_pending_actions_table;
	protected $ukrgb_images_table;
	
	/**
     * Constructor - delayed_action
     *
     * @param \phpbb\config\config $config              The config
     * @param \phpbb\db\driver\driver_interface $db     The db connection
     * @param $ukrgb_fb_posts_table
     * @param $ukrgb_pending_actions_table
     */
	public function __construct(
			\phpbb\config\config $config,  
			\phpbb\db\driver\driver_interface $db,
			$ukrgb_fb_posts_table,
			$ukrgb_pending_actions_table)
	{
		$this->config = $config;
		$this->db = $db;
		$this->ukrgb_fb_posts_table = $ukrgb_fb_posts_table;
        $this->ukrgb_pending_actions_table = $ukrgb_pending_actions_table;
	}
	/**
	 * Runs this cron task.
	 *
	 */
	public function run()
    {
        $this->config->set('ukrgb_delayed_action_last_gc', time());

        $ukrgbFacebook = new \ukrgb\core\model\facebook_bridge(
                $this->config,
                $this->db,
                $this->ukrgb_fb_posts_table,
                $this->ukrgb_pending_actions_table
                );
        $ukrgbFacebook->runTasks();
    }
	/**
	 * Returns whether this cron task can run, given current board configuration.
	 *
	 * @return bool
	 */
	public function is_runnable()
	{
		return $this->config['ukrgb_fb_auto_post'];
	}
	/**
	 * Returns whether this cron task should run now, because enough time
	 * has passed since it was last run.
	 *
	 * @return bool
	 */
	public function should_run()
	{		
		return ((time() - $this->config['ukrgb_delayed_action_last_gc']) >  $this->config['ukrgb_delayed_action_gc']);
	}
}