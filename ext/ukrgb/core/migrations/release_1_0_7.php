<?php
/**
*
* @package phpBB Extension - UKRGB Core phpBB Extension
* @copyright (c) 2017 Mark Gawler
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\core\migrations;
/**
 * Class release_1_0_7
 * @package ukrgb\core\migrations
 */
class release_1_0_7 extends \phpbb\db\migration\migration
{
	/**
	* Assign migration file dependencies for this migration
	*
	* @return array Array of migration files
	* @static
	* @access public
	*/
	static public function depends_on()
	{		
		return array('\ukrgb\core\migrations\release_1_0_6');
	}


    public function update_data()
    {
        return array(

            array('config.add', array('ukrgb_image_aws_region', 'eu-west-1')),
            array('config.add', array('ukrgb_image_aws_key', '')),
            array('config.add', array('ukrgb_image_aws_secret', '')),
            array('config.add', array('ukrgb_image_ses_queue_url', '')),

            array('module.remove', array(
                'acp',
                'ACP_UKRGB_CORE_TITLE',
                array(
                    'module_basename'       => '\ukrgb\core\acp\main_module',
                    'modes'                 => array('settings', 'fb_app_settings'),
                ),
            )),

            array('module.add', array(
                'acp',
                'ACP_UKRGB_CORE_TITLE',
                array(
                    'module_basename'	=> '\ukrgb\core\acp\main_module',
                    'modes'				=> array('settings','fb_app_settings','image_settings'),
                ),
            )),

        );
    }
	
}
