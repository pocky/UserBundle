<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Document;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Black\Bundle\UserBundle\Model\UserRepositoryInterface;

/**
 * Class UserRepository
 *
 * @package Black\Bundle\UserBundle\Document
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class UserRepository extends DocumentRepository implements UserProviderInterface, UserRepositoryInterface
{
    /**
     * @param string $username
     *
     * @return object|UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        $qb = $this->createQueryBuilder();

        $qb = $qb
                ->addOr($qb->expr()->field('username')->equals($username))
                ->addOr($qb->expr()->field('email')->equals($username))
                ->getQuery();

        try {
            $user = $qb->getSingleResult();
        } catch (DocumentNotFoundException $e) {
            throw new UsernameNotFoundException(
                sprintf('Unable to find an active user object identified by "%s".', $username)
            );
        }

        return $user;
    }

    /**
     * @param $token
     *
     * @return object
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByConfirmationToken($token)
    {
        $qb = $this->createQueryBuilder()
                ->field('confirmationToken')->equals($token)
                ->field('isActive')->equals(false)
                ->field('locked')->equals(false)
                ->getQuery();

        try {
            $user = $qb->getSingleResult();
        } catch (DocumentNotFoundException $e) {
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
     * @return object
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadLockedUser($username = null, $token = null)
    {
        if (null === $username && null === $token) {
            throw new UsernameNotFoundException('Unable to find an active user object identified by "%s".');
        }

        $qb = $this->createQueryBuilder()
                ->field('isActive')->equals(false)
                ->field('locked')->equals(true);

        if (null !== $username) {
            $qb = $qb
                    ->addOr($qb->expr()->field('username')->equals($username))
                    ->addOr($qb->expr()->field('email')->equals($username));
        }

        if (null !== $token) {
            $qb->expr()->field('token')->equals($token);
        }

        $qb = $qb->getQuery();

        try {
            $user = $qb->getSingleResult();
        } catch (DocumentNotFoundException $e) {
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
     * @return object
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadLostUser($username = null, $token = null)
    {
        $qb = $this->createQueryBuilder()
                ->field('isActive')->equals(true)
                ->field('locked')->equals(false);

        if (null !== $username) {
            $qb = $qb
                    ->addOr($qb->expr()->field('username')->equals($username))
                    ->addOr($qb->expr()->field('email')->equals($username));
        }

        if (null !== $token) {
            $qb->expr()->field('token')->equals($token);
        }

        $qb = $qb->getQuery();

        try {
            $user = $qb->getSingleResult();
        } catch (DocumentNotFoundException $e) {
            throw new UsernameNotFoundException(
                sprintf('Unable to find an active user object identified by "%s".', $token)
            );
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     *
     * @return object|UserInterface
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
        return $this->getDocumentName() === $class || is_subclass_of($class, $this->getDocumentName());
    }
}
