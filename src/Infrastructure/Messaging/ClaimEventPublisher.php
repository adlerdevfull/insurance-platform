<?php

declare(strict_types=1);

namespace Infrastructure\Messaging;

use Domain\Claim\Entities\Claim;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

final class ClaimEventPublisher
{
    private ?AMQPStreamConnection $connection = null;

    public function __construct(private readonly string $amqpUrl) {}

    public function claimReported(Claim $claim): void
    {
        $this->publish('claim.reported', [
            'claim_id'     => $claim->id,
            'claim_number' => $claim->claimNumber,
            'policy_id'    => $claim->policyId,
            'amount'       => $claim->claimedAmount->toFloat(),
            'occurred_at'  => $claim->occurredAt->format('c'),
        ]);
    }

    public function claimApproved(Claim $claim): void
    {
        $this->publish('claim.approved', [
            'claim_id'        => $claim->id,
            'claim_number'    => $claim->claimNumber,
            'policy_id'       => $claim->policyId,
            'approved_amount' => $claim->approvedAmount?->toFloat(),
        ]);
    }

    private function publish(string $routingKey, array $payload): void
    {
        try {
            $parsed = parse_url($this->amqpUrl);
            $conn   = new AMQPStreamConnection(
                $parsed['host'], $parsed['port'] ?? 5672,
                $parsed['user'], $parsed['pass'],
                ltrim($parsed['path'] ?? '/', '/') ?: '/'
            );
            $channel = $conn->channel();
            $channel->exchange_declare('insurance', 'topic', false, true, false);
            $channel->basic_publish(
                new AMQPMessage(json_encode($payload), ['content_type' => 'application/json', 'delivery_mode' => 2]),
                'insurance',
                $routingKey
            );
            $channel->close();
            $conn->close();
        } catch (\Throwable) {
            // não bloqueia o fluxo principal se RabbitMQ estiver indisponível
        }
    }
}
