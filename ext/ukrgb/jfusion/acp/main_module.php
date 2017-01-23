<?php
/**
*
* @package phpBB Extension - JFusion phpBB Extension
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\jfusion\acp;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $config, $request, $template, $user;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;
		
		
		$user->add_lang('acp/common');
		$user->add_lang('jfusion', false, false, 'ukrgb/jfusion');
		$this->page_title = $user->lang('ACP_UKRGB_JFUSION_TITLE');
		
		$this->tpl_name = 'jfusion_body';
		add_form_key('ukrgb/jfusion');

		if ($request->is_set_post('submit')) {
			if (!check_form_key('ukrgb/jfusion')) {
				trigger_error('FORM_INVALID');
			}
			$config->set('ukrgb_jfusion_jname',   $request->variable('ukrgb_jfusion_jname', ''));
			$config->set('ukrgb_jfusion_apipath', $request->variable('ukrgb_jfusion_apipath', ''));
			trigger_error($user->lang('ACP_UKRGB_JFUSION_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
			'UKRGB_JFUSION_JNAME'		=> $config['ukrgb_jfusion_jname'],
			'UKRGB_JFUSION_APIPATH'		=> $config['ukrgb_jfusion_apipath'],
		));
	}
}
