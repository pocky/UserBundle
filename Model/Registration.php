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

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Registration
 *
 * @package Black\Bundle\UserBundle\Model
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class Registration implements RegistrationInterface
{
    /**
     * @Assert\Type(type="Black\Bundle\UserBundle\Model\User")
     */
    protected $user;

    /**
     * @var
     *
     * @Assert\NotBlank()
     * @Assert\True()
     */
    protected $termsAccepted;

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $termsAccepted
     */
    public function setTermsAccepted($termsAccepted)
    {
        $this->termsAccepted = (boolean) $termsAccepted;
    }

    /**
     * @return mixed
     */
    public function getTermsAccepted()
    {
        return $this->termsAccepted;
    }
} 
