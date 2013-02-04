<?php

/*
 * This file is part of the Blackengine package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Blackroom\Bundle\UserBundle\Form\EventListener;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class SetUserDataSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $class;

    public function __construct(FormFactoryInterface $factory, $class)
    {
        $this->factory = $factory;
        $this->class = $class;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data) {
            return;
        }

        $this->addPassword($data, $form);
    }

    private function addPassword($data, $form)
    {
        if (!$data->getId()) {
            $form->add($this->factory->createNamed('rawPassword', 'repeated', null, array(
                'type'              => 'password',
                'invalid_message'   => 'user.your.password.error',
                'first_options'     => array('label' => 'user.your.password.main'),
                'second_options'    => array('label' => 'user.your.password.confirm')
            )));
        } else {
            $form->add($this->factory->createNamed('rawPassword', 'repeated', null, array(
                'type'              => 'password',
                'required'          => false,
                'invalid_message'   => 'user.your.password.error',
                'first_options'     => array('label' => 'user.your.password.main'),
                'second_options'    => array('label' => 'user.your.password.confirm')
            )));
        }
    }
}