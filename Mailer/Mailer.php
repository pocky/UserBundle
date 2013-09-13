<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Mailer;

use Black\Bundle\UserBundle\Model\UserInterface;
use Black\Bundle\ConfigBundle\Model\ConfigManagerInterface;

/**
 * Class Mailer
 *
 * @package Black\Bundle\UserBundle\Mailer
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var
     */
    protected $config;

    /**
     * @param \Swift_Mailer          $mailer
     * @param \Twig_Environment      $twig
     * @param ConfigManagerInterface $manager
     * @param array                  $parameters
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig,
        ConfigManagerInterface $manager,
        array $parameters
    ) {
        $this->mailer       = $mailer;
        $this->twig         = $twig;
        $this->manager      = $manager;
        $this->parameters   = $parameters;
    }

    /**
     * @param UserInterface $user
     * @param               $token
     */
    public function sendRegisterMessage(UserInterface $user, $token)
    {
        $template   = $this->parameters['template']['register'];
        $property   = $this->getRegisterProperty();

        $context    = array(
            'subject'   => $property['mail_register_header'] ?
                            $property['mail_register_header'] : 'user.mailer.register.subject',
            'message'   => $property['mail_register_text'] ?
                            $property['mail_register_text'] : 'user.mailer.register.message',
            'token'     => $token,
            'user'      => $user,
            'footer'    => $property['mail_footer_note']
        );

        $author = $property['mail_root'];

        $this->sendMessage($template, $context, $author, $user->getEmail());
    }

    /**
     * @param UserInterface $user
     * @param               $token
     */
    public function sendSuspendMessage(UserInterface $user, $token)
    {
        $template   = $this->parameters['template']['suspend'];
        $property   = $this->getRegisterProperty();

        $context    = array(
            'subject'   => $property['mail_suspend_header'] ?
                            $property['mail_suspend_header'] : 'user.mailer.suspend.subject',
            'message'   => $property['mail_suspend_text'] ?
                            $property['mail_suspend_text'] : 'user.mailer.suspend.message',
            'token'     => $token,
            'user'      => $user,
            'footer'    => $property['mail_footer_note']
        );

        $author = $property['mail_root'];

        $this->sendMessage($template, $context, $author, $user->getEmail());
    }

    /**
     * @param UserInterface $user
     * @param               $token
     */
    public function sendLostPasswordMessage(UserInterface $user, $token)
    {
        $template   = $this->parameters['template']['lost'];
        $property   = $this->getRegisterProperty();

        $context    = array(
            'subject'   => $property['mail_lost_header'] ?
                            $property['mail_lost_header'] : 'user.mailer.lost.subject',
            'message'   => $property['mail_lost_text'] ?
                            $property['mail_lost_text'] : 'user.mailer.lost.message',
            'token'     => $token,
            'user'      => $user,
            'footer'    => $property['mail_footer_note']
        );

        $author = $property['mail_root'];

        $this->sendMessage($template, $context, $author, $user->getEmail());
    }

    /**
     * @param UserInterface $user
     * @param               $password
     */
    public function sendBackPasswordMessage(UserInterface $user, $password)
    {
        $template   = $this->parameters['template']['back'];
        $property   = $this->getRegisterProperty();

        $context    = array(
            'subject'   => $property['mail_back_header'] ?
                            $property['mail_back_header'] : 'user.mailer.back.subject',
            'message'   => $property['mail_back_text'] ?
                            $property['mail_back_text'] : 'user.mailer.back.message',
            'user'      => $user,
            'password'  => $password,
            'footer'    => $property['mail_footer_note']
        );

        $author = $property['mail_root'];

        $this->sendMessage($template, $context, $author, $user->getEmail());
    }

    /**
     * @param UserInterface $user
     */
    public function sendGoodByeMessage(UserInterface $user)
    {
        $template   = $this->parameters['template']['byebye'];
        $property   = $this->getRegisterProperty();

        $context    = array(
            'subject'   => $property['mail_byebye_header'] ?
                            $property['mail_byebye_header'] : 'user.mailer.bye.subject',
            'message'   => $property['mail_byebye_text'] ?
                            $property['mail_byebye_text'] : 'user.mailer.bye.message',
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

    /**
     * @return mixed
     */
    protected function getRegisterProperty()
    {
        $property = $this->manager->findPropertyByName('Mail');

        return $property->getValue();
    }
}
