<?php

namespace Integracao\Domain\Queues;

use Integracao\Domain\File;

interface ProcessFileProducer
{
    public function publish(File $file);
}
