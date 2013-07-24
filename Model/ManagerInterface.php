<?php

/*
 * This file is part of the Black package.
 *
 * (c) Boris Tacyniak <boris.tacyniak@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Model;

/**
 * Class ManagerInterface
 *
 * @package Black\Bundle\UserBundle\Model
 * @author  Boris Tacyniak <boris.tacyniak@free.fr>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface ManagerInterface
{
    /**
     * @return mixed
     */
    public function getManager();

    /**
     * @return mixed
     */
    public function getRepository();

    /**
     * @return mixed
     */
    public function createInstance();
}
