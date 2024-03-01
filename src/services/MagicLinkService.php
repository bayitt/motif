<?php

declare(strict_types=1);

namespace Motif\Services;

use Doctrine\ORM\EntityManager;
use Motif\Models\MagicLink;

final class MagicLinkService 
{
    /** @var EntityManager $EntityManager */
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create()
    {
        $magicLink = new MagicLink();

        $this->entityManager->persist($magicLink);
        $this->entityManager->flush($magicLink);

        return $magicLink;
    }
}