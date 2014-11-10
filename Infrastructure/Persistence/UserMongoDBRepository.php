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
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class UserMongoDBRepository
 *
 * @author Alexandre Balmes <${COPYRIGHT_NAME}>
 * @license ${COPYRIGHT_LICENCE}
 */
class UserMongoDBRepository extends DocumentRepository implements UserRepository
{
    /**
     * @param UserId $userId
     * @return mixed
     */
    public function findUserByUserId(UserId $userId)
    {
        $query = $this->getQueryBuilder()
            ->field('userId.value')->equals($userId->getValue())
            ->getQuery();

        return $query->getSingleResult();
    }

    /**
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    public function getQueryBuilder()
    {
        return $this->createQueryBuilder();
    }
} 