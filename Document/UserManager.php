<?php

/*
 * This file is part of the Blackengine package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blackroom\Bundle\UserBundle\Document;

use Blackroom\Bundle\UserBundle\Model\UserInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;

class UserManager extends DocumentManager
{
    protected $_dm;
    protected $_repository;
    protected $_class;

    /**
     * Constructor
     *
     * @param \Doctrine\ODM\MongoDB\DocumentManager $dm
     * @param \Doctrine\ODM\MongoDB\Configuration $class
     */
    public function __construct(DocumentManager $dm, $class)
    {
        $this->_dm          = $dm;
        $this->_repository  = $dm->getRepository($class);

        $metadata           = $dm->getClassMetadata($class);
        $this->_class       = $metadata->name;
    }

    /**
     * Return the document manager
     *
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->_dm;
    }

    /**
     * Save and Flush a new property
     *
     * @param \Blackroom\Bundle\UserBundle\Model\UserInterface $property
     */
    public function persistAndFlush(UserInterface $property)
    {
        $this->_dm->persist($property);
        $this->_dm->flush();
    }

    /**
     * Delete a property
     *
     * @param \Blackroom\Bundle\UserBundle\Model\UserInterface $id
     */
    public function removeAndFlush(UserInterface $property)
    {
        $this->_dm->remove($property);
        $this->_dm->flush();
    }

    /**
     * Update a property
     *
     * @param \Blackroom\Bundle\UserBundle\Model\UserInterface $property
     */
    public function updateUser(UserInterface $user)
    {
        $this->_dm->merge($user);
        $this->_dm->flush();
    }

    public function flush()
    {
        $this->_dm->flush();
    }

    /**
     * Find properties
     *
     * @param array $criteria
     * @return array
     */
    public function findUserBy(array $criteria)
    {
        return $this->_repository->findBy($criteria);
    }

    /**
     * Find user by token
     *
     * @param array $criteria
     * @return array
     */
    public function findUserByToken($token)
    {
        return $this->_repository->loadUserByConfirmationToken($token);
    }

    /**
     * Find user by username
     *
     * @param array $criteria
     * @return array
     */
    public function findUserByUsername($username)
    {
        return $this->_repository->loadUserByUsername($username);
    }

    /**
     * Find property by it's id
     *
     * @param $id
     * @return \Blackroom\Bundle\UserBundle\Model\UserInterface|object
     */
    public function findUserById($id)
    {
        return $this->_repository->find($id);
    }

    public function refreshUser($user)
    {
        $this->_repository->refreshUser($user);
    }

    /**
     * Create a new Config Object
     *
     * @return $config object
     */
    public function createUser()
    {
        $class  = $this->getClass();
        $config = new $class;

        return $config;
    }

    protected function getClass()
    {
        return $this->_class;
    }
}