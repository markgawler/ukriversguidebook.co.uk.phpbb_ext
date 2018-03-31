<?php
/**
 * Created by PhpStorm.
 * User: mrfg
 * Date: 07/01/18
 * Time: 16:52
 */


namespace ukrgb\core\tests\migrations;


class image_model_test extends \phpbb_database_test_case
{
    /** @var \phpbb\db\tools\tools */
    protected $db_tools;

    /** @var string */
    protected $table_prefix;

    /** @var \ukrgb\core\model\image */
    protected $image;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    static protected function setup_extensions()
    {
        return array('ukrgb/core');
    }

    public function getDataSet()
    {
        return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/image_model.xml');
    }

    public function setUp()
    {
        parent::setUp();

        global $table_prefix;

        $this->table_prefix = $table_prefix;
        $this->db = $this->new_dbal();
        $this->db_tools = new \phpbb\db\tools\tools($this->db);
    }

    public function test_constructor_test()
    {
        $file_key = '1111111111111-1554';
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', $file_key);
        $image->store_data();
        $img_data = $image->get_all_image_data();

        $this->assertTrue($img_data['forum_id'] === 0, 'Asserting that image data is "forum_id: 0"');
        $this->assertTrue($img_data['topic_id'] === 0, 'Asserting that image data is "topic_id: 0 "');
        $this->assertTrue($img_data['post_id'] === 0, 'Asserting that image data is "post_id: 0 "');
        $this->assertTrue($img_data['poster_id'] === 0, 'Asserting that image data is "poster_id: 0"');
        $this->assertTrue($img_data['upload_time'] === 0, 'Asserting that image data is "upload_time: 0"');
    }

    public function test_constructor_test_forum()
    {
        $file_key = '1111111111111-1664';
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', $file_key,77, 123456, 654321);
        $image->store_data();
        $img_data = $image->get_all_image_data();

        $this->assertTrue($img_data['forum_id'] === 77, 'Asserting that image data is "forum_id: 77"');
        $this->assertTrue($img_data['topic_id'] === 123456, 'Asserting that image data is "topic_id: 123456 "');
        $this->assertTrue($img_data['post_id'] === 654321, 'Asserting that image data is "post_id: 654321 "');
        $this->assertTrue($img_data['poster_id'] === 0, 'Asserting that image data is "poster_id: 0"');
        $this->assertTrue($img_data['upload_time'] === 0, 'Asserting that image data is "upload_time: 0"');
    }
    public function test_constructor_test_upload()
    {
        $file_key = '1111111111111-1774';
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', $file_key,null, null, null, 33333333, 46);
        $image->store_data();
        $img_data = $image->get_all_image_data();

        $this->assertTrue($img_data['forum_id'] === 0, 'Asserting that image data is "forum_id: 0"');
        $this->assertTrue($img_data['topic_id'] === 0, 'Asserting that image data is "topic_id: 0 "');
        $this->assertTrue($img_data['post_id'] === 0, 'Asserting that image data is "post_id: 0 "');
        $this->assertTrue($img_data['poster_id'] === 46, 'Asserting that image data is "poster_id: 46"');
        $this->assertTrue($img_data['upload_time'] === 33333333, 'Asserting that image data is "upload_time: 33333333"');

        $file_key = '1111111111111-1775';
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', $file_key,0, 0, 0, 33333333, 46);
        $image->store_data();
        $this->assertFalse($image->is_in_post(),'Asserting that is_in_post is false');
    }


    public function test_get_image_data()
    {
        $file_key = '1515583485123-1554';
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', $file_key);

        $img_data = $image->get_upload_data();
        $this->assertTrue($img_data['poster_id'] === 1234, 'Asserting that image data is "poster_id: 1234"');
        $this->assertTrue($img_data['upload_time'] === 1515404083, 'Asserting that image data is "upload_time: 1515404083"');
        $this->assertTrue($img_data['id'] === 1, 'Asserting that image data is "id: 1"');


        $file_key = $file_key = '1515583485999-1345';
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', $file_key);
        $img_data = $image->get_forum_data();

        $this->assertTrue($img_data['post_id'] === 8765432, 'Asserting that image data is "post_id: 8765432"');
        $this->assertTrue($img_data['topic_id'] === 23456, 'Asserting that image data is "topic_id: 23456"');
        $this->assertTrue($img_data['forum_id'] === 43, 'Asserting that image data is "forum_id: 43"');


        // Insert
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', '1515583485999-8888',null ,null ,null ,1515583485923,99);
        $image->store_upload_data();

        $img_data = $image->get_all_image_data();
        $this->assertTrue($img_data['poster_id'] === 99, 'Asserting that image data is "poster_id: 99"');
        $this->assertTrue($img_data['upload_time'] === 1515583485923, 'Asserting that image data is "upload_time: 1515583485923"');

    }


