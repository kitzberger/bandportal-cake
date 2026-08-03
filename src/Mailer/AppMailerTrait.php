<?php

namespace App\Mailer;

use Cake\Mailer\Mailer;

/**
 * Shared mail-sending logic used by controllers and CLI commands.
 */
trait AppMailerTrait
{
    /**
     * @param string $subject Mail subject.
     * @param string|null $message Pre-rendered HTML body, or null to use a template.
     * @param string $to Recipient address.
     * @param string $template Template name (when $message is null).
     * @param array $viewVars View variables for the template.
     * @return void
     * @throws \Exception When no default from address is configured.
     */
    protected function sendMail($subject, $message, $to, $template = 'default', $viewVars = [])
    {
        $mailer = new Mailer('default');

        $from = $mailer->getMessage()->getFrom();
        if (empty($from)) {
            throw new \Exception('Missing default from address in config!');
        }

        if ($message !== null) {
            $mailer->getMessage()->setBodyHtml($message);
        } else {
            $mailer->viewBuilder()->setTemplate($template);
            $mailer->setViewVars($viewVars);
        }

        $mailer
            ->setEmailFormat('html')
            ->setTo($to)
            ->setSubject($subject)
            ->deliver();
    }
}
