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
 * Class FrontUserType
 *
 * @package Black\Bundle\UserBundle\Form\Type
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class FrontUserType extends AbstractType
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @param string $class The User class name
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
        $subscriber = new SetUserDataSubscriber($builder->getFormFactory(), $this->class);
        $builder->addEventSubscriber($subscriber);

        $builder
            ->add('username', 'text', array(
                    'label'             => 'black.user.form.type.frontUser.username.label',
                    'position'          => 'first',
                    'attr'              => array(
                        'placeholder'   => 'black.user.form.type.frontUser.username.placeholder',
                        'pattern'       => '.{6,15}',
                        'class'         => 'span12'
                    )
                )
            )
            ->add('email', 'email', array(
                    'label'             => 'black.user.form.type.frontUser.email.label',
                    'position'          => array(
                        'after'         => 'username'
                    ),
                    'attr'              => array(
                        'placeholder'   => 'black.user.form.type.frontUser.email.plaholder',
                        'class'         => 'span12'
                    )
                )
            )
            ->add('save', 'submit', array(
                    'label'     => 'black.user.form.type.frontUser.save.label',
                    'attr'      => array(
                        'class'     => 'btn btn-success pull-right',
                        'style'     => 'margin-top: 10px'
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
                'data_class'            => $this->class,
                'intention'             => 'black_front',
                'translation_domain'    => 'form'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'black_user_front_user';
    }
}
