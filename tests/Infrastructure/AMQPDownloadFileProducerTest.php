<?php

use Integracao\Domain\File;
use Integracao\Infrastructure\AMQPDownloadFileProducer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Infrastructure\AMQPDownloadFileProducer
 * @covers Integracao\Domain\File
 * @covers Integracao\Configuration
 */
final class AMQPDownloadFileProducerTest extends TestCase
{
    public function testPublish()
    {
        $amqpConnectionStub = $this->getMockBuilder(AMQPStreamConnection::class)->disableOriginalConstructor()->getMock();
        $channelStub = $this->getMockBuilder(AMQPChannel::class)->disableOriginalConstructor()->getMock();

        $amqpConnectionStub->method('channel')->willReturn($channelStub);

        $channelStub->expects($this->once())->method('exchange_declare')->with('integracao.files.download', 'direct', false, true);
        $channelStub->expects($this->once())->method('basic_publish')->with($this->isInstanceOf(AMQPMessage::class), 'integracao.files.download', 'integracao');

        $amqpDownloadFileProducer = new AMQPDownloadFileProducer($amqpConnectionStub);

        $amqpDownloadFileProducer->publish(new File('a', 'b'));
    }
}
