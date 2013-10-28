<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Form\EventListener;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class SetUserDataSubscriber
 *
 * @package Black\Bundle\UserBundle\Form\EventListener
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class SetUserDataSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $this->addPassword($data, $form);
    }

    /**
     * @param $data
     * @param $form
     */
    private function addPassword($data, $form)
    {
        if (null === $data) {
            $form
                ->add('rawPassword', 'repeated', array(
                    'type'              => 'password',
                    'invalid_message'   => 'black.user.form.event.user.rawPassword.invalid',
                    'position'          => array(
                        'after'         => 'email'
                    ),
                    'first_options'     => array(
                        'label'             => 'black.user.form.event.user.rawPassword.first.label',
                        'attr'              => array(
                            'placeholder'   => 'black.user.form.event.user.rawPassword.first.placeholder',
                            'class'         => 'span12'
                        )),
                    'second_options'    => array(
                        'label'             => 'black.user.form.event.user.rawPassword.second.label',
                        'attr'              => array(
                            'placeholder'   => 'black.user.form.event.user.rawPassword.second.placeholder',
                            'class'         => 'span12'
                        ))
                )
            );
        } else {

            $form
                ->add('oldPassword', 'password', array(
                        'label'             => 'black.user.form.event.user.oldPassword.label',
                        'position'          => array(
                            'after'         => 'email'
                        ),
                        'attr'              => array(
                            'placeholder'   => 'black.user.form.event.user.oldPassword.placeholder',
                            'class'         => 'span12'
                        )
                    )
                )
                ->add('rawPassword', 'repeated', array(
                    'type'              => 'password',
                    'required'          => false,
                    'invalid_message'   => 'black.user.form.event.user.rawPassword.invalid',
                    'position'          => array(
                        'after'         => 'oldPassword'
                    ),
                    'first_options'     => array(
                        'label'             => 'black.user.form.event.user.rawPassword.first.label',
                        'attr'              => array(
                            'class'         => 'span12'
                        )),
                    'second_options'    => array(
                        'label'             => 'black.user.form.event.user.rawPassword.second.label',
                        'attr'              => array(
                            'class'         => 'span12'
                        ))
                    )
            );
        }
    }
}
