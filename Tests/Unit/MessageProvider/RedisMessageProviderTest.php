<?php

namespace Dekalee\RedisSwarrot\Tests\Unit\MessageProvider;

use Dekalee\RedisSwarrot\MessageProvider\RedisMessageProvider;

/**
 * Class RedisMessageProviderTest
 */
class RedisMessageProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedisMessageProvider
     */
    private $provider;

    private $redis;
    private $queueName;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $this->redis = $this->prophesize('\Redis');
        $this->queueName = 'foo';

        $this->provider = new RedisMessageProvider($this->redis->reveal(), $this->queueName);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Swarrot\Broker\MessageProvider\MessageProviderInterface', $this->provider);
    }

    /**
     * Test get method
     */
    public function testGet()
    {
        $data = json_encode(['bar' => 'baz']);
        $this->redis->lLen('foo')->willReturn(1);
        $this->redis->brPop('foo', 0)
            ->shouldBeCalled()
            ->willReturn(
            [
                'foo',
                $data,
            ]
        );

        $message = $this->provider->get();

        $this->assertInstanceOf('Swarrot\Broker\Message', $message);
        $this->assertSame($data, $message->getBody());
        $this->assertSame(['channel' => 'foo'], $message->getProperties());
        $this->assertNull($message->getId());
    }

    /**
     * Test nack with requeue
     */
    public function testNack()
    {
        $data = json_encode(['bar' => 'baz']);
        $message = $this->prophesize('Swarrot\Broker\Message');
        $message->getBody()->willReturn($data);

        $this->redis->lPush($this->queueName, $data)->shouldBeCalled();

        $this->provider->nack($message->reveal(), true);
    }

    /**
     * Test nack with requeue
     */
    public function testNackWithNoRequeue()
    {
        $data = json_encode(['bar' => 'baz']);
        $message = $this->prophesize('Swarrot\Broker\Message');
        $message->getBody()->willReturn($data);

        $this->redis->lPush($this->queueName, $data)->shouldNotBeCalled();

        $this->provider->nack($message->reveal(), false);
    }
}
