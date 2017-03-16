<?php

namespace Dekalee\RedisSwarrot\MessagePublisher;

use Swarrot\Broker\Message;
use Swarrot\Broker\MessagePublisher\MessagePublisherInterface;

/**
 * Class RedisMessagePublisher
 */
class RedisMessagePublisher implements MessagePublisherInterface
{
    private $channel;
    private $exchange;

    /**
     * @param \Redis $channel
     * @param string $exchange
     */
    public function __construct(\Redis $channel, $exchange)
    {
        $this->channel = $channel;
        $this->exchange = $exchange;
    }

    /**
     * publish.
     *
     * @param Message $message The message to publish
     * @param string  $key     A routing key to use
     */
    public function publish(Message $message, $key = null)
    {
        $this->channel->lPush($key, $message->getBody());
    }

    /**
     * getExchangeName.
     *
     * Return the name of the exchange where the message will be published
     *
     * @return string
     */
    public function getExchangeName()
    {
        return $this->exchange;
    }
}
