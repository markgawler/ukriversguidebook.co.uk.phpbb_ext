<?php
/**
 * Created by PhpStorm.
 * User: mrfg
 * Date: 07/01/18
 * Time: 16:52
 */


namespace ukrgb\core\tests\migrations;


class image_table_test extends \phpbb_database_test_case
{
    /** @var \phpbb\db\tools\tools */
    protected $db_tools;

    /** @var string */
    protected $table_prefix;

    static protected function setup_extensions()
    {
        return array('ukrgb/core');
    }

    public function getDataSet()
    {
        return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/add_image_table.xml');
    }

    public function setUp()
    {
        parent::setUp();

        global $table_prefix;

        $this->table_prefix = $table_prefix;
        $db = $this->new_dbal();
        $this->db_tools = new \phpbb\db\tools\tools($db);
    }

    public function test_image_table_columns()
    {
        $colums = array('file_key','poster_id','upload_time', 'post_id', 'topic_id', 'forum_id');
        foreach ($colums as $c) {
            $this->assertTrue($this->db_tools->sql_column_exists($this->table_prefix . 'ukrgb_images', $c), 'Asserting that column "' . $c . '" exists');
        }
    }
}
