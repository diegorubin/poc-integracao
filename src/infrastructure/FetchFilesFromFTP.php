<?php

namespace Integracao\Commands;

class FetchFiles
{
    public function fetch()
    {
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect("172.17.0.1");
        $ftp->login("ftp", "ftp");
        $ftp->pasv(true);

        return $ftp->scanDir('.', true);
    }
}
