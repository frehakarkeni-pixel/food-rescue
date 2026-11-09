<?php
namespace App\Repository;

use App\Entity\Commerce;
use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    public function findByCommerce(Commerce $commerce): array
    {
        return $this->createQueryBuilder('o')
            ->addSelect('cat')
            ->join('o.category', 'cat')
            ->where('o.commerce = :c')->setParameter('c', $commerce)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()->getResult();
    }

    /** Offres visibles dans le catalogue client : actives, non expirées, en stock, commerce approuvé. */
    public function findCatalogue(): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('o')
            ->addSelect('c', 'cat')
            ->join('o.commerce', 'c')
            ->join('o.category', 'cat')
            ->where('o.status = :status')
            ->andWhere('o.quantityAvailable > 0')
            ->andWhere('c.verificationStatus = :approved')
            ->andWhere('(o.expiryDate IS NULL OR o.expiryDate >= :today)')
            ->andWhere('(o.availableDate > :today OR (o.availableDate = :today AND o.pickupDeadline > :now))')
            ->setParameter('status', Offer::STATUS_ACTIVE)
            ->setParameter('approved', 'APPROVED')
            ->setParameter('today', $now, Types::DATE_IMMUTABLE)
            ->setParameter('now', $now, Types::TIME_IMMUTABLE)
            ->orderBy('o.availableDate', 'ASC')
            ->addOrderBy('o.startTime', 'ASC')
            ->getQuery()->getResult();
    }
}