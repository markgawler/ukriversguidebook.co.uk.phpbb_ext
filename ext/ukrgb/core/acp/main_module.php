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

	protected $token_storage;
	
	function main($id, $mode)
	{
		global $db, $config, $request, $template, $user;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;
		global $phpbb_container;
		
		$user->add_lang('acp/common');
		$user->add_lang('core', false, false, 'ukrgb/core');
		$this->page_title = $user->lang('ACP_UKRGB_CORE_TITLE');
		
		$this->tpl_name = 'core_body';
		$submit = $request->is_set_post('submit');

		add_form_key('ukrgb/core');

		$commonVars = array(
				'UKRGB_ACP_MODE'					=> $mode,
				'U_ACTION'					=> $this->u_action,
				);
		
		switch ($mode)
		{
			case 'settings':
				
				if ($submit) {
					if (!check_form_key('ukrgb/core')) {
						trigger_error('FORM_INVALID');
					} else {
						$config->set('ukrgb_jfusion_jname',   $request->variable('ukrgb_jfusion_jname', ''));
						$config->set('ukrgb_jfusion_apipath', $request->variable('ukrgb_jfusion_apipath', ''));
						$config->set('ukrgb_secret', $request->variable('ukrgb_secret', ''));

						trigger_error($user->lang('ACP_UKRGB_CORE_SETTING_SAVED') . adm_back_link($this->u_action));
					}
				}

				$secret =  $config['ukrgb_secret'];
				if ( $secret == ''){
					$secret = gen_rand_string_friendly(16, 24);
					$this->config['ukrgb_secret'] = $secret;
				}
				
				//$fb_token_exp = $this->get_token_expiry_date();
				
				$template->assign_vars(array_merge($commonVars, array(
						'UKRGB_JFUSION_JNAME'		=> $config['ukrgb_jfusion_jname'],
						'UKRGB_JFUSION_APIPATH'		=> $config['ukrgb_jfusion_apipath'],
						'UKRGB_SECRET'				=> $secret,
				)));
				break;
				
			case 'fb_app_settings':
				if ($submit) {
					if (!check_form_key('ukrgb/core')) {
						trigger_error('FORM_INVALID');
					} else {
						$config->set('ukrgb_fb_appid', $request->variable('ukrgb_fb_appid', ''));
						$config->set('ukrgb_fb_secret', $request->variable('ukrgb_fb_secret', ''));
						$config->set('ukrgb_fb_page_mgr', $request->variable('ukrgb_fb_page_mgr', ''));
						$config->set('ukrgb_fb_page_id', $request->variable('ukrgb_fb_page_id', ''));
						trigger_error($user->lang('ACP_UKRGB_CORE_SETTING_SAVED') . adm_back_link($this->u_action));
					}
				}	
				
				$appVars = array(
						'UKRGB_FB_APPID'			=> $config['ukrgb_fb_appid'],
						'UKRGB_FB_SECRET'			=> $config['ukrgb_fb_secret'],
						'UKRGB_FB_PAGE_MGR'			=> $config['ukrgb_fb_page_mgr'],
						'UKRGB_FB_PAGE_ID'			=> $config['ukrgb_fb_page_id'],
				);
								
				if ($user->data['user_id'] == $config['ukrgb_fb_page_mgr'])
				{
					$helper = $phpbb_container->get('controller.helper');
					$ukrgb_admin = new \ukrgb\core\controller\admin($config, $request, $user, $helper);
					$tokenData = $ukrgb_admin->getTokenMetaData($accessToken);
						
					$pageTokenVars = array(
							'U_UKRGB_GET_FB_TOKEN'   => $ukrgb_admin->get_request_permisions_url(),
							'UKRGB_FB_TOKEN_REFRESH' => true,
							'UKRGB_FBPT_APP_ID'      => $tokenData['app_id'],
							'UKRGB_FBPT_APP_NAME'    => $tokenData['app_name'],
							'UKRGB_FBPT_EXPIRES'     => $tokenData['expires_at'],
							'UKRGB_FBPT_VALID'       => $tokenData['valid'],
							'UKRGB_FBPT_ISSUED'      => $tokenData['issued'],
							'UKRGB_FBPT_SCOPE'       => $tokenData['scope'],
					);
					$ukrgb_admin->getTokenMetaData($accessToken);
					$modeVars = array_merge($pageTokenVars, $appVars);
					$template->assign_vars(array_merge($commonVars, $appVars, $pageTokenVars));
						
				}
				else
				{
					$template->assign_vars(array_merge($commonVars, $appVars));
				}
				
				break;	
			//end case
		
		}
		
				

	}
	
	
}
