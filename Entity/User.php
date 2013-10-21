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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Black\Bundle\UserBundle\Model\User as AbstractUser;

/**
 * Class User
 *
 * @package Black\Bundle\UserBundle\Entity
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Black\Bundle\UserBundle\Entity\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User extends AbstractUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="username", type="string", length=15, unique=true)
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min="6", max="15")
     * @Assert\Regex("/^[a-z][a-z0-9]+$/i")
     */
    protected $username;

    /**
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     * @Assert\Type(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(name="raw_password", type="string", length=255, nullable=true)
     * @Assert\Length(min="6")
     * @Assert\Type(type="string")
     */
    protected $rawPassword;

    /**
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     * @Assert\Type(type="string")
     */
    protected $salt;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     * @Assert\Type(type="bool")
     */
    protected $isActive;

    /**
     * @ORM\Column(name="is_root", type="boolean", nullable=true)
     * @Assert\Type(type="bool")
     */
    protected $isRoot;

    /**
     * @ORM\Column(name="locked", type="boolean", nullable=true)
     * @Assert\Type(type="bool")
     */
    protected $locked;

    /**
     * @ORM\Column(name="expired", type="boolean", nullable=true)
     * @Assert\Type(type="bool")
     */
    protected $expired;

    /**
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable
     */
    protected $expiresAt;

    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     * @Gedmo\Timestampable
     */
    protected $lastLogin;

    /**
     * @ORM\Column(name="confirmation_token", type="string", nullable=true)
     * @Assert\Type(type="string")
     */
    protected $confirmationToken;

    /**
     * @ORM\Column(name="registered_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $registeredAt;

    /**
     * @ORM\Column(name="roles", type="array", nullable=true)
     */
    protected $roles;
}
