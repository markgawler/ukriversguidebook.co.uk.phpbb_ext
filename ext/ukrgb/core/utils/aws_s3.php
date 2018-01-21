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
class aws_s3
{
    /** @var  \Aws\S3\S3Client */
    protected $s3;

    /**
     * AWS S3 Utility class constructor.
     *
     * @param string $region
     * @param string $aws_key
     * @param string $aws_secret
     *
     */
    public function __construct(
        $region,
        $aws_key,
        $aws_secret)
    {
        $this->s3 = new \Aws\S3\S3Client([
            'version' => '2006-03-01',
            'region' => $region,
            'credentials' => [
                'key' => $aws_key,
                'secret' => $aws_secret]
        ]);
    }

    public function get_iterator($prefix,$bucket) {
        $objects = $this->s3->getIterator('ListObjects', array(
            "Bucket" => $bucket,
            "Prefix" => $prefix . '/'
        ));
        return $objects;
    }


}