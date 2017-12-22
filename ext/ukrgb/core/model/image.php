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
     * Constructor for ukrgb Image
     *
     * @param	\phpbb\config\config $config
     * @param   \phpbb\db\driver\driver_interface
     * @param   string $ukrgb_images_table
     * @param   integer $forum_id
     * @param   integer $topic_id
     * @param   integer $post_id
     */
    public function __construct(
        \phpbb\config\config $config,
        \phpbb\db\driver\driver_interface $db,
        $ukrgb_images_table,
        $forum_id,
        $topic_id,
        $post_id)
    {
        $this->config = $config;
        $this->db = $db;
        $this->ukrgb_images_table = $ukrgb_images_table;
        $this->forum_id = $forum_id;
        $this->topic_id = $topic_id;
        $this->post_id = $post_id;
    }


    /**
     * @param $file_key
     * @param $user_id
     */
    public function set_image_is_posted($file_key, $user_id)
    {
        $image_data = $this->get_image_data($file_key);
        if ($image_data) {
            // Update
            if ($image_data['in_post'] == 0) {
                // Only update the database if the image has not been found in a post before.
                $this->update_image_date($image_data['id']);
            }
        }
        else {
            // Insert
            $this->insert_image_data($file_key, $user_id);
        }
    }

    /**
     * @param $image_id string 18 char
     * @return mixed
     */
    protected function get_image_data($image_id)
    {
        $select_data = array('file_key' => $image_id);
        $sql = 'SELECT id,in_post FROM ' . $this->ukrgb_images_table . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);

        return $row;
    }

    /**
     * array =
     * file_key
     * poster_id
     * post_id
     * topic_id
     * forum_id
     * in_post
     */

    /**
     * @param $id
     */
    protected function update_image_date($id)
    {
        error_log('Update image: ' . $id);
        $data = array (
            'in_post' => true,
            'post_id' => $this->post_id,
            'topic_id' => $this->topic_id,
            'forum_id' => $this->forum_id
        );
        $sql = 'UPDATE ' . $this->ukrgb_images_table . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE id = ' . (int) $id;
        $this->db->sql_query($sql);
    }

    protected function insert_image_data($file_key, $user_id)
    {
        $data = array (
            'file_key' => $file_key,
            'poster_id' => $user_id,
            'in_post' => true,
            'post_id' => $this->post_id,
            'topic_id' => $this->topic_id,
            'forum_id' => $this->forum_id
        );
        $sql = 'INSERT INTO ' . $this->ukrgb_images_table . ' ' . $this->db->sql_build_array('INSERT', $data);
        $this->db->sql_query($sql);
    }


}
