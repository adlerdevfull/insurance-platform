<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Application\Claim\Commands\ReportClaimHandler;
use Application\Claim\Commands\ReviewClaimHandler;
use Domain\Claim\Repositories\ClaimRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ClaimController extends AbstractController
{
    public function __construct(
        private readonly ReportClaimHandler $reportHandler,
        private readonly ReviewClaimHandler $reviewHandler,
        private readonly ClaimRepositoryInterface $claims,
    ) {}

    #[Route('/api/v1/claims', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page    = (int) $request->query->get('page', 1);
        $perPage = (int) $request->query->get('per_page', 15);
        $filters = array_filter($request->query->all(), fn($k) => in_array($k, ['status', 'policy_id']), ARRAY_FILTER_USE_KEY);

        $items = $this->claims->paginate($page, $perPage, $filters);
        $total = $this->claims->count($filters);

        return new JsonResponse([
            'data' => array_map(fn($c) => $this->serialize($c), $items),
            'meta' => ['total' => $total, 'page' => $page, 'per_page' => $perPage],
        ]);
    }

    #[Route('/api/v1/claims/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $claim = $this->claims->findById($id);
        if (!$claim) return new JsonResponse(['error' => 'Claim not found'], 404);
        return new JsonResponse(['data' => $this->serialize($claim)]);
    }

    #[Route('/api/v1/claims', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $claim = $this->reportHandler->handle(
                policyId: (int) ($data['policy_id'] ?? 0),
                userId: $this->getUser()->getId(),
                description: $data['description'] ?? '',
                claimedAmountCents: (int) ($data['claimed_amount_cents'] ?? 0),
                occurredAt: $data['occurred_at'] ?? 'now',
            );
        } catch (\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 422);
        }

        return new JsonResponse(['data' => $this->serialize($claim)], 201);
    }

    #[Route('/api/v1/claims/{id}/review', methods: ['PATCH'])]
    public function review(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $decision = $data['decision'] ?? '';

        if ($decision === 'start_review') {
            $claim = $this->claims->findById($id)
                ?? throw new \DomainException('Claim not found');
            $updated = $claim->transition(\Domain\Claim\Enums\ClaimStatus::UnderReview);
            $saved   = $this->claims->save($updated);
            return new JsonResponse(['data' => $this->serialize($saved)]);
        }

        $claim = $this->reviewHandler->handle(
            id: $id,
            decision: $decision,
            approvedAmountCents: isset($data['approved_amount_cents']) ? (int) $data['approved_amount_cents'] : null,
            rejectionReason: $data['rejection_reason'] ?? null,
        );

        return new JsonResponse(['data' => $this->serialize($claim)]);
    }

    private function serialize($c): array
    {
        return [
            'id'               => $c->id,
            'claim_number'     => $c->claimNumber,
            'policy_id'        => $c->policyId,
            'description'      => $c->description,
            'claimed_amount'   => $c->claimedAmount->toFloat(),
            'approved_amount'  => $c->approvedAmount?->toFloat(),
            'status'           => $c->status->value,
            'rejection_reason' => $c->rejectionReason,
            'occurred_at'      => $c->occurredAt->format('Y-m-d'),
        ];
    }
}
