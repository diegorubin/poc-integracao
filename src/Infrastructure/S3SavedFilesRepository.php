<?php

namespace Integracao\Infrastructure;

use Aws\S3\S3Client;
use Integracao\Domain\Repositories\SavedFilesRepository;

class S3SavedFilesRepository implements SavedFilesRepository
{
    private $client;

    public function __construct(S3Client $client)
    {
        $this->client = $client;
    }

    public function save($file, $bucket, $key)
    {
        $this->client->putObject([
            'Bucket' => $bucket,
            'Key' => $this->normalizeKey($key),
            'Body' => fopen($file, 'r'),
            'ACL' => 'public-read'
        ]);
    }

    public function load($bucket, $key, $destiny)
    {
        $file = $this->client->getObject([
            'Bucket' => $bucket,
            'Key' => $this->normalizeKey($key),
        ]);
        file_put_contents($destiny, $file['Body']->getContents());
    }

    private function normalizeKey($key)
    {
        return str_replace("/", "-", $key);
    }
}
