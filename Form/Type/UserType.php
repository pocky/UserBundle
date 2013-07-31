<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Black\Bundle\UserBundle\Form\EventListener\SetUserDataSubscriber;

/**
 * Class UserType
 *
 * @package Black\Bundle\UserBundle\Form\Type
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class UserType extends AbstractType
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @param $dbDriver
     * @param $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                'text',
                array(
                    'label'     => 'user.admin.user.username.text'
                )
            )
            ->add(
                'email',
                'email',
                array(
                    'label'     => 'user.admin.user.email.text'
                )
            )
            ->add(
                'isActive',
                'checkbox',
                array(
                    'label'     => 'user.admin.user.isActive.text',
                    'required'  => false
                )
            )
            ->add(
                'isRoot',
                'checkbox',
                array(
                    'label'     => 'user.admin.user.isRoot.text',
                    'required'  => false
                )
            )
            ->add(
                'locked',
                'checkbox',
                array(
                    'label'     => 'user.admin.user.isLocked.text',
                    'required'  => false
                )
            )
            ->add(
                'roles',
                'collection',
                array(
                    'type'          => 'text',
                    'required'      => false,
                    'label'         => 'user.admin.user.roles.text',
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'attr'          => array(
                        'class' => 'roles-collection',
                        'add'   => 'add-another-role'
                    ),
                    'options' => array(
                        'required'  => true,
                        'label'     => 'user.admin.user.role.text'
                    )
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->class,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blackengine_user_user';
    }
}
