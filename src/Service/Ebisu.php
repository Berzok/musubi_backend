<?php

namespace App\Service;

use App\Entity\Paquet;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class Ebisu {

    private S3Client $client;
    private string $endpoint;
    private string $key;
    private string $name;
    private string $secret;

    public function __construct(string $endpoint, string $name, string $key, string $secret) {
        $this->endpoint = $endpoint;
        $this->name = $name;
        $this->key = $key;
        $this->secret = $secret;
    }

    private function connect(): void {
        $this->client = new S3Client([
            'version' => 'latest',
            'region' => 'ams3',
            'endpoint' => $this->endpoint,
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret,
            ],
            'http' => [
                'verify' => false
            ]
        ]);
    }

    /**
     * @throws Exception
     */
    private function disconnect(): void {
        try {
            unset($this->client);
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * Delete a resource from the bucket it is stored in
     * @param Resource $resource
     * @return bool
     * @throws Exception
     */
    public function delete(Paquet $paquet): bool {
        $this->connect();
        try {
            $this->client->deleteObject([
                'Bucket' => $this->name,
                'Key' => $paquet->getSlug(),
            ]);
        } catch (S3Exception $e) {
            var_dump($e);
        }

        $this->disconnect();
        return true;
    }

    /**
     * @param UploadedFile $file
     * @param string $slug
     * @return void
     * @throws Exception
     */
    public function upload(UploadedFile $file, string $slug): void {
        $this->connect();
        try {
            $this->client->putObject([
                'Bucket' => $this->name,
                'Key' => $slug,
                'Body' => $file->getContent(), //The contents of the file
                'ACL' => 'private',
                'ContentType' => $file->getMimeType(),
                'Metadata' => array(
                    'x-amz-meta-my-key' => 'your-value'
                )
            ]);
        } catch (S3Exception $e) {
            var_dump($e);
        }
        $this->disconnect();
    }

    /**
     * Create a presigned URL for a given item in the Bucket
     * @param string $key - The key of the item
     * @param int $lifetime - Duration in minutes for which the created URL is valid. Evaluated using strtotime(), default to 5min
     * @return string - Presigned URL to $key valid for $lifetime
     */
    public function createUrl(string $key, int $lifetime = 5): string {
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => $this->name,
            'Key' => $key
        ]);

        $request = $this->client->createPresignedRequest($cmd, '+' . $lifetime . ' minutes');
        return (string)$request->getUri();
    }

    public function getAll(): array {
        $objects = $this->client->listObjects([
            'Bucket' => $this->name
        ]);

        $data = [];
        foreach ($objects['Contents'] as $obj) {
            $cmd = $this->client->getCommand('GetObject', [
                'Bucket' => $this->name,
                'Key' => $obj['Key']
            ]);

            $request = $this->client->createPresignedRequest($cmd, '+25 minutes');
            $presignedUrl = (string)$request->getUri();

            $obj['url'] = $presignedUrl;
            $data[] = $obj;
        }

        return $data;
    }
}