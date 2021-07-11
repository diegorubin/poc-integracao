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
        $this->connect();

        $files = [];
        foreach ($this->ftp->scanDir('.', true) as $key => $value) {
            if ($value["type"] == "file") {
                array_push($files, new File(str_replace("file#", "", $key), "ftp"));
            }
        }

        $this->close();

        return $files;
    }

    public function download(string $fullpath, string $destiny)
    {
        $this->connect();

        $this->ftp->get($destiny, $fullpath);

        $this->close();
    }

    private function connect()
    {
        // TODO: check if connected before create a new connection
        $this->ftp->connect($this->config['host']);
        $this->ftp->login($this->config['user'], $this->config['pass']);
        $this->ftp->pasv($this->config['pasv']);
    }

    private function close()
    {
        $this->ftp->close();
    }
}
