<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class EmailService
{
    /**
     * @var \Swift_Mailer
     */
    protected $swiftMailer;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * EmailService constructor.
     * @param \Swift_Mailer $mailer
     * @param EngineInterface $templating
     * @param LoggerInterface $logger
     */
    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, LoggerInterface $logger)
    {
        $this->swiftMailer = $mailer;
        $this->templating = $templating;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     * @param string $subject
     * @param string $templatePath
     * @param array $templateParams
     */
    public function sendEmailToUser(User $user, string $subject, string $templatePath, array $templateParams = array())
    {
        try {
            $template = $this->templating->render($templatePath, $templateParams);

            $message = (new \Swift_Message($subject))
                ->setFrom('max.kytsenyuk@gmail.com')
                ->setTo($user->getEmail())
                ->setBody($template, 'text/html');

            $this->swiftMailer->send($message);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage() ?? 'Unknown Error');
        }
    }
}