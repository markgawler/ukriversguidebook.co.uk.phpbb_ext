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


use foo\typewrong\error;

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

    protected $in_post;

    protected $is_new;
    /**
     * Constructor for ukrgb Image
     *
     * @param   \phpbb\db\driver\driver_interface
     * @param   string $ukrgb_images_table
     * @param   string $file_key
     * @param   integer $forum_id = null
     * @param   integer $topic_id = null
     * @param   integer $post_id = null
     * @param   integer $upload_time = null
     * @param   integer $poster_id = null
     */
    public function __construct(
        \phpbb\db\driver\driver_interface $db,
        $ukrgb_images_table,
        $file_key,
        $forum_id = null,
        $topic_id = null,
        $post_id = null,
        $upload_time = null,
        $poster_id = null)
    {
        $this->db = $db;
        $this->ukrgb_images_table = $ukrgb_images_table;
        $this->file_key = $file_key;
        $img_data = $this->get_all_image_data();
        $is_new = ($img_data['id'] === null);
        $this->is_new = $is_new;

        $this->forum_id = $this->set_value($forum_id,$img_data['forum_id'],0,$is_new);
        $this->topic_id = $this->set_value($topic_id,$img_data['topic_id'],0,$is_new);
        $this->post_id = $this->set_value($post_id,$img_data['post_id'],0,$is_new);
        $this->upload_time = $this->set_value($upload_time,$img_data['upload_time'],0,$is_new);
        $this->poster_id = $this->set_value($poster_id,$img_data['poster_id'],0,$is_new);

        if (empty($forum_id) && empty($post_id) && empty($topic_id) && $is_new) {
            $this-> in_post = false;
        } else {
            if ($is_new){
                $this-> in_post = true;
            } else {
                $this->in_post = ($img_data['in_post'] === 1);
            }
        }
    }

    private function set_value($param_val, $db_val, $default_val, $is_new)
    {
        if ($param_val !== null) {
            return $param_val;
        } else {
            if ($is_new) {
                return $default_val;
            } else {
                return $db_val;
            }
        }
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
        $this->in_post = ($forum_id || $topic_id || $post_id);
        $this->store_forum_data();
    }


    /**
    * @return boolean
    */
    public function is_in_post()
    {
        return ($this->in_post);
    }

    /**
     * @return boolean
     */
    public function is_new_image()
    {
        return $this->is_new;
    }


    /**
     */
    public function store_upload_data()
    {
        if ($this->is_new) {
            $data = array(
                'file_key' => $this->file_key,
                'upload_time' => $this->upload_time,
                'poster_id' => $this->poster_id,
            );
            $sql = 'INSERT INTO ' . $this->ukrgb_images_table . ' ' . $this->db->sql_build_array('INSERT', $data);
        } else {
            $data = array(
                'upload_time' => (int) $this->upload_time,
                'poster_id' => $this->poster_id,
            );
            $select_data = array('file_key' => $this->file_key);
            $sql = 'UPDATE ' . $this->ukrgb_images_table . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        }
        $this->db->sql_query($sql);
        $this->is_new = false;
    }

    /**
     */
    public function store_forum_data()
    {
        $data = array (
            'in_post' => $this->in_post,
            'post_id' => $this->post_id,
            'topic_id' => $this->topic_id,
            'forum_id' => $this->forum_id,
            'poster_id' => $this->poster_id,

        );
        if ($this->is_new) {
            $sql = 'INSERT INTO ' . $this->ukrgb_images_table . ' ' . $this->db->sql_build_array('INSERT', array_merge(array('file_key' => $this->file_key),$data));
        } else {
            $select_data = array('file_key' => $this->file_key);
            $sql = 'UPDATE ' . $this->ukrgb_images_table . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        }
        $this->db->sql_query($sql);
        $this->is_new = false;
    }

    /**
     */
    public function store_data()
    {
        $data = array (
            'in_post' => $this->in_post,
            'post_id' => $this->post_id,
            'topic_id' => $this->topic_id,
            'forum_id' => $this->forum_id,
            'poster_id' => $this->poster_id,
            'upload_time' => $this->upload_time,

        );
        if ($this->is_new) {
            $sql = 'INSERT INTO ' . $this->ukrgb_images_table . ' ' . $this->db->sql_build_array('INSERT', array_merge(array('file_key' => $this->file_key),$data));
        } else {
            $select_data = array('file_key' => $this->file_key);
            $sql = 'UPDATE ' . $this->ukrgb_images_table . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        }
        $this->db->sql_query($sql);
        $this->is_new = false;
    }

    /**
     * @return mixed
     */
    public function get_all_image_data()
    {
        $select_data = array('file_key' => $this->file_key);
        $sql = 'SELECT * FROM ' . $this->ukrgb_images_table . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
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
