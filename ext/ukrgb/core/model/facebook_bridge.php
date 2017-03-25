<?php
/**
 *
 * UKRGB Core extension facebook / phpbb bridge.
 *
 * @copyright (c) Mark Gawler 2017
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace ukrgb\core\model;

class facebook_bridge
{
	protected $fb;
	
	/**
	 * phpBB config
	 *
	 * @var \phpbb\config\config
	 */
	protected $config;

	/**
	 * Database driver
	 *
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;
	
	protected $ukrgb_fb_posts_table;
	
	/**
	 * Constructor for Facebook phpBB bridge
	 * 
	 * @param	\phpbb\config\config				$config
	 */
	public function __construct(
			\phpbb\config\config $config,
			\phpbb\db\driver\driver_interface $db,
			$ukrgb_fb_posts_table)
	{
		$this->config = $config;
		$this->db = $db;
		$this->ukrgb_fb_posts_table = $ukrgb_fb_posts_table;
		$appId = $config['ukrgb_fb_appid'];
		$appSecret = $config['ukrgb_fb_secret'];
		if (!empty($appId) && !empty($appSecret)){
			$this->fb = new facebook($appId, $appSecret);
		}
	}

	public function post($message, $forumId, $topicId, $postId)
	{
		$link = generate_board_url(false) . '/viewtopic.php?f=' . $forumId .'&t=' . $topicId;
		
		$postData  = [
				'message' => $message,
				'link' => $link,
		];
		
		//try {
		$response = $this->fb->post($postData, $this->config['ukrgb_fb_page_token'],$this->config['ukrgb_fb_page_id']);
		//} catch(Facebook\Exceptions\FacebookResponseException $e) {
		//	echo 'Graph returned an error: '.$e->getMessage();
			//	exit;
			//} catch(Facebook\Exceptions\FacebookSDKException $e) {
			//	echo 'Facebook SDK returned an error: '.$e->getMessage();
			//	exit;
			//}
		$graphNode = $response->getGraphNode();
		
		$this->store_graph_node($graphNode['id'], $postId, $topicId);
	}
	

	public function delete_post($postIds)
	{
		$token = $this->config['ukrgb_fb_page_token'];
		foreach ($postIds as $post) {
			$node = $this->get_graph_node($post);
			if ( ! empty($node)) {
				$this->fb->deletePost($node, $token);
				$this->delete_graph_node($post);
			}
		}
	}
	
	protected function store_graph_node($graphNode, $postId, $topicId)
	{
		$submit_data = array (
				'post_id' => $postId,
				'topic_id' => $topicId,
				'graph_node' => $graphNode
		);
		
		$sql = 'INSERT INTO ' . $this->ukrgb_fb_posts_table . '
			' . $this->db->sql_build_array('INSERT', $submit_data);
		$this->db->sql_query($sql);
	}
	
	protected function get_graph_node($postId)
	{
		$select_data = array ('post_id' => $postId,);
		
		$sql = 'SELECT graph_node,topic_id FROM '. $this->ukrgb_fb_posts_table .' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		return $row['graph_node'];
	}
	
	protected function delete_graph_node($postId)
	{
		$select_data = array ('post_id' => $postId,);
		$sql = 'DELETE FROM ' . $this->ukrgb_fb_posts_table .' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
		$this->db->sql_query($sql);
	
	}
	
	
	
	
	
}