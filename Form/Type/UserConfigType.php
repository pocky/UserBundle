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
 * Class UserConfigType
 *
 * @package Black\Bundle\UserBundle\Form\Type
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class UserConfigType extends AbstractType
{
    /**
     * @var string
     */
    private $class;

    /**
     * @param string $class The Person class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('name')
            ->add(
                $builder
                ->create('value', 'form', array(
                        'by_reference'  => false,
                        'label'         => 'black.page.type.config.label'
                    )
                )
                ->add('mail_register_header', 'text', array(
                        'label' => 'config.admin.mailConfig.mail.register.subject.text'
                    )
                )
                ->add('mail_register_text', 'textarea', array(
                        'label' => 'config.admin.mailConfig.mail.register.message.text'
                    )
                )
                ->add('mail_suspend_header', 'text', array(
                        'label' => 'config.admin.mailConfig.mail.suspend.subject.text'
                    )
                )
                ->add('mail_suspend_text', 'textarea', array(
                        'label' => 'config.admin.mailConfig.mail.suspend.message.text'
                    )
                )
                ->add('mail_lost_header', 'text', array(
                        'label' => 'config.admin.mailConfig.mail.lost.subject.text'
                    )
                )
                ->add('mail_lost_text', 'textarea', array(
                        'label' => 'config.admin.mailConfig.mail.lost.message.text'
                    )
                )
                ->add('mail_back_header', 'text', array(
                        'label' => 'config.admin.mailConfig.mail.back.subject.text'
                    )
                )
                ->add('mail_back_text', 'textarea', array(
                        'label' => 'config.admin.mailConfig.mail.back.message.text'
                    )
                )
                ->add('mail_byebye_header', 'text', array(
                        'label' => 'config.admin.mailConfig.mail.byebye.subject.text'
                    )
                )
                ->add('mail_byebye_text', 'textarea', array(
                        'label' => 'config.admin.mailConfig.mail.byebye.message.text'
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
                'intention'             => 'user_config_form',
                'translation_domain'    => 'form'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'black_user_config';
    }
}
