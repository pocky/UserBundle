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

/**
 * Class RegisterType
 *
 * @package Black\Bundle\UserBundle\Form\Type
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class RegisterType extends AbstractType
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
            )
            ->add('terms', 'checkbox', array(
                    'property_path' => 'termsAccepted',
                    'label'         => 'user.www.register.terms.text'
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
                'intention'             => 'black_register',
                'translation_domain'    => 'form'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blackengine_user_register';
    }
}
