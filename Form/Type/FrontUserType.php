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
                    'label'             => 'user.www.register.username.text',
                    'attr'              => array(
                        'placeholder'   => 'user.www.register.username.text',
                        'pattern'       => '.{6,15}',
                        'class'         => 'span12'
                    )
                )
            )
            ->add('email', 'email', array(
                    'label'             => 'user.www.register.email.text',
                    'attr'              => array(
                        'placeholder'   => 'user.www.register.email.text',
                        'class'         => 'span12'
                    )
                )
            )
            ->add('rawPassword', 'repeated', array(
                    'type'              => 'password',
                    'invalid_message'   => 'user.www.register.password.not.match.text',
                    'first_options'     => array(
                        'label'             => 'user.www.register.password.main.text',
                        'attr'              => array(
                            'placeholder'   => 'user.www.register.password.main.text',
                            'class'         => 'span12'
                        )),
                    'second_options'    => array(
                        'label'             => 'user.www.register.password.confirm.text',
                        'attr'              => array(
                            'placeholder'   => 'user.www.register.password.confirm.text',
                            'class'         => 'span12'
                        ))
                )
            )
            ->add('save', 'submit', array(
                    'label'     => 'black.user.form.type.frontUser.save.label',
                    'attr'      => array(
                        'class'     => 'buttonL bBlue floatL mb10 mr10'
                    )
                )
            )
            ->add('reset', 'reset', array(
                    'label'     => 'black.user.form.type.frontUser.reset.label',
                    'attr'      => array(
                        'class'     => 'buttonL bDefault floatL mb10 mr10'
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
