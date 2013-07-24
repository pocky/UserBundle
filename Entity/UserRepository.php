<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Black\Bundle\UserBundle\Model\UserRepositoryInterface;

/**
 * Class UserRepository
 *
 * @package Black\Bundle\UserBundle\Entity
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class UserRepository extends EntityRepository implements UserProviderInterface, UserRepositoryInterface
{
    /**
     * @param string $username
     *
     * @return UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        $qb = $this->getQueryBuilder();

        $qb = $qb
                ->where('u.username LIKE :username')
                ->orWhere('u.email LIKE :email')
                ->setParameter('username', $username)
                ->setParameter('email', $username)
                ->getQuery();

        try {
            $user = $qb->getSingleResult();
        } catch (EntityNotFoundException $e) {
            throw new UsernameNotFoundException(
                sprintf('Unable to find an active user object identified by "%s".', $username)
            );
        }

        return $user;
    }

    /**
     * @param $token
     *
     * @return mixed
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByConfirmationToken($token)
    {
        $qb = $this->getQueryBuilder()
                ->where('u.confirmation_token LIKE :token')
                ->andWhere('u.is_active LIKE :is_active')
                ->andWhere('u.locked LIKE :locked')
                ->setParameter('token', $token)
                ->setParameter('is_active', false)
                ->setParameter('locked', false)
                ->getQuery();

        try {
            $user = $qb->getSingleResult();
        } catch (EntityNotFoundException $e) {
            throw new UsernameNotFoundException(
                sprintf('Unable to find an active user object identified by "%s".', $token)
            );
        }

        return $user;
    }

    /**
     * @param null $username
     * @param null $token
     *
     * @return mixed
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadLockedUser($username = null, $token = null)
    {
        if (null === $username && null === $token) {
            throw new UsernameNotFoundException('Unable to find an active user object identified by "%s".');
        }

        $qb = $this->getQueryBuilder()
                ->where('u.is_active LIKE :is_active')
                ->andWhere('u.locked LIKE :locked')
                ->setParameter('is_active', false)
                ->setParameter('locked', true);

        if (null !== $username) {
            $qb = $qb
                    ->andWhere(
                        $qb
                            ->expr()
                            ->orX(
                                $qb->expr()->like('u.username', ':username'),
                                $qb->expr()->like('u.email', ':email')
                            )
                    )
                    ->setParameter('username', $username)
                    ->setParameter('email', $username);
        }

        if (null !== $token) {
            $qb = $qb
                    ->andWhere('u.confirmation_token LIKE :token')
                    ->setParameter('token', $token);
        }

        $qb = $qb->getQuery();

        try {
            $user = $qb->getSingleResult();
        } catch (EntityNotFoundException $e) {
            throw new UsernameNotFoundException(
                sprintf('Unable to find an active user object identified by "%s".', $token)
            );
        }

        return $user;
    }

    /**
     * @param null $username
     * @param null $token
     *
     * @return mixed
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadLostUser($username = null, $token = null)
    {
        $qb = $this->getQueryBuilder()
                ->where('u.is_active LIKE :is_active')
                ->andWhere('u.locked LIKE :locked')
                ->setParameter('is_active', true)
                ->setParameter('locked', false);

        if (null !== $username) {
            $qb = $qb
                    ->andWhere(
                        $qb
                            ->expr()
                            ->orX(
                                $qb->expr()->like('u.username', ':username'),
                                $qb->expr()->like('u.email', ':email')
                            )
                    )
                    ->setParameter('username', $username)
                    ->setParameter('email', $username);
        }

        if (null !== $token) {
            $qb = $qb
                    ->andWhere('u.confirmation_token LIKE :token')
                    ->setParameter('token', $token);
        }

        $qb = $qb->getQuery();

        try {
            $user = $qb->getSingleResult();
        } catch (EntityNotFoundException $e) {
            throw new UsernameNotFoundException(
                sprintf('Unable to find an active user object identified by "%s".', $token)
            );
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    /**
     * @param string $alias
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQueryBuilder($alias = 'u')
    {
        return $this->createQueryBuilder($alias);
    }
}
