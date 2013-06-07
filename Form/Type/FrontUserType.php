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

class FrontUserType extends AbstractType
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
        $subscriber = new SetUserDataSubscriber($builder->getFormFactory(), $this->class);
        $builder->addEventSubscriber($subscriber);

        $builder
            ->add('username', 'text', array(
                'label'     => 'user.your.username'
            ))
            ->add('email', 'email', array(
                'label'     => 'user.your.password.main'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
        ));
    }

    public function getName()
    {
        return 'blackengine_user_front_user';
    }
}