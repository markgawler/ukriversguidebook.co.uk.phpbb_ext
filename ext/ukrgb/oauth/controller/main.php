<?php
/**
 *
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

namespace ukrgb\oauth\controller;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\HttpFoundation\Response;

class main
{
	/* @var config */
	protected $config;

	/* @var helper */
	protected $helper;

	/* @var template */
	protected $template;

	/* @var user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param config $config
	 * @param helper $helper
	 * @param template $template
	 * @param user $user
	 */
	public function __construct(config $config, helper $helper, template $template, user $user)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	 * Demo controller for route /demo/{name}
	 *
	 * @param string $name
	 * @return Response A Symfony Response object
	 */
	public function handle($name)
	{
		if ($name === 'bertie')
		{
			return $this->helper->message('NO_AUTH_SPEAKING', array($name), 'NO_AUTH_OPERATION', 403);
		}

		$l_message = empty($this->config['acme_demo_goodbye']) ? 'DEMO_HELLO' : 'DEMO_GOODBYE';
		$this->template->assign_var('DEMO_MESSAGE', $this->user->lang($l_message, $name));

		return $this->helper->render('oauth_body.html', $name);
		
	}
}
