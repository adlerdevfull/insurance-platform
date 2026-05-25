<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Application\Policy\Commands\CreatePolicyHandler;
use Application\Policy\Commands\TransitionPolicyHandler;
use Domain\Policy\Repositories\PolicyRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class PolicyController extends AbstractController
{
    public function __construct(
        private readonly CreatePolicyHandler $createHandler,
        private readonly TransitionPolicyHandler $transitionHandler,
        private readonly PolicyRepositoryInterface $policies,
    ) {}

    #[Route('/api/v1/policies', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page    = (int) $request->query->get('page', 1);
        $perPage = (int) $request->query->get('per_page', 15);
        $filters = array_filter($request->query->all(), fn($k) => in_array($k, ['status']), ARRAY_FILTER_USE_KEY);

        $items = $this->policies->paginate($page, $perPage, $filters);
        $total = $this->policies->count($filters);

        return new JsonResponse([
            'data' => array_map(fn($p) => $this->serialize($p), $items),
            'meta' => ['total' => $total, 'page' => $page, 'per_page' => $perPage],
        ]);
    }

    #[Route('/api/v1/policies/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $policy = $this->policies->findById($id);
        if (!$policy) return new JsonResponse(['error' => 'Policy not found'], 404);
        return new JsonResponse(['data' => $this->serialize($policy)]);
    }

    #[Route('/api/v1/policies', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $policy = $this->createHandler->handle(
            userId: $this->getUser()->getId(),
            insuredName: $data['insured_name'] ?? '',
            insuredDocument: $data['insured_document'] ?? '',
            riskType: $data['risk_type'] ?? 'low',
            basePremiumCents: (int) ($data['base_premium_cents'] ?? 0),
            startsAt: $data['starts_at'] ?? 'now',
            expiresAt: $data['expires_at'] ?? '+1 year',
        );

        return new JsonResponse(['data' => $this->serialize($policy)], 201);
    }

    #[Route('/api/v1/policies/{id}/transition', methods: ['PATCH'])]
    public function transition(int $id, Request $request): JsonResponse
    {
        $data   = json_decode($request->getContent(), true);
        $policy = $this->transitionHandler->handle($id, $data['status'] ?? '');
        return new JsonResponse(['data' => $this->serialize($policy)]);
    }

    private function serialize($p): array
    {
        return [
            'id'               => $p->id,
            'policy_number'    => $p->policyNumber,
            'insured_name'     => $p->insuredName,
            'insured_document' => $p->insuredDocument,
            'risk_type'        => $p->riskType->value,
            'base_premium'     => $p->basePremium->toFloat(),
            'premium'          => $p->premium->toFloat(),
            'status'           => $p->status->value,
            'starts_at'        => $p->startsAt->format('Y-m-d'),
            'expires_at'       => $p->expiresAt->format('Y-m-d'),
        ];
    }
}
