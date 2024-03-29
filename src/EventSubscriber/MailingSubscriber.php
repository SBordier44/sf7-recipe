<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

readonly class MailingSubscriber implements EventSubscriberInterface
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $to = match ($event->data->service) {
            'service_commercial' => ['service-commercial@demo.fr', 'Service Commercial'],
            'service_technique' => ['service-technique@demo.fr', 'Service Technique'],
            default => ['service-client@demo.fr', 'Service Client'],
        };

        $email = (new TemplatedEmail())
            ->from(new Address($event->data->email, $event->data->name))
            ->to(new Address($to[0], $to[1]))
            ->subject('Demande de contact')
            ->htmlTemplate('emails/contact.html.twig')
            ->context(['data' => $event->data]);

        $this->mailer->send($email);
    }

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $user = $event->getAuthenticatedToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $email = (new Email())
            ->from(new Address('support@demo.fr', 'Support'))
            ->to(new Address($user->getEmail(), $user->getEmail()))
            ->subject('Connexion')
            ->text('Bonjour ' . $user->getEmail() . ', vous êtes connecté.');

        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
        ];
    }
}
