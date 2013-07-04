<?php

/*
 * This file is part of the Blackengine package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Black\Bundle\UserBundle\Model;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

abstract class User implements UserInterface, \Serializable
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var email
     */
    protected $email;

    /**
     * Encrypted password, must be persisted
     *
     * @var string
     */
    protected $password;

    /**
     * Raw password, used by user for defined $password. Not persisted
     *
     * @var string
     */
    protected $rawPassword;

    /**
     * Salt hash
     *
     * @var string
     */
    protected $salt;

    /**
     * @var bool
     */
    protected $isActive;

    /**
     * @var bool
     */
    protected $isRoot;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * @var \DateTime
     */
    protected $expired;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * Random Token for registering
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     */
    protected $registeredAt;

    /**
     * @var Collection
     */
    protected $roles;

    /**
     * @var string
     */
    protected $person;

    public function __construct()
    {
        $this->isActive = false;
        $this->isAdmin  = false;
        $this->locked   = false;
        $this->roles    = array();
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the username
     *
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get the username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the mail address
     *
     * @param $mail
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get the user mail address
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the encrypted passowrd
     *
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get the encrypted password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Valid if the username is not used in password
     *
     * @Assert\True(message="Your password must not contain your username")
     *
     * @return boolean
     */
    public function isPasswordValid()
    {
        return 0 === preg_match('/' . preg_quote($this->username) . '/i', $this->password);
    }

    /**
     * Set the raw password
     *
     * @param $rawPassword
     */
    public function setRawPassword($rawPassword)
    {
        $this->rawPassword = $rawPassword;
    }

    /**
     * Get the raw password
     *
     * @return string
     */
    public function getRawPassword()
    {
        return $this->rawPassword;
    }


    /**
     * Set the salt
     *
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set active parameter
     *
     * @param $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * Get the user active status
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Defined user as root (Linus like it)
     *
     * @param $isRoot
     */
    public function setIsRoot($isRoot)
    {
        if (true === $isRoot) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        $this->isRoot = $isRoot;
    }

    /**
     * Get root status
     *
     * @return bool
     */
    public function getIsRoot()
    {
        return $this->isRoot;
    }

    /**
     * Défined if the account is locked
     *
     * @param $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * Get the locked status
     *
     * @return bool
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set the account as expired
     *
     * @param $expired
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;
    }

    /**
     * Get the expired status
     *
     * @return \DateTime
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set the expiration date for the account
     *
     * @param $expiresAt
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * Get the expiration date
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set last login date for user
     *
     * @param $lastLogin
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * Get last login date for user
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set confirmation token
     *
     * @param $confirmationToken
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * Get confirmation token
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set the resgistration date
     *
     * @param $registeredAt
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;
    }

    /**
     * Get the registration date
     *
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Set Roles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $roles
     */
    public function setRoles($roles)
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    /**
     * Get roles
     *
     * @return Collection|\Doctrine\Common\Collections\ArrayCollection|\Symfony\Component\Security\Core\User\Roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add role
     *
     * @param $role
     */
    public function addRole($role)
    {
        $role = strtoupper($role);

        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }
    }

    /**
     * Remove role
     *
     * @param $role
     */
    public function removeRole($role)
    {
        $role = strtoupper($role);

        if (false !== $key = array_search($role, $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    /**
     * Check if user has a specific role
     *
     * @param $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if (in_array($role, $this->roles)) {
            return true;
        }

        return false;
    }

    /**
     * Erase the default credentials for account creation
     */
    public function eraseCredentials()
    {
        $this->rawPassword = null;
    }

    /**
     * Encode Password and Salt when user account is created
     *
     * @param \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder
     */
    public function encodePassword(PasswordEncoderInterface $encoder)
    {
        if (null !== $this->rawPassword) {
            $this->salt     = sha1(uniqid().microtime().rand(0, 9999999));
            $this->password = $encoder->encodePassword($this->rawPassword, $this->salt);

            $this->eraseCredentials();
        }
    }

    /**
     * Test if the account is not expired
     *
     * @return bool|\DateTime
     */
    public function isAccountNonExpired()
    {
        return $this->expiresAt;
    }

    /**
     * Test if the account is non locked
     *
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return !$this->locked;
    }

    /**
     * Test if the credential are expired
     *
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Test if the account is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isActive;
    }

    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->username,
                $this->password,
                $this->isActive,
                $this->isRoot,
                $this->locked,
                $this->expired,
                $this->expiresAt,
                $this->lastLogin,
                $this->registeredAt
            )
        );
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            $this->isRoot,
            $this->locked,
            $this->expired,
            $this->expiresAt,
            $this->lastLogin,
            $this->registeredAt,
        ) = unserialize($serialized);
    }


    public function setPerson($person)
    {
        $this->person = $person;
    }

    public function getPerson()
    {
        return $this->person;
    }
}
