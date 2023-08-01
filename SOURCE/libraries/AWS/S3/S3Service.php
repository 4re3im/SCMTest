<?php

require_once DIR_BASE . '/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class S3Service
{
    /**
     * @var S3Client
     */
    private $client;

    /**
     * @return S3Client
     */
    public function connection()
    {
        return $this->client;
    }

    public function setConnection($connection)
    {
        $this->client = $connection;
    }

    public function useGigyaDataFlowConnection()
    {
        $connection = new S3Client([
            'version' => 'latest',
            'region' => GIGYA_S3_REGION,
            'credentials' => [
                'key' => GIGYA_S3_KEY,
                'secret' => GIGYA_S3_SECRET
            ]
        ]);

        $this->setConnection($connection);
    }

    public function useCWSConnection()
    {
        $connection = new S3Client([
            'version' => 'latest',
            'region' => GIGYA_S3_REGION,
            'credentials' => [
                'key' => CWS_GIGYA_S3_KEY,
                'secret' => CWS_GIGYA_S3_SECRET
            ]
        ]);

        $this->setConnection($connection);
    }

    public function upload($bucket, $filename, $file, $acl = null)
    {
        try {
            $this->connection()
                ->putObject([
                    'Bucket' => $bucket,
                    'Key' => $filename,
                    'Body' => $file,
                    'ACL' => $acl
                ]);

            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function getObject($bucket, $filename)
    {
        try {
            return $this->connection()
                ->getObject([
                    'Bucket' => $bucket,
                    'Key' => $filename
                ]);
        } catch (S3Exception $e) {
            error_log($e);
            return false;
        }
    }

    public function getSignedUrl()
    {

    }
}
