<?php

declare(strict_types=1);

namespace Motif\Services;

use Doctrine\ORM\EntityManager;
use Motif\Models\Reading;

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

    public function create(Int $value): Reading
    {
        $reading = new Reading($value);

        $this->entityManager->persist($reading);
        $this->entityManager->flush($reading);

        return $reading;
    }

    public function findOne(Array $args): Reading | null
    {
        return $this->entityManager->getRepository(Reading::class)->findOneBy($args);
    }
}
