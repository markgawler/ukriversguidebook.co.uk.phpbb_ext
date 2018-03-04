<?php

/**
 *
 * @package phpBB Extension - UKRGB Template
 * @copyright (c) 2016 Mark Gawler
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace ukrgb\bbcode\acp;

class main_module
{
    /** @var string $page_title The page title */
    public $page_title;

    /** @var string $u_action Custom form action */
    public $u_action;

    /** @var string $tpl_name The page template name */
    public $tpl_name;

    /** @var string $post_table  */
    protected $post_table;

    /** @var \phpbb\db\driver\driver_interface $db */
    protected $db;

    /**
     * @param $id
     * @param $mode
     * @throws \Exception
     */
	function main(/** @noinspection PhpUnusedParameterInspection */ $id, $mode)
	{
        global $phpbb_container;
        global $db, $table_prefix;
        //global $table_prefix;

        /** @var \phpbb\language\language $language Language object */
        $language = $phpbb_container->get('language');

        /** @var \phpbb\request\request $request Request object */
        $request  = $phpbb_container->get('request');

        /** @var \phpbb\template\template $template Template object */
        $template = $phpbb_container->get('template');

        /** @var \phpbb\db\driver\driver_interface $db Database object */
        $this->db = $db; //$phpbb_container->get('db');

        $this->post_table = $table_prefix . 'posts';

        $this->tpl_name = 'bbcode_body';

		$this->page_title = $language->lang('ACP_UKRGB_BBCODE_TITLE');

		$submit = $request->is_set_post('submit');
		$hidden_fields = array();

        add_form_key('ukrgb/bbcode');
        $post_data['post_text'] = '';
        $post_id = '';
        $post_text = '';
        $new_text = '';
        switch ($mode) {
            case 'settings':
                if ($submit and !check_form_key('ukrgb/bbcode')) {
                    trigger_error('FORM_INVALID');
                } else {
                    $post_id = $request->variable('post_id', '');
                    if ($post_id != '') {
                        $post_data = $this->get_post($post_id);

                        $post_text = $post_data['post_text'];
                        decode_message($post_text, $post_data['bbcode_uid']);
                        $pattern = '/(\[youtube\])([0-9a-zA-Z\-_]+)(\[\/youtube\])/i';
                        $replacement = 'https://www.youtube.com/watch?v=$2';
                        $new_text = preg_replace($pattern, $replacement, $post_text);

                        $this->update_post($post_id,$post_data['forum_id'],$post_data['topic_id'],$post_data['post_subject'],$new_text,$post_data['post_username'], $post_data['poster_id']);

                    }
                }

                $template->assign_vars(array(
                    'UKRGB_ACP_MODE'	=> $mode,
                    'UKRGB_BBCODE_POST_ID' => $post_id,
                    'UKRGB_BBCODE_EDIT_POST_TEXT' => $post_text,
                    'UKRGB_BBCODE_NEW_POST_TEXT' => $new_text,
                    'S_HIDDEN_FIELDS'	=> build_hidden_fields($hidden_fields),
				));
			break;
		}
	}


    /**
     * @param $post_id
     * @return mixed
     */
    public function get_post($post_id)
    {
        $select_data = array('post_id' => $post_id);
        $sql = 'SELECT forum_id, topic_id, poster_id, post_subject, post_text, post_username, bbcode_uid, bbcode_bitfield, enable_bbcode, enable_smilies, enable_magic_url FROM ' . $this->post_table . ' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        //var_dump($row);
        //die();
        return $row;
    }


    public function update_post($post_id, $forum_id, $topic_id, $subject, $post_text, $username, $poster_id)
    {
        global $phpbb_root_path, $phpEx;

        include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
        $message = utf8_normalize_nfc($post_text);
        $uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
        $allow_bbcode = $allow_urls = true;
        $allow_smilies = false;
        $this-> my_generate_text_for_storage($message, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);



        var_dump($message);
        var_dump($uid);
        var_dump($bitfield);
        var_dump($options);
        var_dump($allow_bbcode);
        var_dump($allow_urls);
        var_dump($allow_smilies);
        //die();

        $data = array(
            'poster_id' => $poster_id,
            'post_id' => $post_id,
            // General Posting Settings
            'forum_id'      => $forum_id,    // The forum ID in which the post will be placed. (int)
            'topic_id'      => $topic_id,    // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
            'icon_id'       => false,        // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)

            // Defining Post Options
            'enable_bbcode' => $allow_bbcode,    // Enable BBcode in this post. (bool)
            'enable_smilies'    => $allow_smilies,    // Enabe smilies in this post. (bool)
            'enable_urls'        => $allow_urls,    // Enable self-parsing URL links in this post. (bool)
            'enable_sig'        => true,    // Enable the signature of the poster to be displayed in the post. (bool)

            // Message Body
            'message'            => $message,        // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
            'message_md5'    => md5($message),// The md5 hash of your message

            // Values from generate_text_for_storage()
            'bbcode_bitfield'    => $bitfield,    // Value created from the generate_text_for_storage() function.
            'bbcode_uid'        => $uid,        // Value created from the generate_text_for_storage() function.

            // Other Options
            'post_edit_locked'    => 0,        // Disallow post editing? 1 = Yes, 0 = No
            'topic_title'        => $subject,    // Subject/Title of the topic. (string)

            // Email Notification Settings
            'notify_set'        => false,        // (bool)
            'notify'            => false,        // (bool)
            'post_time'         => 0,        // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
            'forum_name'        => '',        // For identifying the name of the forum in a notification email. (string)

            // Indexing
            'enable_indexing'    => true,        // Allow indexing the post? (bool)

            'force_approved_state'    => true, // Allow the post to be submitted without going into unapproved queue
            'force_visibility'            => true, // Allow the post to be submitted without going into unapproved queue, or make it be deleted
        );
        $poll = '';
        submit_post('edit', $subject, $username, POST_NORMAL, $poll, $data);
    }


    function my_generate_text_for_storage(&$text, &$uid, &$bitfield, &$flags, $allow_bbcode = false, $allow_urls = false, $allow_smilies = false, $allow_img_bbcode = true, $allow_flash_bbcode = true, $allow_quote_bbcode = true, $allow_url_bbcode = true, $mode = 'post')
    {
        global $phpbb_root_path, $phpEx;

        $uid = $bitfield = '';
        $flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);

        if (!class_exists('parse_message'))
        {
            include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
        }

        $message_parser = new \parse_message($text);
        $message_parser->parse($allow_bbcode, $allow_urls, $allow_smilies, $allow_img_bbcode, $allow_flash_bbcode, $allow_quote_bbcode, $allow_url_bbcode, true, $mode);

        $text = $message_parser->message;
        $uid = $message_parser->bbcode_uid;

        $bitfield = $message_parser->bbcode_bitfield;
        return $message_parser->warn_msg;
    }
}