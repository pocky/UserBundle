<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <alexandre@lablackroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UpdatePasswordType
 */
class UpdatePasswordType extends AbstractType
{
    /**
     * @var type
     */
    protected $class;

    /**
     * @var
     */
    protected $name;

    /**
     * @param $class
     * @param $name
     */
    public function __construct($class, $name)
    {
        $this->class = $class;
        $this->name  = $name;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden')
            ->add('password', 'repeated', [
                    'type' => 'password',
                    'invalid_message' => 'black_user.form.password.invalid',
                    'first_options' => ['label' => 'black_user.form.password.password.label'],
                    'second_options' => ['label' => 'black_user.form.password.password_repeat.label'],
                    'options' => [
                        'required' => true,
                    ],
                ]
            );
    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => $this->class,
                'empty_data' => function (FormInterface $form) {
                    return new $this->class(
                        $form->get('id')->getData(),
                        $form->get('password')->getData()
                    );
                },
                'translation_domain' => 'form',
            ]
        );
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
