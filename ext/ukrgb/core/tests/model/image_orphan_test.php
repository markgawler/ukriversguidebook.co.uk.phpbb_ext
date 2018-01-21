<?php
/**
 * Created by PhpStorm.
 * User: mrfg
 * Date: 20/01/18
 * Time: 18:35
 */

namespace ukrgb\core\model\tests\image_orphan;

class image_orphan_test extends \phpbb_database_test_case
{
    /** @var \phpbb\db\tools\tools */
    protected $db_tools;

    /** @var string */
    protected $table;

    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \ukrgb\core\utils\aws_s3 */
    protected $aws_s3_util;

    static protected function setup_extensions()
    {
        return array('ukrgb/core');
    }

    public function getDataSet()
    {
        return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/orphan_image.xml');
    }

    public function setUp()
    {
        parent::setUp();

        global $table_prefix;

        $this->table = $table_prefix . 'ukrgb_images';
        $this->db = $this->new_dbal();
        $this->db_tools = new \phpbb\db\tools\tools($this->db);

        $this->aws_s3_util = $this->getMockBuilder( \ukrgb\core\utils\aws_s3::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = array(
            'ukrgb_image_s3_prefix' => 'some_prefix',
            'ukrgb_image_s3_bucket' => 'some_bucket'
        );
    }

    public function test_find_orphan_images_new()
    {
        $time_stamp = time();
        $object = array(
            'Key' => 'some_prefix/9663/1516227349588-8853.png',
            'LastModified' => \Aws\Api\DateTimeResult::fromEpoch($time_stamp)
            );
        $objects = array(0 => $object);

        $this->aws_s3_util->expects($this->once())
            ->method('get_iterator')
            ->with('some_prefix', 'some_bucket')
            ->willReturn($objects);

        $oi = new \ukrgb\core\model\image_orphan($this->aws_s3_util, $this->config, $this->db, $this->table);
        $count = $oi->find_orphan_images();
        $this->assertTrue($count === 1, 'Asserting if count of orphans is zero');

        $image = new \ukrgb\core\model\image($this->db, $this->table, '1516227349588-8853');
        $img_data = $image->get_all_image_data();


        $this->assertTrue($img_data['poster_id'] === 9663, 'Asserting that image data is "poster_id: 9663"');
        $this->assertTrue($img_data['upload_time'] === $time_stamp, 'Asserting that image data is "upload_time: ' . $time_stamp);
    }

    public function test_find_orphan_images_existing()
    {
        $time_stamp = 1515404083;
        $object = array(
            'Key' => 'some_prefix/1234/1515583485123-1554.png',
            'LastModified' => \Aws\Api\DateTimeResult::fromEpoch($time_stamp)
        );
        $objects = array(0 => $object);

        $this->aws_s3_util->expects($this->once())
            ->method('get_iterator')
            ->with('some_prefix', 'some_bucket')
            ->willReturn($objects);

        $oi = new \ukrgb\core\model\image_orphan($this->aws_s3_util, $this->config, $this->db, $this->table);
        $count = $oi->find_orphan_images();
        $this->assertTrue($count === 0, 'Asserting if count of orphans is zero');

        $image = new \ukrgb\core\model\image($this->db, $this->table, '1515583485123-1554');
        $img_data = $image->get_all_image_data();
        $this->assertTrue($img_data['id'] == 22, 'Asserting that image ID is "id: 22"');
        $this->assertTrue($img_data['forum_id'] == 43, 'Asserting that image data is "forum_id: 43"');
        $this->assertTrue($img_data['topic_id'] == 23456, 'Asserting that image data is "topic_id: 5555555 "');
        $this->assertTrue($img_data['post_id'] ==  8765432, 'Asserting that image data is "post_id: 45454545 "');
        $this->assertTrue($img_data['in_post'] === 1, 'Asserting that image data is "in_post: True"');
        $this->assertTrue($img_data['poster_id'] === 1234, 'Asserting that image data is "poster_id: 1234"');
        $this->assertTrue($img_data['upload_time'] === $time_stamp, 'Asserting that image data is "upload_time: ' . $time_stamp);
    }
}