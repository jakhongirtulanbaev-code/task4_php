<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private string $mailFrom
    ) {
    }

    public function sendConfirmationEmail(User $user): void
    {
        $confirmationUrl = $this->urlGenerator->generate(
            'app_confirm',
            ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new Email())
            ->from($this->mailFrom)
            ->to($user->getEmail())
            ->subject('Confirm your account')
            ->html(sprintf(
                '<p>Hello %s,</p><p>Please confirm your account by clicking the link below:</p><p><a href="%s">%s</a></p>',
                htmlspecialchars($user->getName()),
                $confirmationUrl,
                $confirmationUrl
            ));

        // Async sending (in real app, use Messenger component)
        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            // Log error for debugging
            error_log('Email sending failed: ' . $e->getMessage());
            // Don't throw in production, but log it
            if ($_ENV['APP_ENV'] === 'dev') {
                throw $e;
            }
        }
    }
}

