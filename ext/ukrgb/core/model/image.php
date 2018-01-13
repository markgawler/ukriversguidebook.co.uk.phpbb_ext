<?php
/**
 * User: Mark Gawler
 * Date: 21/12/17
 *
 * UKRGB Core extension image upload forum backend.
 *
 * @copyright (c) Mark Gawler 2017
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace ukrgb\core\model;


class image
{
    /**
     * Database driver
     *
     * @var \phpbb\db\driver\driver_interface
     */
    protected $db;

    /**
     * phpBB config
     *
     * @var \phpbb\config\config
     */
    protected $config;

    /**
     * ukrgb image table
     *
     * @var \phpbb\config\config
     */
    protected $ukrgb_images_table;

    /**
     * @var string file_key
     */
    protected $file_key;

    /**
     * @var int forum_id
     */
    protected $forum_id;

    /**
     * @var int topic_id
     */
    protected $topic_id;

    /**
     * @var int post_id
     */
    protected $post_id;

    /**
     * @var int $upload_time in seconds
     */
    protected $upload_time;

    /**
     * @var int $poster_id the phpbb user id of the poster
     */
    protected $poster_id;

    /**
     * Constructor for ukrgb Image
     *
     * @param   \phpbb\db\driver\driver_interface
     * @param   string $ukrgb_images_table
     * @param   string $file_key
     * @param   integer $forum_id = 0
     * @param   integer $topic_id = 0
     * @param   integer $post_id = 0
     * @param   integer $upload_time = 0
     * @param   integer $poster_id = 0
     */
    public function __construct(
        \phpbb\db\driver\driver_interface $db,
        $ukrgb_images_table,
        $file_key,
        $forum_id = 0,
        $topic_id = 0,
        $post_id = 0,
        $upload_time = 0,
        $poster_id = 0)
    {
        $this->file_key = $file_key;
        $this->db = $db;
        $this->ukrgb_images_table = $ukrgb_images_table;
        $this->forum_id = $forum_id;
        $this->topic_id = $topic_id;
        $this->post_id = $post_id;
        $this->upload_time = $upload_time;
        $this->poster_id = $poster_id;
    }

    /**
     * Clear existing object and populate with new upload data, saves destroying and recreating the image object.
     * @param $file_key
     * @param $poster_id
     * @param int $upload_time
     */
    public function new_upload_data($file_key, $poster_id = 0, $upload_time = 0)
    {
        $this->clear_data();
        $this->file_key = $file_key;
        $this->upload_time = $upload_time;
        $this->poster_id = $poster_id;
    }

    /**
     * Clear existin object and populate with new forum data, saves destroying and recreating the image object.
     * @param $file_key
     * @param $forum_id
     * @param $topic_id
     * @param $post_id
     */
    public function new_forum_data($file_key, $forum_id, $topic_id, $post_id)
    {
        $this->clear_data();
        $this->file_key = $file_key;
        $this->forum_id = $forum_id;
        $this->topic_id = $topic_id;
        $this->post_id = $post_id;
    }

    /**
     * Update the file data,
     * @param $poster_id
     * @param int $upload_time
     */
    public function update_and_store_upload_data($poster_id = 0, $upload_time = 0)
    {
        $this->upload_time = $upload_time;
        $this->poster_id = $poster_id;
        $this->store_upload_data();
    }

    /**
     * Update the forum data,
     * @param $forum_id
     * @param $topic_id
     * @param $post_id
     */
    public function update_and_store_forum_data($forum_id, $topic_id, $post_id)
    {
        $this->forum_id = $forum_id;
        $this->topic_id = $topic_id;
        $this->post_id = $post_id;
        $this->store_forum_data();
    }

    protected function clear_data()
    {
        $this->file_key = '';
        $this->forum_id = 0;
        $this->topic_id = 0;
        $this->post_id = 0;
        $this->upload_time = 0;
        $this->poster_id = 0;
    }

    /**
    * @return boolean
    */
    public function is_in_post()
    {
        $select_data = array('file_key' => $this->file_key);
        $sql = 'SELECT in_post FROM ' . $this->ukrgb_images_table . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);

        return ($row['in_post'] === 1);
    }

    /**
     *
     */
    public function is_new_image()
    {
        $select_data = array('file_key' => $this->file_key);
        $sql = 'SELECT id FROM ' . $this->ukrgb_images_table . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);

        return ($row['id'] === null);
    }


    /**
     * @param bool $in_post = false
     */
    public function store_upload_data()
    {
        if ($this->is_new_image()) {
            $data = array(
                'file_key' => $this->file_key,
                'upload_time' => $this->upload_time,
                'poster_id' => $this->poster_id,
            );
            $sql = 'INSERT INTO ' . $this->ukrgb_images_table . ' ' . $this->db->sql_build_array('INSERT', $data);
        } else {
            $data = array(
                'upload_time' => $this->upload_time,
                'poster_id' => $this->poster_id,
            );
            $sql = 'UPDATE ' . $this->ukrgb_images_table . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE file_key = ' . $this->db->sql_escape($this->file_key);
        }
        $this->db->sql_query($sql);
    }

    /**
     * @param bool $in_post = true
     */
    public function store_forum_data($in_post = true)
    {
        $data = array (
            'in_post' => $in_post,
            'post_id' => $this->post_id,
            'topic_id' => $this->topic_id,
            'forum_id' => $this->forum_id,
           // 'poster_id' => $this->poster_id

        );
        if ($this->is_new_image()) {
            $sql = 'INSERT INTO ' . $this->ukrgb_images_table . ' ' . $this->db->sql_build_array('INSERT', array_merge(array('file_key' => $this->file_key),$data));
        } else {
            $select_data = array('file_key' => $this->file_key);
            $sql = 'UPDATE ' . $this->ukrgb_images_table . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        }
        $this->db->sql_query($sql);
    }

    /**
     * @param $file_key string 18 char
     * @return mixed
     */
    public function get_all_image_data()
    {
        $select_data = array('file_key' => $this->file_key);
        $sql = 'SELECT * FROM ' . $this->ukrgb_images_table . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        //var_dump($row);
        return $row;
    }








    /**
     * @return array of data
     */
    public function get_upload_data()
    {
        $select_data = array('file_key' => $this->file_key);
        $sql = 'SELECT id,poster_id,upload_time FROM ' . $this->ukrgb_images_table . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        return $row;
    }

    /**
     * @return array of data
     */
    public function get_forum_data()
    {
        $select_data = array('file_key' => $this->file_key);
        $sql = 'SELECT post_id,forum_id,topic_id,in_post FROM ' . $this->ukrgb_images_table . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        return $row;
    }

}
