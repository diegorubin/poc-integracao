<?php

namespace Integracao\Domain\Repositories;

use Integracao\Domain\File;

interface FilesReadRepository
{
    public function put(File $file): void;
    public function exists(File $file): bool;
}
