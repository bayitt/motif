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

    
    public function findBetweenDates(String $start_date, String $end_date): Array
    {
        $repository = $this->entityManager->createQueryBuilder();
        $query = $repository->select("r")
            ->from(Reading::class, "r")
            ->where("r.created_at BETWEEN :start_date AND :end_date")
            ->orderBy("r.created_at", "DESC")
            ->getQuery();

        $start_date = $start_date . " 00:00:00";
        $end_date = $end_date . " 23:59:59";

        $query->setParameter(":start_date", $start_date);
        $query->setParameter(":end_date", $end_date);

        $readings = $query->getResult();

        return array_map(fn ($reading) => $reading->jsonSerialize(), $readings);
    }

    public function delete(Reading $reading): void
    {
        $this->entityManager->remove($reading);
        $this->flush();
    }
}
