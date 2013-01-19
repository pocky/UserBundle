<?php

/*
 * This file is part of the Blackengine package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blackroom\Bundle\UserBundle\Mailer;

use Blackroom\Bundle\UserBundle\Model\UserInterface;
use Blackroom\Bundle\EngineBundle\Model\Config\ConfigManagerInterface;

class Mailer
{
    protected $mailer;
    protected $twig;
    protected $parameters;
    protected $config;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, ConfigManagerInterface $manager, array $parameters)
    {
        $this->mailer       = $mailer;
        $this->twig         = $twig;
        $this->manager      = $manager;
        $this->parameters   = $parameters;
    }

    public function sendRegisterMessage(UserInterface $user, $token)
    {
        $template   = $this->parameters['template']['register'];
        $property   = $this->getRegisterProperty();

        $context    = array(
            'subject'   => $property['mail_register_header'] ? $property['mail_register_header'] : 'Confirm your registration from our website',
            'message'   => $property['mail_register_text'] ? $property['mail_register_text'] : 'For confirm your registration, click on the link:',
            'token'     => $token,
            'user'      => $user,
            'footer'    => $property['mail_footer_note']
        );

        $author = $property['mail_root'];

        $this->sendMessage($template, $context, $author, $user->getEmail());
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $template   = $this->twig->loadTemplate($templateName);
        $subject    = $template->renderBlock('subject', $context);
        $textBody   = $template->renderBlock('body_text', $context);
        $htmlBody   = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($fromEmail)
                ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                    ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }

    protected function getRegisterProperty()
    {
        $property = $this->manager->findPropertyByName('Mail');

        return $property->getValue();
    }

}