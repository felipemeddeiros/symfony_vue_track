<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateTrackDto
{
    #[Assert\NotBlank(message: 'Title is required')]
    private(set) string $title;

    #[Assert\NotBlank(message: 'Artist is required')]
    private(set) string $artist;

    #[Assert\Positive(message: 'Duration must be a positive number')]
    private(set) int $duration;

    #[Assert\Regex(
        pattern: '/^[A-Z]{2}-[A-Z0-9]{3}-\d{2}-\d{5}$/',
        message: 'ISRC must match format XX-XXX-XX-XXXXX (e.g., US-ABC-12-34567)'
    )]
    private(set) ?string $isrc = null;

    public static function fromArray(array $data): self
    {
        $dto = new self();

        $dto->title = self::normalizeRequiredString($data['title'] ?? null);
        $dto->artist = self::normalizeRequiredString($data['artist'] ?? null);
        $dto->duration = isset($data['duration']) ? (int) $data['duration'] : 0;
        $dto->isrc = self::normalizeOptionalString($data['isrc'] ?? null);

        return $dto;
    }

    private static function normalizeRequiredString(mixed $value): string
    {
        return trim((string) $value);
    }

    private static function normalizeOptionalString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
