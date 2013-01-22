<?php

/*
 * This file is part of the Blackengine package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Blackroom\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Blackroom\Bundle\UserBundle\Form\EventListener\SetUserDataSubscriber;

class FrontUserType extends AbstractType
{
    private $class;

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
                'label'     => 'your.username'
            ))
            ->add('email', 'email', array(
                'label'     => 'your.password'
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