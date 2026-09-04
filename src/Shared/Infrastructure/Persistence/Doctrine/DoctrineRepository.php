<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Aggregate\AggregateRoot;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

abstract class DoctrineRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    protected function persist(AggregateRoot $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $entityClass
     *
     * @return EntityRepository<T>
     */
    protected function repository(string $entityClass): EntityRepository
    {
        return $this->entityManager->getRepository($entityClass);
    }
}
