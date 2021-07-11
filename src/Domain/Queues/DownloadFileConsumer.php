<?php

namespace Integracao\Domain\Queues;

interface DownloadFileConsumer
{
    public function incoming($callback);
}
