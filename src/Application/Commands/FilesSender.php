<?php

namespace Integracao\Application\Commands;

class FilesSender
{
    private $files_repository;

    public function __construct($files_repository)
    {
        $this->files_repository = $files_repository;
    }

    public function execute()
    {
        foreach ($this->files_repository->fetch() as $file) {
            var_dump($file);
        }
    }
}
