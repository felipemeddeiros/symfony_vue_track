<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateTrackDto
{
    #[Assert\NotBlank(message: 'Title is required', allowNull: true)]
    private(set) ?string $title = null;

    #[Assert\NotBlank(message: 'Artist is required', allowNull: true)]
    private(set) ?string $artist = null;

    #[Assert\Positive(message: 'Duration must be a positive number')]
    private(set) ?int $duration = null;

    #[Assert\Regex(
        pattern: '/^[A-Z]{2}-[A-Z0-9]{3}-\d{2}-\d{5}$/',
        message: 'ISRC must match format XX-XXX-XX-XXXXX (e.g., US-ABC-12-34567)'
    )]
    private(set) ?string $isrc = null;

    public static function fromArray(array $data): self
    {
        $dto = new self();

        $dto->title = self::normalizeString($data['title'] ?? null);
        $dto->artist = self::normalizeString($data['artist'] ?? null);
        $dto->duration = isset($data['duration']) ? (int) $data['duration'] : null;
        $dto->isrc = self::normalizeString($data['isrc'] ?? null);

        return $dto;
    }

    private static function normalizeString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
