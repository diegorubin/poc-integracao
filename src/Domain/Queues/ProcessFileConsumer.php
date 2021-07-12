<?php

namespace Integracao\Domain\Queues;

use Integracao\Domain\File;

interface ProcessFileConsumer
{
    public function incoming(File $file);
}
