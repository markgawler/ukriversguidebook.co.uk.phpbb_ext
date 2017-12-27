<?php
/**
 * Created by PhpStorm.
 * User: mrfg
 * Date: 23/12/17
 * Time: 16:10
 */


namespace ukrgb\core\model;

require_once __DIR__ . '/../vendor/autoload.php';

class image_client
{
    /**
     * @var string URL of AWS SQS queue
     */
    protected $queueUrl;

    /**
     * @var \Aws\Sqs\SqsClient
     */
    protected $client;

    /**
     * @var string ukrgb image table
     */
    protected $ukrgb_images_table;

    /**
     * @var \ukrgb\core\model\image  image model
     */
    protected $ukrgbImage;

    /**
     * @var \phpbb\config\config
     */
    protected $config;

    /**
     * @var \phpbb\db\driver\driver_interface
     */
    protected $db;

    /**
     * image_client constructor.
     * @param \phpbb\config\config $config
     * @param \phpbb\db\driver\driver_interface $db
     * @param string $ukrgb_images_table
     */
    public function __construct(
        \phpbb\config\config $config,
    	\phpbb\db\driver\driver_interface $db,
        $ukrgb_images_table
        )
    {
        $this->config = $config;
        $this->db = $db;
        $this->ukrgb_images_table = $ukrgb_images_table;
        $this->queueUrl = $config['ukrgb_image_ses_queue_url'];
        $this->client = new \Aws\Sqs\SqsClient(['region' => $config['ukrgb_image_aws_region'],
            'version' => 'latest',
            'credentials' =>
                ['key' => $config['ukrgb_image_aws_key'],
                    'secret' => $config['ukrgb_image_aws_secret']]]);
    }

    public function get_message()
    {
        try
        {
            $result = $this->client->receiveMessage(array(
                'AttributeNames' => ['SentTimestamp'],
                'MaxNumberOfMessages' => 1,
                'MessageAttributeNames' => ['All'],
                'QueueUrl' => $this->queueUrl, // REQUIRED
                'WaitTimeSeconds' => 0,
            ));
            if (count($result->get('Messages')) > 0)
            {
                $message = $result->get('Messages')[0];
                $sentTime = json_decode($message['Attributes']['SentTimestamp']);
                $s3 = json_decode($message['Body'])->Records[0]->s3;

                /*        $result = $client->deleteMessage([
                    'QueueUrl' => $this->>queueUrl, // REQUIRED
                    'ReceiptHandle' => $result->get('Messages')[0]['ReceiptHandle'] // REQUIRED
                ]);*/
                return array(
                    'bucketName' =>  $s3->bucket->name,
                    'objectKey' => $s3->object->key,
                    'sentTime' => $sentTime);
            }
            else {
                return array(); // "No messages in queue. \n";
            }
        } catch (\Aws\Exception\AwsException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    protected function initImage()
    {
        if (empty($this->ukrgbImage)) {
            $this->ukrgbImage = new \ukrgb\core\model\image(
                $this->db,
                $this->ukrgb_images_table,
                0,
                0,
                0);
        }
    }

    public function runTask()
    {
        do {
            $message = $this->get_message();
            if ($message) {
                $this->initImage();
                $key = explode('/', $message['objectKey']);
                $user_id = $key[1];
                $file_key = substr($key[2], 0, strpos($key[2], "."));
                $image_data = $this->ukrgbImage->get_image_data($file_key);
                if (! $image_data) {
                    $this->ukrgbImage->insert_image_data($file_key, $user_id, false);
                }
            }
        } while ($message);


    }
}