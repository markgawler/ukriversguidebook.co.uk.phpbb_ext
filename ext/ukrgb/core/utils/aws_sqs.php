<?php
/**
 * Created by PhpStorm.
 * User: mrfg
 * Date: 13/01/18
 * Time: 21:17
 */

namespace ukrgb\core\utils;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Class aws_sqs
 * @package ukrgb\core\utils
 */
class aws_sqs
{
    /** @var string */
    protected $queue_url;

    /** @var  \Aws\Sqs\SqsClient */
    protected $client;

    /**
     * AWS SQS Utility class constructor.
     *
     * @param string $queue_url
     * @param string $region
     * @param string $aws_key
     * @param string $aws_secret
     *
     */
    public function __construct(
        $queue_url,
        $region,
        $aws_key,
        $aws_secret)
    {
        $this->queue_url = $queue_url;
        $this->client = new \Aws\Sqs\SqsClient(['region' => $region,
            'version' => 'latest',
            'credentials' =>  ['key' => $aws_key, 'secret' => $aws_secret]]);
    }

    public function receive_message ()
    {
        return $this->client->receiveMessage(array(
            'AttributeNames' => ['SentTimestamp'],
            'MaxNumberOfMessages' => 1,
            'MessageAttributeNames' => ['All'],
            'QueueUrl' => $this->queue_url,
            'WaitTimeSeconds' => 0,
        ));
    }

    public function delete_message ($receipt_handle)
    {
        return $this->client->deleteMessage([
            'QueueUrl' => $this->queue_url,
            'ReceiptHandle' => $receipt_handle
        ]);
    }
}