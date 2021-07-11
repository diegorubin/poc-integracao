<?php

use FtpClient\FtpClient;
use Integracao\Infrastructure\FTPFilesRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Infrastructure\FTPFilesRepository
 * @covers Integracao\Configuration
 * @covers Integracao\Domain\File
 */
final class FTPFilesRepositoryTest extends TestCase
{
    public function testFetch()
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

        $file = $result[0];
        $this->assertEquals(count($result), 1);
        $this->assertEquals("dir/file", $file->getFullpath());
        $this->assertEquals("integracao", $file->getSource());
    }

    public function testDownload()
    {
        $ftpClientStub = $this->getMockBuilder(FtpClient::class)->disableOriginalConstructor()->getMock();
        $ftpClientStub->expects($this->once())->method('connect')->with('172.17.0.1');
        $ftpClientStub->expects($this->once())->method('close')->with();
        $ftpClientStub->expects($this->exactly(2))->method('__call')->with($this->logicalOr(
            $this->equalTo('pasv', [true]),
            $this->equalTo('get', [true])
        ));
        $fileRepository = new FTPFilesRepository($ftpClientStub);

        $fileRepository->download('a', 'b');
    }
}
