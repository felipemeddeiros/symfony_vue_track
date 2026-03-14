<?php

namespace App\Service;

use App\Dto\CreateTrackDto;
use App\Dto\UpdateTrackDto;
use App\Entity\Track;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TrackService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Get all tracks from the database
     */
    public function getAllTracks(): array
    {
        $tracks = $this->entityManager->getRepository(Track::class)->findAll();
        
        return array_map(fn(Track $track) => $track->toArray(), $tracks);
    }

    /**
     * Create a new track from validated DTO
     */
    public function createTrack(CreateTrackDto $dto): Track
    {
        $track = new Track(
            title: $dto->title,
            artist: $dto->artist,
            duration: $dto->duration,
            isrc: $dto->isrc
        );

        $this->entityManager->persist($track);
        $this->entityManager->flush();

        return $track;
    }

    /**
     * Update an existing track from validated DTO
     *
     * @throws NotFoundHttpException
     */
    public function updateTrack(int $id, UpdateTrackDto $dto): Track
    {
        $track = $this->entityManager->getRepository(Track::class)->find($id);

        if (!$track) {
            throw new NotFoundHttpException('Track not found');
        }

        $track->update(
            title: $dto->title ?? $track->title,
            artist: $dto->artist ?? $track->artist,
            duration: $dto->duration ?? $track->duration,
            isrc: $dto->isrc !== null ? $dto->isrc : $track->isrc
        );

        $this->entityManager->flush();

        return $track;
    }
}
