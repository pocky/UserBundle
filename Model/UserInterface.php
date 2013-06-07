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

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

interface UserInterface extends AdvancedUserInterface
{
    const ROLE_DEFAULT      = 'ROLE_USER';
    const ROLE_SUPER_ADMIN  = 'ROLE_SUPER_ADMIN';

    public function encodePassword(PasswordEncoderInterface $encoder);
}