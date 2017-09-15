<?php

/**
 *
 * @package phpBB Extension - UKRGB Template
 * @copyright (c) 2016 Mark Gawler
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace ukrgb\fix\acp;

class main_module
{
	var $u_action;
	function main($id, $mode)
	{
		global $request, $template, $user, $db;
		$user->add_lang('acp/common');
		$user->add_lang('fix', false, false, 'ukrgb/fix');
		$this->tpl_name = 'fix_body';
		$this->page_title = $user->lang('ACP_UKRGB_FIX_TITLE');
		add_form_key('ukrgb/fix');
		$submit = $request->is_set_post('submit');
		$check_all = $request->is_set_post('check_all');
		$fix_all = $request->is_set_post('fix_all');
		$fix_one = $request->is_set_post('fix_one');
		$database_hash = 0;
		$count = 0;
		$calculated_hash =  $request->variable('email_hash',0);
		$user_name =  $request->variable('h_user_name','');
		$user_id =  $request->variable('user_id','');
		$hidden_fields = array();
		switch ($mode)
		{
			case 'settings':
				if (($submit or $check_all or $fix_all or $fix_one) and !check_form_key('ukrgb/fix'))
				{
					trigger_error('FORM_INVALID');
				}
				if ($submit)
				{

					$user_name =  $request->variable('user_name','');

					if ($user_name != '')
					{
						$sql_array = array(
							'SELECT'	=> 'user_id, username, user_email, user_email_hash',
							'FROM'		=> array(USERS_TABLE => 'u'),
							'WHERE'		=> "username_clean = '" . $db->sql_escape(utf8_clean_string($user_name)) . "'"
						);

						$sql = $db->sql_build_query('SELECT', $sql_array);
						$result = $db->sql_query($sql);
						$user_row = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						if (!$user_row)
						{
							trigger_error('NO_EMAIL_USER');
						}

						if ($user_row['user_type'] == USER_IGNORE)
						{
							trigger_error('NO_USER');
						}

						$database_hash = $user_row['user_email_hash'];
						$calculated_hash = phpbb_email_hash($user_row['user_email']);
						$user_name = $user_row['username'];

						$hidden_fields = array(
							'user_id'			=> $user_row['user_id'],
							'h_user_name'		=> $user_name,
							'email_hash'		=> $calculated_hash,
						);

					}

				}
				if ($fix_one)
				{
					$sql_array = array('user_email_hash' => $calculated_hash);
					$sql = 'UPDATE ' . USERS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', $sql_array) . '
							WHERE user_id = ' . $user_id;
					$db->sql_query($sql);
					$database_hash = $calculated_hash;
				}

				if ($check_all)
				{
					$sql_array = array(
						'SELECT'	=> 'username, user_email, user_email_hash',
						'FROM'		=> array(USERS_TABLE => 'u'),
					);

					$sql = $db->sql_build_query('SELECT', $sql_array);
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$calculated_hash = phpbb_email_hash($row['user_email']);
						if ($row['user_email_hash'] != $calculated_hash)
						{
							$count = $count +1;
						}
					}
					$db->sql_freeresult($result);
				}

				if ($fix_all)
				{
					$sql_array = array(
						'SELECT'	=> 'user_id, username, user_email, user_email_hash',
						'FROM'		=> array(USERS_TABLE => 'u'),
					);

					$sql = $db->sql_build_query('SELECT', $sql_array);
					$result = $db->sql_query($sql);
					$count = 0;
					while ($row = $db->sql_fetchrow($result))
					{
						$calculated_hash = phpbb_email_hash($row['user_email']);
						if ($row['user_email_hash'] != $calculated_hash)
						{
							$count = $count +1;
							$sql2 = 'UPDATE ' . USERS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', array('user_email_hash' => $calculated_hash)) . '
							WHERE user_id = ' . $row['user_id'];
							$db->sql_query($sql2);
						}
					}
					$db->sql_freeresult($result);
				}

				if (!$submit)
				{
					$hidden_fields = array(
						'user_id'			=> $user_id,
						'h_user_name'		=> $user_name,
						'email_hash'		=> $calculated_hash,
					);
				}

				$template->assign_vars(array(
					'U_ACTION'      	=> $this->u_action,
					'UKRGB_FIX_CALC_HASH' 	=> $calculated_hash,
					'UKRGB_FIX_DB_HASH' => $database_hash,
					'UKRGB_ACP_MODE'	=> $mode,
					'UKRGB_FIX_USER_NAME'	=> $user_name,
					'UKRGB_FIX_HASH_MATCH' => ($calculated_hash == $database_hash),
					'UKRGB_ACP_CHECK_ALL' => $check_all,
					'UKRGB_FIX_MISMATCH_COUNT' => $count,
					'S_HIDDEN_FIELDS'	=> build_hidden_fields($hidden_fields),
				));
			break;
		}
	}
}