<?php

use FtpClient\FtpClient;
use Integracao\Domain\File;
use Integracao\Infrastructure\FTPFilesRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Infrastructure\FTPFilesRepository
 * @covers Integracao\Configuration
 * @covers Integracao\Domain\File
 */
final class FTPFilesRepositoryTest extends TestCase
{
    public function testConstruct()
    {
        $ftpClientStub = $this->getMockBuilder(FtpClient::class)->disableOriginalConstructor()->getMock();
        $ftpClientStub->method('scanDir')->willReturn(["directory#dir" => ["type" => "directory"], "file#dir/file" => ["type" => "file"]]);
        $ftpClientStub->expects($this->once())->method('scanDir')->with('.', true);
        $ftpClientStub->expects($this->once())->method('connect')->with('172.17.0.1');
        $ftpClientStub->expects($this->once())->method('close')->with();
        $ftpClientStub->expects($this->once())->method('login')->with('ftp', 'ftp');
        $ftpClientStub->expects($this->once())->method('__call')->with('pasv', [true]);

        $fileRepository = new FTPFilesRepository($ftpClientStub);

        $result = $fileRepository->fetch();

        $this->assertEquals([new File("dir/file", "ftp")], $result);
    }
}
