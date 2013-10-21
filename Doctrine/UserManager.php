<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Doctrine;

use Black\Bundle\UserBundle\Model\UserInterface;
use Black\Bundle\UserBundle\Model\UserManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class UserManager
 *
 * @package Black\Bundle\UserBundle\Doctrine
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class UserManager implements UserManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param ObjectManager $dm
     * @param string        $class
     */
    public function __construct(ObjectManager $dm, $class)
    {
        $this->manager     = $dm;
        $this->repository  = $dm->getRepository($class);

        $metadata          = $dm->getClassMetadata($class);
        $this->class       = $metadata->name;
    }

    /**
     * @return ObjectManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return ObjectRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param object $model
     *
     * @throws \InvalidArgumentException
     */
    public function persist($model)
    {
        if (!$model instanceof $this->class) {
            throw new \InvalidArgumentException(gettype($model));
        }

        $this->getManager()->persist($model);
    }

    /**
     *
     */
    public function flush()
    {
        $this->getManager()->flush();
    }

    /**
     * @param object $model
     *
     * @throws \InvalidArgumentException
     */
    public function remove($model)
    {
        if (!$model instanceof $this->class) {
            throw new \InvalidArgumentException(gettype($model));
        }
        $this->getManager()->remove($model);
    }

    /**
     * @param mixed $model
     */
    public function persistAndFlush($model)
    {
        $this->persist($model);
        $this->flush();
    }

    /**
     * @param mixed $model
     */
    public function removeAndFlush($model)
    {
        $this->getManager()->remove($model);
        $this->getManager()->flush();
    }

    /**
     * @return $config object
     */
    public function createInstance()
    {
        $class  = $this->getClass();
        $model = new $class;

        return $model;
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return $this->class;
    }

    /**
     * Update a property
     *
     * @param \Black\Bundle\UserBundle\Model\UserInterface $user
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
     * 
     * @return array
     */
    public function findUserBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function findUserById($id)
    {
        return $this->getRepository()->findOneBy(array('id' => $id));
    }

    /**
     * @param string $token
     * 
     * @return array
     */
    public function findUserByToken($token)
    {
        return $this->getRepository()->loadUserByConfirmationToken($token);
    }

    /**
     * @param string $username
     * 
     * @return UserInterface
     */
    public function findUserByUsername($username)
    {
        return $this->getRepository()->loadUserByUsername($username);
    }

    /**
     * @param integer $id
     * 
     * @return \Black\Bundle\UserBundle\Model\UserInterface|object
     */
    public function findUserByPersonId($id)
    {
        return $this->getRepository()->getUserByPersonId($id);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @param UserInterface $user
     */
    public function refreshUser($user)
    {
        $this->getRepository()->refreshUser($user);
    }
}
