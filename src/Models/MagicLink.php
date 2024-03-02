<?php

declare(strict_types=1);

namespace Motif\Models;

use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Ramsey\Uuid\Uuid;
use JsonSerializable;

#[Entity, Table(name: "magic_links")]
final class MagicLink implements JsonSerializable {

    #[Id, Column(type: "integer"), GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[Column(type: "string", nullable: false)]
    private string $uuid;

    #[Column(type: "datetimetz_immutable", nullable: false)]
    private DateTimeImmutable $expires_at;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
        $now = new DateTimeImmutable("now");
        $now->add(new DateInterval(("PT0H10M0S")));
        $this->expires_at = $now;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expires_at;
    }

    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->getUuid(),
            'expires_at' => $this->getExpiresAt()
        ];        
    }
}