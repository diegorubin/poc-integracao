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
            'Key' => $key,
            'Body' => fopen($file, 'r'),
            'ACL' => 'public-read'
        ]);
    }
}
