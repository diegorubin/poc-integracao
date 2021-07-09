<?php

namespace Integracao\Infrastructure;

use Integracao\Configuration;
use Integracao\Domain\File;
use Integracao\Domain\Repositories\FilesRepository;

class FTPFilesRepository implements FilesRepository
{
    private $ftp;
    private $config;

    public function __construct($ftp)
    {
        $this->config = Configuration::getInstance()->get()['ftp'];
        $this->ftp = $ftp;
    }

    public function fetch()
    {
        $this->ftp->connect($this->config['host']);
        $this->ftp->login($this->config['user'], $this->config['pass']);
        $this->ftp->pasv($this->config['pasv']);
 
        $files = [];
        foreach ($this->ftp->scanDir('.', true) as $key => $value) {
            if ($value["type"] == "file") {
                array_push($files, new File(str_replace("file#", "", $key), "ftp"));
            }
        }

        $this->ftp->close();

        return $files;
    }
}
