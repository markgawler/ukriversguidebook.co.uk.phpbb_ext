<?php
namespace ukrgb\core\facebook_storage;

use Facebook\PersistentData\PersistentDataInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class Storage implements PersistentDataInterface
{

	private $session;

	public function __construct()
	{
		$this->session = new Session();
	}

	public function get($key)
	{
		return $this->session->get('FBRLH_' . $key);
	}

	public function set($key, $value)
	{
		$this->session->set('FBRLH_' . $key, $value);
	}

}