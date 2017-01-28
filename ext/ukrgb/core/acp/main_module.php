<?php
/**
*
* @package phpBB Extension - UKRGB core phpBB Extension
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\core\acp;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $config, $request, $template, $user;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;
		
		$user->add_lang('acp/common');
		$user->add_lang('core', false, false, 'ukrgb/core');
		$this->page_title = $user->lang('ACP_UKRGB_CORE_TITLE');
		
		$this->tpl_name = 'core_body';
		add_form_key('ukrgb/core');

		if ($request->is_set_post('submit')) {
			if (!check_form_key('ukrgb/core')) {
				trigger_error('FORM_INVALID');
			}
			$config->set('ukrgb_jfusion_jname',   $request->variable('ukrgb_jfusion_jname', ''));
			$config->set('ukrgb_jfusion_apipath', $request->variable('ukrgb_jfusion_apipath', ''));
			$config->set('ukrgb_secret', $request->variable('ukrgb_secret', ''));
			trigger_error($user->lang('ACP_UKRGB_CORE_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		$secret =  $config['ukrgb_secret'];
		if ( $secret == ''){
			$secret = gen_rand_string_friendly(16, 24);
			$this->config['ukrgb_secret'] = $secret;
		}
		
		$template->assign_vars(array(
			'U_ACTION'					=> $this->u_action,
			'UKRGB_JFUSION_JNAME'		=> $config['ukrgb_jfusion_jname'],
			'UKRGB_JFUSION_APIPATH'		=> $config['ukrgb_jfusion_apipath'],
			'UKRGB_SECRET'				=> $secret,
		));
	}
}
