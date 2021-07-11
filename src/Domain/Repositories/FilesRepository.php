<?php

namespace Integracao\Domain\Repositories;

interface FilesRepository
{
    public function fetch();
    public function download(string $fullpath, string $destiny);
}
