<?php

namespace Integracao\Infrastructure;

use FtpClient\FtpClient;
use Integracao\Configuration;
use Integracao\Domain\File;
use Integracao\Domain\FilesRepository;

class FTPFilesRepository implements FilesRepository
{
    protected $ftp;

    public function __construct()
    {
        $config = Configuration::getInstance()['ftp'];
        $this->ftp = new FtpClient();
        $this->ftp->connect($config['host']);
        $this->ftp->login($config['user'], $config['pass']);
        $this->ftp->pasv($config['pasv']);
    }

    public function fetch()
    {
        return array_map(function ($raw_file) {
            var_dump($raw_file);
            return new File('a', 'b');
        }, $this->ftp->scanDir('.', true));
    }
}
