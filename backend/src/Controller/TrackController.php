<?php

namespace App\Controller;

use App\Dto\CreateTrackDto;
use App\Dto\UpdateTrackDto;
use App\Service\TrackService;
use App\Service\ValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tracks')]
class TrackController extends AbstractController
{
    public function __construct(
        private TrackService $trackService,
        private ValidationService $validationService
    ) {
    }

    /**
     * List all tracks
     */
    #[Route('', name: 'track_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $tracks = $this->trackService->getAllTracks();
        
        return $this->json($tracks);
    }

    /**
     * Create a new track
     */
    #[Route('', name: 'track_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !is_array($data)) {
            return $this->json(['error' => 'Invalid JSON data'], 400);
        }

        $dto = CreateTrackDto::fromArray($data);
        $this->validationService->validate($dto);

        $track = $this->trackService->createTrack($dto);

        return $this->json($track->toArray(), 201);
    }

    /**
     * Update an existing track
     */
    #[Route('/{id}', name: 'track_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !is_array($data)) {
            return $this->json(['error' => 'Invalid JSON data'], 400);
        }

        $dto = UpdateTrackDto::fromArray($data);
        $this->validationService->validate($dto);

        $track = $this->trackService->updateTrack($id, $dto);

        return $this->json($track->toArray());
    }
}
