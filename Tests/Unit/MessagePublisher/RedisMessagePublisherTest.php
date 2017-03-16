<?php

namespace Dekalee\RedisSwarrot\Tests\Unit\MessagePublisher;

use Dekalee\RedisSwarrot\MessagePublisher\RedisMessagePublisher;
use Swarrot\Broker\Message;
use Swarrot\Broker\MessagePublisher\MessagePublisherInterface;

/**
 * Class RedisMessagePublisherTest
 */
class RedisMessagePublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedisMessagePublisher
     */
    protected $publisher;

    protected $channel;
    protected $queueName;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->channel = $this->prophesize('\Redis');
        $this->queueName = 'foo';

        $this->publisher = new RedisMessagePublisher($this->channel->reveal(), $this->queueName);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(MessagePublisherInterface::CLASS, $this->publisher);
    }

    /**
     * Test get exchange name
     */
    public function testGetExchangeName()
    {
        $this->assertSame($this->queueName, $this->publisher->getExchangeName());
    }

    /**
     * Test publish
     */
    public function testPublish()
    {
        $message = $this->prophesize(Message::CLASS);
        $message->getBody()->willReturn('body');

        $this->channel->lPush('bar', 'body')->shouldBeCalled();

        $this->publisher->publish($message->reveal(), 'bar');
    }
}
