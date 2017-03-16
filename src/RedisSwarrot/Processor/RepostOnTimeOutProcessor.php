<?php

namespace Dekalee\RedisSwarrot\Processor;

use Psr\Log\LoggerInterface;
use Swarrot\Broker\Message;
use Swarrot\Broker\MessageProvider\MessageProviderInterface;
use Swarrot\Processor\ProcessorInterface;

/**
 * Class RepostOnTimeOutProcessor
 */
class RepostOnTimeOutProcessor implements ProcessorInterface
{
    protected $processor;
    protected $messageProvider;
    protected $logger;

    /**
     * @param ProcessorInterface       $processor       Processor
     * @param MessageProviderInterface $messageProvider Message provider
     * @param LoggerInterface          $logger          Logger
     */
    public function __construct(ProcessorInterface $processor, MessageProviderInterface $messageProvider, LoggerInterface $logger = null)
    {
        $this->processor = $processor;
        $this->messageProvider = $messageProvider;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Message $message, array $options)
    {
        $return = $this->processor->process($message, $options);

        if (false === $return) {
            $this->messageProvider->nack($message, true);

            $this->logger and $this->logger->info(
                sprintf(
                    '[Repost] A timeout occurred. Message #%d has been %s.',
                    $message->getId(),
                    'requeued'
                ),
                [
                    'swarrot_processor' => 'repost',
                ]
            );
        }

        return $return;
    }

}
