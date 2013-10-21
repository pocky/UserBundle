<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Model;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * Class UserInterface
 *
 * @package Black\Bundle\UserBundle\Model
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface UserInterface extends AdvancedUserInterface
{
    /**
     *
     */
    const ROLE_DEFAULT      = 'ROLE_USER';

    /**
     *
     */
    const ROLE_SUPER_ADMIN  = 'ROLE_SUPER_ADMIN';

    /**
     * @param PasswordEncoderInterface $encoder
     *
     * @return mixed
     */
    public function encodePassword(PasswordEncoderInterface $encoder);
}
