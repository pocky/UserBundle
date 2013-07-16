<?php

/*
 * This file is part of the Blackengine package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Document;

use Black\Bundle\UserBundle\Model\UserInterface;
use Black\Bundle\UserBundle\Document\BaseManager;
use Black\Bundle\UserBundle\Model\UserManagerInterface;

class UserManager extends BaseManager implements UserManagerInterface
{
    /**
     * Update a property
     *
     * @param \Black\Bundle\UserBundle\Model\UserInterface $property
     */
    public function updateUser(UserInterface $user)
    {
        $this->dm->merge($user);
        $this->dm->flush();
    }

    /**
     * Find properties
     *
     * @param array $criteria
     * @return array
     */
    public function findUserBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * Find user by token
     *
     * @param array $criteria
     * @return array
     */
    public function findUserByToken($token)
    {
        return $this->getRepository()->loadUserByConfirmationToken($token);
    }

    /**
     * Find user by username
     *
     * @param array $criteria
     * @return array
     */
    public function findUserByUsername($username)
    {
        return $this->getRepository()->loadUserByUsername($username);
    }

    /**
     * Find property by it's id
     *
     * @param $id
     * @return \Black\Bundle\UserBundle\Model\UserInterface|object
     */
    public function findUserByPersonId($id)
    {
        return $this->getRepository()->getUserByPersonId($id);
    }

    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    public function refreshUser($user)
    {
        $this->getRepository()->refreshUser($user);
    }
}
