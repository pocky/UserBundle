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

class UserType extends AbstractType
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
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('isActive', 'checkbox', array(
                'label'     => 'Is active?',
                'required'  => false
            ))
            ->add('isRoot', 'checkbox', array(
                'label'     => 'Is superadmin?',
                'required'  => false
            ))
            ->add('locked', 'checkbox', array(
                'label'     => 'Is locked?',
                'required'  => false
            ))
            ->add('person', 'document', array(
                'class'         => 'Blackroom\Bundle\ConnectBundle\Document\Person',
                'property'      => 'name',
                'empty_value'   => 'Associate a person',
                'required'      => false
            ))
            ->add('roles', 'collection', array(
                'type'          => 'text',
                'required'      => false,
                'label'         => 'Roles',
                'allow_add'     => true,
                'allow_delete'  => true,
                'attr'          => array(
                    'class' => 'roles-collection',
                    'add'   => 'add-another-role'
                ),
                'options' => array(
                    'required'  => true,
                    'label'     => 'Role'
                )
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
        return 'blackengine_user_user';
    }
}