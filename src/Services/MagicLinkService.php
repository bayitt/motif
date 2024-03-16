<?php

declare(strict_types=1);

namespace Motif\Services;

use Doctrine\ORM\EntityManager;
use Motif\Models\MagicLink;

final class MagicLinkService
{
    /**
     * 
     *
     * @var EntityManager $entityManager 
     */
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(): MagicLink
    {
        $magicLink = new MagicLink();

        $this->entityManager->persist($magicLink);
        $this->entityManager->flush($magicLink);

        return $magicLink;
    }

    public function findOne(array $args): MagicLink | null
    {
        return $this->entityManager->getRepository(MagicLink::class)
            ->findOneBy($args);
    }
}
