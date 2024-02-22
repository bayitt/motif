<?php 

declare(strict_types=1);

namespace Motif\Models;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

#[Entity, Table(name: "readings")]
final class Reading implements JsonSerializable {

    #[Id, Column(type: "integer"), GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[Column(type: "string", nullable: false)]
    private string $uuid;

    #[Column(type: "integer", nullable: false)]
    private int $value;

    #[Column(type: "datetimetz_immutable", nullable: false)]
    private DateTimeImmutable $created_at;

    #[Column(type: "datetimetz_immutable", nullable: false)]
    private DateTimeImmutable $updated_at;

    public function __construct(Int $value)
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->value = $value;
        $now = new DateTimeImmutable("now");
        $this->created_at = $now;
        $this->updated_at = $now;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->getUuid(),
            'value' => $this->getValue(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];     
    }
} 