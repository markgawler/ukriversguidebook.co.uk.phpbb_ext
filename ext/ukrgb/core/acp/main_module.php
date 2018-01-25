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
	//  var $u_action;

    /** @var string $page_title The page title */
    public $page_title;

    /** @var string $u_action Custom form action */
    public $u_action;

    /** @var string $tpl_name The page template name */
    public $tpl_name;

    /**
     * @param $id
     * @param $mode
     * @throws \Exception
     */
    function main(/** @noinspection PhpUnusedParameterInspection */ $id  , $mode)
	{
        global $phpbb_container;

        /** @var \phpbb\config\config $config Config object */
        $config = $phpbb_container->get('config');

        /** @var \phpbb\db\driver\driver_interface $db Database object */
        //$db = $phpbb_container->get('db');

        /** @var \phpbb\request\request $request Request object */
        $request  = $phpbb_container->get('request');

        /** @var \phpbb\template\template $template Template object */
        $template = $phpbb_container->get('template');

        /** @var \phpbb\language\language $language Language object */
        $language = $phpbb_container->get('language');

        global $user;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;
        global $db, $table_prefix;

        //$language->add_lang('core', 'ukrgb/core');


        $this->tpl_name = 'core_body';

        $this->page_title = $language->lang('ACP_UKRGB_CORE_TITLE');
		
		$submit = $request->is_set_post('submit');
        $submit_orphan = $request->is_set_post('orphan_image');


        add_form_key('ukrgb/core');

		$commonVars = array(
				'UKRGB_ACP_MODE'	=> $mode,
				'U_ACTION'			=> $this->u_action,
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
						$config->set('ukrgb_beta_enabled', $request->variable('ukrgb_beta_enabled', 0));
						$config->set('ukrgb_beta_group', $request->variable('ukrgb_beta_group', ''));

						trigger_error($language->lang('ACP_UKRGB_CORE_SETTING_SAVED') . adm_back_link($this->u_action));
					}
				}

				$secret =  $config['ukrgb_secret'];
				if ( $secret == ''){
					$secret = gen_rand_string_friendly(24);
					$config['ukrgb_secret'] = $secret;
				}
								
				$template->assign_vars(array_merge($commonVars, array(
						'UKRGB_JFUSION_JNAME'		=> $config['ukrgb_jfusion_jname'],
						'UKRGB_JFUSION_APIPATH'		=> $config['ukrgb_jfusion_apipath'],
						'UKRGB_SECRET'				=> $secret,
						'UKRGB_BETA_GROUP' 			=> $config['ukrgb_beta_group'],
						'UKRGB_BETA_ENABLED' 		=> $config['ukrgb_beta_enabled']
				)));
				break;
				
			case 'fb_app_settings':
				/** @noinspection PhpIncludeInspection */
                include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);
				
				if ($submit) {
					if (!check_form_key('ukrgb/core')) {
						trigger_error('FORM_INVALID');
					} else {
						$config->set('ukrgb_fb_appid', $request->variable('ukrgb_fb_appid', ''));
						$config->set('ukrgb_fb_secret', $request->variable('ukrgb_fb_secret', ''));
						$config->set('ukrgb_fb_page_mgr', $request->variable('ukrgb_fb_page_mgr', ''));
						$config->set('ukrgb_fb_page_id', $request->variable('ukrgb_fb_page_id', ''));
						$config->set('ukrgb_fb_subforums', $request->variable('ukrgb_fb_subforums', ''));
						$config->set('ukrgb_fb_auto_post', $request->variable('ukrgb_fb_auto_post', 0));
						$config->set('ukrgb_delayed_action_gc', $request->variable('ukrgb_delayed_action_gc', 300));
						
						trigger_error($language->lang('ACP_UKRGB_CORE_SETTING_SAVED') . adm_back_link($this->u_action));
					}
				}
                /** @var \phpbb\controller\helper $helper Controller helper object */
				$helper = $phpbb_container->get('controller.helper');
				$ukrgbFacebook = new \ukrgb\core\controller\facebook_controller($config, $request, $user, $helper, $phpbb_admin_path, $phpEx);
				$tokenData = $ukrgbFacebook->getTokenMetaData();
				
				$appVars = array(
						'UKRGB_FB_APPID'		=> $config['ukrgb_fb_appid'],
						'UKRGB_FB_SECRET'		=> $config['ukrgb_fb_secret'],
						'UKRGB_FB_PAGE_MGR'		=> $config['ukrgb_fb_page_mgr'],
						'UKRGB_FB_PAGE_ID'		=> $config['ukrgb_fb_page_id'],
						'UKRGB_FB_SUBFORUMS'	=> $config['ukrgb_fb_subforums'],
						'UKRGB_FB_AUTO_POST'	=> $config['ukrgb_fb_auto_post'],
						'UKRGB_CRON_FREQUENCY'  => $config['ukrgb_delayed_action_gc'],
						'UKRGB_FBPT_APP_ID'     => $tokenData['app_id'],
						'UKRGB_FBPT_APP_NAME'   => $tokenData['app_name'],
						'UKRGB_FBPT_EXPIRES'    => $tokenData['expires_at'],
						'UKRGB_FBPT_VALID'      => $tokenData['valid'],
						'UKRGB_FBPT_ISSUED'     => $tokenData['issued'],
						'UKRGB_FBPT_SCOPE'      => $tokenData['scope'],
						'UKRGB_FBPT_ERROR'		=> $tokenData['error'],
				);
						
				if (!empty(group_memberships(array($config['ukrgb_fb_page_mgr']), $user->data['user_id'])))
				{			
					$pageTokenVars = array(
							'U_UKRGB_GET_FB_TOKEN'   => $ukrgbFacebook->getRequestPermisionsUrl(),
							'UKRGB_FB_TOKEN_REFRESH' => true,			
					);
					$template->assign_vars(array_merge($commonVars, $appVars, $pageTokenVars));						
				}
				else
				{
					$template->assign_vars(array_merge($commonVars, $appVars));
				}
				
				break;
			case 'image_settings':
			    $orphans = 0;
                if ($submit) {
                    if (!check_form_key('ukrgb/core')) {
                        trigger_error('FORM_INVALID');
                    } else {
                        $config->set('ukrgb_image_aws_region',   $request->variable('ukrgb_image_aws_region', ''));
                        $config->set('ukrgb_image_aws_key',   $request->variable('ukrgb_image_aws_key', ''));
                        $config->set('ukrgb_image_aws_secret',   $request->variable('ukrgb_image_aws_secret', ''));
                        $config->set('ukrgb_image_ses_queue_url',   $request->variable('ukrgb_image_ses_queue_url', ''));
                        $config->set('ukrgb_image_sqs_enabled',   $request->variable('ukrgb_image_sqs_enabled', 0));
                        $config->set('ukrgb_image_s3_bucket',   $request->variable('ukrgb_image_s3_bucket', ''));
                        $config->set('ukrgb_image_s3_prefix',   $request->variable('ukrgb_image_s3_prefix', ''));
                        $config->set('ukrgb_cleanup_gc', $request->variable('ukrgb_cleanup_gc', 300));

                        trigger_error($language->lang('ACP_UKRGB_CORE_SETTING_SAVED') . adm_back_link($this->u_action));
                    }
                }
                if ($submit_orphan) {
                    $table = $table_prefix . 'ukrgb_images';
                    try {
                    $orphan_images = new \ukrgb\core\model\image_orphan(
                        new \ukrgb\core\utils\aws_s3(
                            $config['ukrgb_image_aws_region'],
                            $config['ukrgb_image_aws_key'],
                            $config['ukrgb_image_aws_secret']),
                        $config, $db, $table);
                    $orphans = $orphan_images->find_orphan_images();
                    } catch (\Aws\S3\Exception\S3Exception $e) {
                        trigger_error($e->getMessage());
                    }

                }

                $template->assign_vars(array_merge($commonVars, array(
                    'UKRGB_IMAGE_AWS_REGION'		=> $config['ukrgb_image_aws_region'],
                    'UKRGB_IMAGE_AWS_KEY'		    => $config['ukrgb_image_aws_key'],
                    'UKRGB_IMAGE_AWS_SECRET'		=> $config['ukrgb_image_aws_secret'],
                    'UKRGB_IMAGE_SES_QUEUE'		    => $config['ukrgb_image_ses_queue_url'],
                    'UKRGB_IMAGE_SQS_ENABLED'		=> $config['ukrgb_image_sqs_enabled'],
                    'UKRGB_IMAGE_S3_PREFIX' 		=> $config['ukrgb_image_s3_prefix'],
                    'UKRGB_IMAGE_S3_BUCKET' 		=> $config['ukrgb_image_s3_bucket'],
                    'UKRGB_CRON_FREQ_CLEANUP'       => $config['ukrgb_cleanup_gc'],
                    'UKRGB_ORPHAN_IMAGE_COUNT'      => $orphans['orphan_count'],
                    'UKRGB_ORPHAN_INVALID_COUNT'    => $orphans['invalid_count'],
                )));
                break;
			//end case
		}
	}
}
