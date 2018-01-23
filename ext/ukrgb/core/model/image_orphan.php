<?php
/**
 * Created by PhpStorm.
 * User: mrfg
 * Date: 20/01/18
 * Time: 17:56
 */

namespace ukrgb\core\model;


class image_orphan
{
    /* @var \ukrgb\core\utils\aws_s3 */
    protected $aws_s3;

    /* @var string */
    protected $prefix;

    /* @var string */
    protected $bucket;

    /* @var \phpbb\db\driver\driver_interface */
    protected $db;

    /* @var string */
    protected $table;


    /**
     * image_orphan constructor.
     * @param \ukrgb\core\utils\aws_s3  $aws_s3
     * @param \phpbb\config\config      $config
     * @param \phpbb\db\driver\driver_interface $db
     * @param string $image_table
    */
    public function __construct($aws_s3,$config, $db ,$image_table)
    {
        $this->aws_s3 = $aws_s3;
        $this->prefix = $config['ukrgb_image_s3_prefix'];
        $this->bucket = $config['ukrgb_image_s3_bucket'];
        $this->db = $db;
        $this->table = $image_table;
    }

    public function find_orphan_images()
    {
        $objects = $this->aws_s3->get_iterator($this->prefix, $this->bucket);
        $orphan_count = 0;
        $invalid_count = 0;
        foreach ($objects as $object) {
            $field = explode('/', $object['Key']);
            $user_id = $field[1];
            $file_key = substr($field[2], 0, strpos($field[2], "."));


            $modified_time = $object['LastModified']->getTimestamp();

            if (preg_match('#^[0-9]{13}-[0-9]{4}$#', $file_key) !== 0)
            {
                $image = new \ukrgb\core\model\image($this->db, $this->table, $file_key);
                if ($image->is_new_image()){
                    $image->update_and_store_upload_data($user_id, $modified_time);
                    $orphan_count += 1;
                }
            } else {
                $invalid_count += 1;
                error_log("Invalid File: " . $object['Key']);
            }
        }
        return array(
            'orphan_count' => $orphan_count,
            'invalid_count' => $invalid_count);
    }

}