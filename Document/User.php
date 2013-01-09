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

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Blackroom\Bundle\UserBundle\Model\User as AbstractUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Blackroom\Bundle\EngineBundle\Traits\ThingDocument;

/**
 * User Document
 *
 * @ODM\MappedSuperClass()
 */
class User extends AbstractUser
{
    /**
     * @ODM\String
     * @ODM\UniqueIndex
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\MinLength(6)
     * @Assert\MaxLength(15)
     * @Assert\Regex("/^[a-z][a-z0-9]+$/i")
     */
    protected $username;

    /**
     * @ODM\String
     * @ODM\UniqueIndex
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @ODM\String
     * @Assert\Type(type="string")
     */
    protected $password;

    /**
     * @ODM\String
     * @Assert\MinLength(6)
     * @Assert\Type(type="string")
     */
    protected $rawPassword;

    /**
     * @ODM\String
     * @Assert\Type(type="string")
     */
    protected $salt;

    /**
     * @ODM\Boolean
     * @Assert\Type(type="bool")
     */
    protected $isActive;

    /**
     * @ODM\Boolean
     * @Assert\Type(type="bool")
     */
    protected $isRoot;

    /**
     * @ODM\Boolean
     * @Assert\Type(type="bool")
     */
    protected $locked;

    /**
     * @ODM\Boolean
     * @Assert\Type(type="bool")
     */
    protected $expired;

    /**
     * @ODM\Timestamp
     * @Gedmo\Timestampable
     */
    protected $expiresAt;

    /**
     * @ODM\Timestamp
     * @Gedmo\Timestampable
     */
    protected $lastLogin;

    /**
     * @ODM\String
     * @Assert\Type(type="string")
     */
    protected $confirmationToken;

    /**
     * @ODM\Timestamp
     * @Gedmo\Timestampable(on="create")
     */
    protected $registeredAt;

    /**
     * @ODM\Collection
     */
    protected $roles;
}