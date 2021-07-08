<?php

namespace Integracao\Infrastructure;

use FtpClient\FtpClient;
use Integracao\Configuration;
use Integracao\Domain\File;
use Integracao\Domain\Repositories\FilesRepository;

class FTPFilesRepository implements FilesRepository
{
    protected $ftp;

    public function __construct()
    {
        $config = Configuration::getInstance()->get()['ftp'];
        $this->ftp = new FtpClient();
        $this->ftp->connect($config['host']);
        $this->ftp->login($config['user'], $config['pass']);
        $this->ftp->pasv($config['pasv']);
    }

    public function fetch()
    {
        $files = [];
        foreach ($this->ftp->scanDir('.', true) as $key => $value) {
            if ($value["type"] == "file") {
                array_push($files, new File(str_replace("file#", "", $key), "ftp"));
            }
        }

        return $files;
    }
}
