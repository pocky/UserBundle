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
     * @var
     */
    protected $userType;

    /**
     * @param string $class The User class name
     */
    public function __construct($class, $userType)
    {
        $this->class    = $class;
        $this->userType = $userType;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', $this->userType)
            ->add('terms', 'checkbox', array(
                    'property_path' => 'termsAccepted',
                    'label'         => 'user.www.register.terms.text',
                    'attr'          => array(
                        'placeholder'   => 'user.www.register.terms.text',
                        'class'         => 'checkLabel'
                    ),
                    'label_attr'          => array(
                        'class'         => 'checkLabel'
                    )
                )
            )
            ->add('save', 'submit', array(
                    'label'     => 'black.user.form.type.register.save.label',
                    'attr'      => array(
                        'class'     => 'btn btn-success span5 pull-right'
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
        return 'black_user_register';
    }
}
