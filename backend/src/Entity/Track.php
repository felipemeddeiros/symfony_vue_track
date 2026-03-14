<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'track')]
class Track
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private(set) ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private(set) string $title;

    #[ORM\Column(type: 'string', length: 255)]
    private(set) string $artist;

    #[ORM\Column(type: 'integer')]
    private(set) int $duration;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private(set) ?string $isrc;

    public function __construct(
        string $title,
        string $artist,
        int $duration,
        ?string $isrc = null
    ) {
        $this->title = $title;
        $this->artist = $artist;
        $this->duration = $duration;
        $this->isrc = $isrc;
    }

    public function update(
        string $title,
        string $artist,
        int $duration,
        ?string $isrc = null
    ): void {
        $this->title = $title;
        $this->artist = $artist;
        $this->duration = $duration;
        $this->isrc = $isrc;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'artist' => $this->artist,
            'duration' => $this->duration,
            'isrc' => $this->isrc,
        ];
    }
}
