<?php
/*
 * This file is part of the ${FILE_HEADER_PACKAGE}.
 *
 * ${FILE_HEADER_COPYRIGHT}
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Black\Bundle\UserBundle\Infrastructure\DomainListener;

use Black\Component\User\UserDomainEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FlashListener
 */
class FlashSuccessListener implements EventSubscriberInterface
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var array
     */
    protected static $successMessages = [
        UserDomainEvents::USER_DOMAIN_ACTIVATED => 'application.user.flash.success.activated',
        UserDomainEvents::USER_DOMAIN_CREATED => 'application.user.flash.success.created',
        UserDomainEvents::USER_DOMAIN_REGISTERED => 'application.user.flash.success.registered',
        UserDomainEvents::USER_DOMAIN_REMOVED => 'application.user.flash.success.removed'
    ];

    /**
     * @param Session $session
     * @param TranslatorInterface $translator
     */
    public function __construct(Session $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            UserDomainEvents::USER_DOMAIN_ACTIVATED => 'addSuccessFlash',
            UserDomainEvents::USER_DOMAIN_CREATED => 'addSuccessFlash',
            UserDomainEvents::USER_DOMAIN_REGISTERED => 'addSuccessFlash',
            UserDomainEvents::USER_DOMAIN_REMOVED => 'addSuccessFlash'
        ];
    }

    /**
     * @param Event $event
     */
    public function addSuccessFlash(Event $event)
    {
        if (!isset(self::$successMessages[$event->getName()])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->session->getFlashBag()->add('success', $this->trans(self::$successMessages[$event->getName()]));
    }

    /**
     * @param $message
     * @param array $params
     * @return string
     */
    private function trans($message, array $params = array())
    {
        return $this->translator->trans($message, $params, 'flash');
    }
}