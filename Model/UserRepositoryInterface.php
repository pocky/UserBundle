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

/**
 * Class UserRepositoryInterface
 *
 * @package Black\Bundle\UserBundle\Model
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface UserRepositoryInterface
{
    /**
     * @param $username
     *
     * @return mixed
     */
    function loadUserByUsername($username);

    /**
     * @param $token
     *
     * @return mixed
     */
    function loadUserByConfirmationToken($token);

    /**
     * @param null $username
     * @param null $token
     *
     * @return mixed
     */
    function loadLockedUser($username = null, $token = null);

    /**
     * @param null $username
     * @param null $token
     *
     * @return mixed
     */
    function loadLostUser($username = null, $token = null);

    /**
     * @param $class
     *
     * @return mixed
     */
    function supportsClass($class);
}
