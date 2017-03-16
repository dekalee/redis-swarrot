<?php

namespace Dekalee\RedisSwarrot\MessageProvider;

use Swarrot\Broker\Message;
use Swarrot\Broker\MessageProvider\MessageProviderInterface;

/**
 * Class RedisMessageProvider
 */
class RedisMessageProvider implements MessageProviderInterface
{
    private $channel;
    private $queueName;

    /**
     * @param \Redis $channel
     * @param string $queueName
     */
    public function __construct(\Redis $channel, $queueName)
    {
        $this->channel = $channel;
        $this->queueName = $queueName;
    }

    /**
     * get.
     *
     * @return Message|null
     */
    public function get()
    {
        if (0 == $this->channel->lLen($this->getQueueName())) {
            return null;
        }

        list($channel, $json) = $this->channel->brPop($this->getQueueName(), 0);

        $message = new Message($json, ['channel' => $channel]);

        return $message;
    }

    /**
     * ack.
     *
     * @param Message $message
     */
    public function ack(Message $message)
    {
    }

    /**
     * nack.
     *
     * @param Message $message The message to NACK
     * @param bool    $requeue Requeue the message in the queue ?
     */
    public function nack(Message $message, $requeue = false)
    {
        if ($requeue) {
            $this->channel->lPush($this->getQueueName(), $message->getBody());
        }
    }

    /**
     * getQueueName.
     *
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }
}
