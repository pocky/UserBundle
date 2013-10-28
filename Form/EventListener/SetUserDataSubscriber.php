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

        if (!$data) {
            return;
        }

        $this->addPassword($data, $form);
    }

    /**
     * @param $data
     * @param $form
     */
    private function addPassword($data, $form)
    {
        if (null === $data && null === $data->getId()) {
            $form->add('rawPassword', 'repeated', array(
                'type'              => 'password',
                'invalid_message'   => 'user.admin.user.password.nomatch.text',
                'first_options'     => array('label' => 'user.admin.user.password.main.text',
                    'attr'              => array(
                        'class'         => 'span6'
                    )),
                'second_options'    => array('label' => 'user.admin.user.password.confirm.text',
                    'attr'              => array(
                        'class'         => 'span6'
                    ))
                )
            );
        } else {
            $form->add('rawPassword', 'repeated', array(
                'type'              => 'password',
                'required'          => false,
                'invalid_message'   => 'user.admin.user.password.nomatch.text',
                'first_options'     => array('label' => 'user.admin.user.password.main.text',
                    'attr'              => array(
                        'class'         => 'span6'
                    )),
                'second_options'    => array('label' => 'user.admin.user.password.confirm.text',
                    'attr'              => array(
                        'class'         => 'span6'
                    ))
                )
            );
        }
    }
}
