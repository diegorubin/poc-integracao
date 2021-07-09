<?php

namespace Integracao\Application\Commands;

class FilesSender
{
    private $filesRepository;
    private $filesReadRepository;

    public function __construct($filesRepository, $filesReadRepository)
    {
        $this->filesRepository = $filesRepository;
        $this->filesReadRepository = $filesReadRepository;
    }

    public function execute()
    {
        foreach ($this->filesRepository->fetch() as $file) {
            if (!$this->filesReadRepository->exists($file)) {
                $this->filesReadRepository->put($file);
            }
        }
    }
}
