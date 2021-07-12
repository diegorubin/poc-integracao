<?php

use Integracao\Domain\File;
use Integracao\Infrastructure\AMQPProcessFileProducer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Infrastructure\AMQPProcessFileProducer
 * @covers Integracao\Domain\File
 * @covers Integracao\Configuration
 */
final class AMQPProcessFileProducerTest extends TestCase
{
    public function testPublish()
    {
        $amqpConnectionStub = $this->getMockBuilder(AMQPStreamConnection::class)->disableOriginalConstructor()->getMock();
        $channelStub = $this->getMockBuilder(AMQPChannel::class)->disableOriginalConstructor()->getMock();

        $amqpConnectionStub->method('channel')->willReturn($channelStub);

        $channelStub->expects($this->once())->method('exchange_declare')->with('integracao.files.process', 'direct', false, true);
        $channelStub->expects($this->once())->method('basic_publish')->with($this->isInstanceOf(AMQPMessage::class), 'integracao.files.process', '');

        $amqpProcessFileProducer = new AMQPProcessFileProducer($amqpConnectionStub);

        $amqpProcessFileProducer->publish(new File('a', 'b'));
    }
}
