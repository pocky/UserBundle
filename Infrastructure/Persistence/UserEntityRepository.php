<?php
/*
 * This file is part of the ${FILE_HEADER_PACKAGE} package.
 *
 * ${FILE_HEADER_COPYRIGHT}
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Black\Bundle\UserBundle\Infrastructure\Persistence;

use Black\Component\User\Infrastructure\Persistence\UserRepository;
use Black\Component\User\Domain\Model\UserId;
use Doctrine\ORM\EntityRepository;

/**
 * Class UserEntityRepository
 *
 * @author Alexandre Balmes <${COPYRIGHT_NAME}>
 * @license ${COPYRIGHT_LICENCE}
 */
class UserEntityRepository extends EntityRepository implements UserRepository
{
    /**
     * @param UserId $userId
     * @return mixed
     */
    public function findUserByUserId(UserId $userId)
    {
        $query = $this->getQueryBuilder()
            ->where('p.userId.value = :id', $userId)
            ->setParameter('id', $userId->getValue())
            ->getQuery();

        return $query->getSingleResult();
    }

    /**
     * @param string $alias
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder($alias = 'p')
    {
        return $this->createQueryBuilder($alias);
    }
} 