    public function test_insert_new_upload_data()
    {
        // Insert
        $file_key = '1515583485999-7777';
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', $file_key, 0,0,0,15155834859777, 77);

        $image->store_upload_data();

        $img_data = $image->get_all_image_data();
        $this->assertTrue($img_data['poster_id'] === 77, 'Asserting that image data is "poster_id: 77"');
        $this->assertTrue($img_data['upload_time'] === 15155834859777, 'Asserting that image data is "upload_time: 1515583485923"');


        // Insert new reusing object
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', '1515583485999-8888',null,null,null,1515583485923,99);
        $image->store_upload_data();

        $img_data = $image->get_all_image_data();
        $this->assertTrue($img_data['poster_id'] === 99, 'Asserting that image data is "poster_id: 99"');
        $this->assertTrue($img_data['upload_time'] === 1515583485923, 'Asserting that image data is "upload_time: 1515583485923"');

    }

    public function test_insert_new_forum_data()
    {
        $file_key = '1515583485999-1111';
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', $file_key, 12,121212,2121212121);

        $image->store_forum_data();

        $img_data = $image->get_all_image_data();
        $this->assertTrue($img_data['forum_id'] === 12, 'Asserting that image data is "forum_id: 12"');
        $this->assertTrue($img_data['topic_id'] === 121212, 'Asserting that image data is "topic_id: 121212 "');
        $this->assertTrue($img_data['post_id'] === 2121212121, 'Asserting that image data is "post_id: 2121212121 "');


        // Insert new reusing object
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', '1515583485999-2222',23, 232323, 3434343434);
        $image->store_forum_data();

        $img_data = $image->get_all_image_data();
        $this->assertTrue($img_data['forum_id'] === 23, 'Asserting that image data is "forum_id: 23"');
        $this->assertTrue($img_data['topic_id'] === 232323, 'Asserting that image data is "topic_id: 232323 "');
        $this->assertTrue($img_data['post_id'] === 3434343434, 'Asserting that image data is "post_id: 3434343434 "');

    }

    public function test_update_with_forum_data ()
    {
        $file_key = '1515583485123-1554';
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', $file_key);
        $image->update_and_store_forum_data(44, 5555555, 45454545);
        $img_data = $image->get_all_image_data();


        $this->assertTrue($img_data['forum_id'] == 44, 'Asserting that image data is "forum_id: 44"');
        $this->assertTrue($img_data['topic_id'] == 5555555, 'Asserting that image data is "topic_id: 5555555 "');
        $this->assertTrue($img_data['post_id'] == 45454545, 'Asserting that image data is "post_id: 45454545 "');
        $this->assertTrue($img_data['poster_id'] === 1234, 'Asserting that image data is "poster_id: 1234"');
        $this->assertTrue($img_data['upload_time'] === 1515404083, 'Asserting that image data is "upload_time: 1515404083"');
    }

    public function test_update_with_upload_data ()
    {
        $file_key = '1515583999888-5555';
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', $file_key);

        //$img_data = $image->get_all_image_data();
        $image->update_and_store_upload_data(4321, 1515406683);
        $img_data = $image->get_all_image_data();


        $this->assertTrue($img_data['forum_id'] == 43, 'Asserting that image data is "forum_id: 43"');
        $this->assertTrue($img_data['topic_id'] == 23456, 'Asserting that image data is "topic_id: 5555555 "');
        $this->assertTrue($img_data['post_id'] ==  8765432, 'Asserting that image data is "post_id: 45454545 "');
        $this->assertTrue($img_data['poster_id'] === 4321, 'Asserting that image data is "poster_id: 4321"');
        $this->assertTrue($img_data['upload_time'] === 1515406683, 'Asserting that image data is "upload_time: 1515406683"');
    }


    public function test_is_in_post()
    {
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', '1515583485999-1345');
        //var_dump($image->get_all_image_data());

        $this->assertTrue($image->is_in_post(), 'Asserting that 1515583485999-1345 has in post set');

        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', '1515583485199-1559');
        $this->assertFalse($image->is_in_post(), 'Asserting that 1515583485199-1559 dose NOT have in_post set');

        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', '0000000000000-1559');
        $this->assertFalse($image->is_in_post(), 'Asserting that none existent image returns false');
    }

    public function test_is_new_image()
    {
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', '1515583485123-1554');
        $this->assertFalse($image->is_new_image(), 'Asserting that 1515583485123-1554 is_new_image is false');

        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', '0000000000000-1559');
        $this->assertTrue($image->is_new_image(), 'Asserting that none existent image is_new_image is true');
    }


    public function test_delete_image_row()
    {
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', '1515583999888-6666');
        $this->assertFalse($image->is_new_image(),'Asserting that this is NOT a new image');
        $image->delete();
        unset($image);
        $image = new \ukrgb\core\model\image($this->db, $this->table_prefix . 'ukrgb_images', '1515583999888-6666');
        $this->assertTrue($image->is_new_image(),'Asserting that this is a new image');

    }

}