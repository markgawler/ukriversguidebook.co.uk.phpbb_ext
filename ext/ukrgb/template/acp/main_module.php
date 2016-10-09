<?php

/**
 *
 * @package phpBB Extension - UKRGB Template
 * @copyright (c) 2016 Mark Gawler
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace ukrgb\template\acp;
class main_module
{
	var $u_action;
	function main($id, $mode)
	{
		global $config, $request, $template, $user;
		$user->add_lang('acp/common');
		$this->tpl_name = 'template_body';
		$this->page_title = $user->lang('ACP_UKRGB_TPL_TITLE');
		add_form_key('ukrgb/template');
		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('ukrgb/template'))
			{
				trigger_error('FORM_INVALID');
			}
			$config->set('ukrgb_jdbuser', $request->variable('ukrgb_jdbuser', ''));
			$config->set('ukrgb_jdb', $request->variable('ukrgb_jdb', ''));
			$config->set('ukrgb_jdbpwd', $request->variable('ukrgb_jdbpwd', ''));
			$config->set('ukrgb_jdbhost', $request->variable('ukrgb_jdbhost', ''));
			trigger_error($user->lang('ACP_UKRGB_SETTING_SAVED') . adm_back_link($this->u_action));
		}
		$template->assign_vars(array(
				'U_ACTION'				=> $this->u_action,
				'UKRGB_JDBUSER'		=> $config['ukrgb_jdbuser'],
				'UKRGB_JDB'			=> $config['ukrgb_jdb'],
				'UKRGB_JDBPWD'		=> $config['ukrgb_jdbpwd'],
				'UKRGB_JDBHOST'		=> $config['ukrgb_jdbhost'],
		));
	}
}