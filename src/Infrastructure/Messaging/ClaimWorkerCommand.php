<?php

declare(strict_types=1);

namespace Infrastructure\Messaging;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:worker:claims', description: 'Process claim events from RabbitMQ')]
class ClaimWorkerCommand extends Command
{
    public function __construct(private readonly string $amqpUrl)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Starting claim worker...</info>');

        try {
            $parsed  = parse_url($this->amqpUrl);
            $conn    = new AMQPStreamConnection(
                $parsed['host'], $parsed['port'] ?? 5672,
                $parsed['user'], $parsed['pass'],
                ltrim($parsed['path'] ?? '/', '/') ?: '/'
            );
            $channel = $conn->channel();
            $channel->exchange_declare('insurance', 'topic', false, true, false);
            $channel->queue_declare('claim_notifications', false, true, false, false);
            $channel->queue_bind('claim_notifications', 'insurance', 'claim.*');

            $channel->basic_consume('claim_notifications', '', false, false, false, false,
                function (AMQPMessage $msg) use ($output) {
                    $payload = json_decode($msg->body, true);
                    $output->writeln(sprintf(
                        '[%s] Event received: %s | Claim #%s',
                        date('H:i:s'),
                        $msg->getRoutingKey(),
                        $payload['claim_number'] ?? '?'
                    ));
                    // aqui entraria lógica de envio de email, notificação, etc.
                    $msg->ack();
                }
            );

            while ($channel->is_consuming()) {
                $channel->wait();
            }
        } catch (\Throwable $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
