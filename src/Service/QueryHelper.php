<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class QueryHelper
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * QueryHelper constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getAdminUsers()
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('u')
            ->from('App\Entity\User', 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%ROLE_SUPER_ADMIN%');

        $result = $queryBuilder->getQuery()->getResult();

        return $result ?? array();
    }

    /**
     * @return array
     */
    public function getUsersForAdmin()
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select(array('u.id', 'u.email', 'u.username', 'u.canAddItems', 'u.lastLogin'))
            ->from('App\Entity\User', 'u')
            ->where('u.roles NOT LIKE :roles')
            ->setParameter('roles', '%ROLE_SUPER_ADMIN%')
            ->getQuery();

        $users = $query->getArrayResult();

        foreach ($users as &$user) {
            if ($user['lastLogin']) {
                /** @var \DateTime $date */
                $date = $user['lastLogin'];
                $user['lastLogin'] = $date->format('Y-m-d H:i:s');
            }
        }

        return $users;
    }

    /**
     * @param int $userId
     * @param int $enable
     *
     * @return User
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function toggleUserItemRestriction(int $userId, int $enable)
    {
       $query = $this->entityManager->createQueryBuilder()
           ->select('u')
           ->from('App\Entity\User', 'u')
           ->where('u.id = :user_id')
           ->setParameter('user_id', $userId)
           ->getQuery();

        /** @var User $user */
        $user = $query->getSingleResult();

        $user->setCanAddItems(boolval($enable));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getItemsForUser(User $user){

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('i', 'u.email')
            ->from('App\Entity\Item', 'i')
            ->innerJoin('i.user_id', 'u');

        if (!($user->hasRole('ROLE_SUPER_ADMIN'))) {
            $queryBuilder
                ->where(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq('i.user_id', $user->getId()),
                        $queryBuilder->expr()->eq('i.is_private', 0)
                    )
                );
        }

        $items = $queryBuilder->getQuery()->getArrayResult();

        foreach ($items as &$item) {
            $itemData = $item[0];

            /** @var \DateTime $date */
            $date = $itemData['created'];
            $itemData['created'] = $date->format('Y-m-d H:i:s');

            unset($item[0]);
            $item = array_merge($item, $itemData);
        }

        return $items;
    }
}