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

class UserType extends AbstractType
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
        //$subscriber = new SetUserDataSubscriber($builder->getFormFactory(), $this->class);
        //$builder->addEventSubscriber($subscriber);

        $builder
            ->add('username', 'text', array(
                'label'     => 'user.admin.user.username.text'
            ))
            ->add('email', 'email', array(
                'label'     => 'user.admin.user.email.text'
            ))
            ->add('isActive', 'checkbox', array(
                'label'     => 'user.admin.user.isActive.text',
                'required'  => false
            ))
            ->add('isRoot', 'checkbox', array(
                'label'     => 'user.admin.user.isRoot.text',
                'required'  => false
            ))
            ->add('locked', 'checkbox', array(
                'label'     => 'user.admin.user.isLocked.text',
                'required'  => false
            ))
            ->add('person', 'document', array(
                'class'         => 'ActivCompanyERPBundle:Person',
                'property'      => 'name',
                'label'         => 'user.admin.user.person.text',
                'empty_value'   => 'user.admin.user.person.input',
                'required'      => false
            ))
            ->add('roles', 'collection', array(
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