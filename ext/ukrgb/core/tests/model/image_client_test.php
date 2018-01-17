<?php
/**
 * User: mrfg
*/

namespace ukrgb\core\model\tests\image_client;

class main_test extends \phpbb_test_case
{
    /** @var \ukrgb\core\model\image */
    protected $image;
    protected $config;
    protected $db;

    /** @var \ukrgb\core\utils\aws_sqs */
    protected $aws_sqs;


    protected $aws_sqs_util;

    public function setUp()
    {
        parent::setUp();

        $this->config = $this->getMockBuilder('\phpbb\config\config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->db = $this->getMockBuilder('\phpbb\db\driver\driver_interface')
            ->disableOriginalConstructor()
            ->getMock();


        $this->aws_sqs_util = $this->getMockBuilder( \ukrgb\core\utils\aws_sqs::class)
            ->disableOriginalConstructor()
            ->getMock();

    }

    public function test_handle_get_message()
    {
        $body = file_get_contents(dirname(__FILE__) . '/fixtures/body.json');
        $message = array(
            'Body' => $body,
            'Attributes' => array('SentTimestamp' => '1515753119227')
        );

        $aws_result = $this->getMockBuilder(\Aws\Result::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->aws_sqs_util->method('receive_message')
            ->willReturn($aws_result);

        $aws_result->method('get')
            ->will($this->onConsecutiveCalls(
                array(0 => ''),
                array(0 =>$message),
                null));

        $aws_result->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo('Messages'));

        $client = new \ukrgb\core\model\image_client($this->config, $this->db, 'dummy_table', $this->aws_sqs_util);

        $result = $client->get_message();

        $this->assertTrue($result['bucketName'] === 'xxx.ukriversguidebook.co.uk', 'Assert bucket name');
        $this->assertTrue($result['objectKey'] === 'uploads/74759/1515753121410-4956.png', 'Assert');
        $this->assertTrue($result['sentTime'] === (int) 1515753119227 / 1000, 'Assert ');

    }


    public function test_handle_run_task()
    {
        $body = file_get_contents(dirname(__FILE__) . '/fixtures/body.json');
        $message = array(
            'Body' => $body,
            'Attributes' => array('SentTimestamp' => '1515753119227')
        );

        $aws_result = $this->getMockBuilder(\Aws\Result::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->aws_sqs_util->method('receive_message')
            ->willReturn($aws_result);

        $aws_result->method('get')
            ->will($this->onConsecutiveCalls(
                array(0 => ''),
                array(0 =>$message),
                null));

        $aws_result->expects($this->exactly(3))
            ->method('get')
            ->with($this->equalTo('Messages'));

        $client = new \ukrgb\core\model\image_client($this->config, $this->db, 'dummy_table', $this->aws_sqs_util);

        $client->runTask();
    }
}
