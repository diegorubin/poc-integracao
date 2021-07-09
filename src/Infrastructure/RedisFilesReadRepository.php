<?php

namespace Integracao\Infrastructure;

use Integracao\Configuration;
use Integracao\Domain\File;
use Integracao\Domain\Repositories\FilesReadRepository;

class RedisFilesReadRepository implements FilesReadRepository
{
    private $redis;
    private $config;

    public function __construct($redis)
    {
        $this->config = Configuration::getInstance()->get()['redis'];
        $this->redis = $redis;
        $this->redis->connect($this->config["host"], $this->config["port"]);
    }

    public function put(File $file): void
    {
        // TODO: check if is important put TTL in this cache
        $this->redis->set($this->key($file), json_encode($file->attributes()));
    }

    public function exists(File $file): bool
    {
        return $this->redis->exists($this->key($file));
    }

    private function key(File $file)
    {
        return $file->getSource().":".$file->getFullpath();
    }
}
