<?php

declare(strict_types=1);

namespace Motif\Services;

use Doctrine\ORM\EntityManager;
use Motif\Models\Reading;
use DateTimeImmutable;

final class ReadingService
{
    /**
     * 
     *
     * @var EntityManager $EntityManager 
     */
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function flush(): void 
    {
        $this->entityManager->flush();
    }

    public function create(Int $value, ?DateTimeImmutable $created_at): Reading
    {
        $reading = new Reading($value, $created_at);

        $this->entityManager->persist($reading);
        $this->entityManager->flush($reading);

        return $reading;
    }

    public function findOne(Array $args): Reading | null
    {
        return $this->entityManager->getRepository(Reading::class)->findOneBy($args);
    }

    public function delete(Reading $reading): void
    {
        $this->entityManager->remove($reading);
        $this->flush();
    }
}
