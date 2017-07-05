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
		global $config, $request, $template, $user, $cache;
		$user->add_lang('acp/common');
		$this->tpl_name = 'template_body';
		$this->page_title = $user->lang('ACP_UKRGB_TPL_TITLE');
		add_form_key('ukrgb/template');
		$submit = $request->is_set_post('submit');
		switch ($mode)
		{
			case 'settings':

				if ($submit)
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
					'U_ACTION'      	=> $this->u_action,
					'UKRGB_JDBUSER' 	=> $config['ukrgb_jdbuser'],
					'UKRGB_JDB'     	=> $config['ukrgb_jdb'],
					'UKRGB_JDBPWD'  	=> $config['ukrgb_jdbpwd'],
					'UKRGB_JDBHOST' 	=> $config['ukrgb_jdbhost'],
					'UKRGB_ACP_TPL_MODE'	=> $mode,

				));
			break;

			case 'page_banners':
				if ($submit)
				{
					if (!check_form_key('ukrgb/template'))
					{
						trigger_error('FORM_INVALID');
					}
					$page_banners = array ();
					foreach (range(0, 1) as $key)
					{
						if ($key == 0) {
							$forums = 0;
						} else {
							$forums = $request->variable('page_banner_forums_' . $key, '');
						}
						$page_banners[$key] = array(
							'img'    => $request->variable('page_banner_img_' . $key, ''),
							'fb_img'    => $request->variable('fb_banner_img_' . $key, ''),
							'forums' => $forums,
						);
					}
					$config->set('ukrgb_page_banners', json_encode($page_banners));
					$cache->put('_ukrgb_page_banner_lookup',null);

					trigger_error($user->lang('ACP_UKRGB_SETTING_SAVED') . adm_back_link($this->u_action));
				}
				$template_vars = array();

				foreach (json_decode($config['ukrgb_page_banners']) as $key => $data){
					$template_vars['UKRGB_PAGE_BANNER_' . $key] = $data->img;
					$template_vars['UKRGB_FB_BANNER_' . $key] = $data->fb_img;
					$template_vars['UKRGB_DISPLAY_FORUMS_' . $key] = $data->forums;
				}

				$template->assign_vars(array_merge($template_vars, array(
					'U_ACTION'      	=> $this->u_action,
					'UKRGB_ACP_TPL_MODE'	=> $mode,
				)));
			break;


		}
	}
}