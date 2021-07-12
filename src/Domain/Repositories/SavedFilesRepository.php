<?php

namespace Integracao\Domain\Repositories;

interface SavedFilesRepository
{
    public function save($file, $bucket, $key);
    public function load($bucket, $key, $destiny);
}
