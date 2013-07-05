<?php

/*
 * This file is part of the Blackengine package.
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

class RegisterType extends AbstractType
{
    protected $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                'text',
                array(
                    'label'             => 'user.www.register.username.text',
                    'attr'              => array(
                        'class'         => 'span6',
                        'pattern'       => '.{6,15}'
                    )
                )
            )
            ->add(
                'email',
                'email',
                array(
                    'label'             => 'user.www.register.email.text',
                    'attr'              => array(
                        'class'         => 'span6'
                    )
                )
            )
            ->add(
                'rawPassword',
                'repeated',
                array(
                'type'              => 'password',
                'invalid_message'   => 'user.www.register.password.not.match.text',
                'first_options'     => array('label' => 'user.www.register.password.main.text',
                    'attr'              => array(
                        'class'         => 'span6'
                    )),
                'second_options'    => array('label' => 'user.www.register.password.confirm.text',
                    'attr'              => array(
                        'class'         => 'span6'
                    ))
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->class,
            )
        );
    }

    public function getName()
    {
        return 'blackengine_user_register';
    }
}
