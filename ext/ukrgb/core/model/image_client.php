<?php
/**
 * Created by PhpStorm.
 * User: mrfg
 * Date: 23/12/17
 * Time: 16:10
 */


namespace ukrgb\core\model;


/**
 * Class image_client
 * @package ukrgb\core\model
 */
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

    protected $util_aws_sqs;

    /**
     * image_client constructor.
     * @param \phpbb\config\config      $config
     * @param \phpbb\db\driver\driver_interface $db
     * @param string                    $ukrgb_images_table
     * @param \ukrgb\core\utils\aws_sqs $util_aws_sqs
     */
    public function __construct(
        \phpbb\config\config $config,
    	\phpbb\db\driver\driver_interface $db,
        $ukrgb_images_table,
        $util_aws_sqs
        )
    {
        $this->config = $config;
        $this->db = $db;
        $this->ukrgb_images_table = $ukrgb_images_table;
        $this->util_aws_sqs = $util_aws_sqs;

    }

    public function get_message()
    {
        try
        {
            $result = $this->util_aws_sqs->receive_message();
            if (!empty($result->get('Messages')))
            {
                $message = $result->get('Messages')[0];
                $sentTime = (int) json_decode($message['Attributes']['SentTimestamp'])/1000;
                $s3 = json_decode($message['Body'])->Records[0]->s3;
                $this->util_aws_sqs->delete_message($message['ReceiptHandle']);
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

    public function runTask()
    {
        do {
            $img_data = $this->get_message();
            if ($img_data) {
                $key = explode('/', $img_data['objectKey']);
                $sent_time = $img_data['sentTime'];
                $user_id = $key[1];
                $file_key = substr($key[2], 0, strpos($key[2], "."));

                $image = new \ukrgb\core\model\image($this->db, $this->ukrgb_images_table, $file_key);
                $image->update_and_store_upload_data($user_id, $sent_time);
            }
        } while ($img_data);
    }
}