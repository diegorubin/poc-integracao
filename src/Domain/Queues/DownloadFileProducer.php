<?php

namespace Integracao\Domain\Queues;

use Integracao\Domain\File;

interface DownloadFileProducer
{
    public function publish(File $file);
}
