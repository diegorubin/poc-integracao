<?php

use Integracao\Infrastructure\AMQPDownloadFileConsumer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class Message
{
    public $body = '{"fullpath": "a", "source": "b"}';
}

/**
 * @covers Integracao\Infrastructure\AMQPDownloadFileConsumer
 * @covers Integracao\Domain\File
 * @covers AMQPDownloadFileConsumerTest::testConsumer
 */
final class AMQPDownloadFileConsumerTest extends TestCase
{
    public function testConsumer()
    {
        $amqpConnectionStub = $this->getMockBuilder(AMQPStreamConnection::class)->disableOriginalConstructor()->getMock();
        $channelStub = $this->getMockBuilder(AMQPChannel::class)->disableOriginalConstructor()->getMock();

        $amqpConnectionStub->method('channel')->willReturn($channelStub);

        $channelStub->expects($this->once())->method('queue_declare')->with('ftp.files.download', false, true, false, false);
        $channelStub->expects($this->once())->method('queue_bind')->with('ftp.files.download', 'integracao.files.download', 'ftp');
        $channelStub->expects($this->once())->method('basic_consume')->with('ftp.files.download', '', false, true, false, false, $this->captureArg($consumerCallback));

        $amqpDownloadFileProducer = new AMQPDownloadFileConsumer($amqpConnectionStub);

        $testThis = $this;
        $amqpDownloadFileProducer->incoming(function ($content) use (&$testThis) {
            $testThis->assertEquals($content->attributes(), [
                'fullpath' => 'a',
                'source' => 'b'
            ]);
        });

        $consumerCallback(new Message());
    }

    private function captureArg(&$arg)
    {
        return $this->callback(function ($argToMock) use (&$arg) {
            $arg = $argToMock;
            return true;
        });
    }
}
