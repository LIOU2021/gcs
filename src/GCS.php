<?php

namespace Liou2021\Gcs;

use Exception;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GCS
{
    private static string $signedUrlTime = "1 day"; //"1 minutes"

    /**
     * buckey name
     * 
     * @var string
     */
    private string $bucket;

    /**
     * GCS key file path
     * 
     * @var string
     */
    private string $key;

    /**
     * GCS instance
     * 
     * @var \Google\Cloud\Storage\StorageClient
     */
    private \Google\Cloud\Storage\StorageClient $connect;

    public function __construct()
    {
        $this->bucket = config('filesystems.gcs.bucket');
        $this->key = config('filesystems.gcs.key');
        $this->connect = $this->connect();
    }

    /**
     * verify GCS
     */
    private function connect(): \Google\Cloud\Storage\StorageClient
    {
        $storage = new StorageClient([
            'keyFilePath' => $this->key
        ]);
        return $storage;
    }

    private function exceptionLog(Exception $e): bool
    {
        $result = [
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'message' => $e->getMessage(),
        ];
        Log::error(__FILE__ . "@" . __LINE__, $result);
        return false;
    }

    public static function getSignedUrlTime(): string
    {
        return self::$signedUrlTime;
    }

    public static function signedUrlTimeConvert(): int
    {
        return strtotime(self::getSignedUrlTime()) - time();
    }

    /**
     * upload file to GCS
     * 
     * @param string $path example : "test/he.csv"
     */
    public function put(\Illuminate\Http\UploadedFile $file, string $path = ''): bool
    {
        try {

            $fileName = $path;

            if ($path == '') {
                $fileName = $file->getClientOriginalName();
            }

            $storage = $this->connect;

            $bucket = $storage->bucket($this->bucket);

            $bucket->upload(
                file_get_contents($file->getRealPath()),
                [
                    'name' => $fileName
                ]
            );

            return true;
        } catch (Exception $e) {
            return $this->exceptionLog($e);
        }
    }

    /**
     * get url from GCS
     * 
     * @param string $file example : "test/hello.txt"
     */
    public function url(string $file): string
    {
        return "https://storage.googleapis.com/" . $this->bucket . "/" . $file;
    }

    /**
     * List Cloud Storage bucket objects.
     *
     */
    public function allFiles(): array|bool
    {
        try {
            $result = [];

            $storage = $this->connect;
            $bucket = $storage->bucket($this->bucket);
            foreach ($bucket->objects() as $object) {
                $result[] = $object->name();
            }

            return $result;
        } catch (Exception $e) {
            return $this->exceptionLog($e);
        }
    }

    /**
     * List Cloud Storage bucket objects with specified prefix.
     *
     * @param string $directoryPrefix the prefix to use in the list objects API call. example : "test/"
     */
    public function files(string $directoryPrefix): array|bool
    {
        try {
            $result = [];

            $storage = $this->connect;
            $bucket = $storage->bucket($this->bucket);
            $options = ['prefix' => $directoryPrefix];

            foreach ($bucket->objects($options) as $object) {
                $result[] = $object->name();
            }

            return $result;
        } catch (Exception $e) {
            return $this->exceptionLog($e);
        }
    }

    /**
     * Delete an object.
     * 
     * @param string $objectName example : "test/he2.csv"
     */
    public function delete(string $objectName): bool
    {
        try {
            $storage = $this->connect;
            $bucket = $storage->bucket($this->bucket);
            $object = $bucket->object($objectName);
            $object->delete();
            return true;
        } catch (Exception $e) {
            return $this->exceptionLog($e);
        }
    }

    /**
     * check file if exists Cloud Storage bucket 
     * 
     * @param string $file example : "test/hello.txt"
     */
    public function exists(string $file): bool
    {
        $url = $this->url($file);
        $status = Http::get($url)->status();
        $result = $status == 200 ? true : false;
        return $result;
    }

    /**
     * get signed url
     * 
     * @param string $objectName example : "test/myfile.jpg"
     */
    public function getSignedUrl(string $objectName): string|bool
    {
        try {
            $storage = $this->connect;
            $bucket = $storage->bucket($this->bucket);
            $object = $bucket->object($objectName);
            $url = $object->signedUrl(
                new \DateTime(self::$signedUrlTime),
                [
                    'version' => 'v4',
                ]
            );

            return $url;
        } catch (Exception $e) {
            return $this->exceptionLog($e);
        }
    }
}
