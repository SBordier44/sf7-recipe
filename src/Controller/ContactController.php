<?php

namespace App\Controller;

use App\Dto\ContactDto;
use App\Event\ContactRequestEvent;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(
        Request $request,
        MailerInterface $mailer,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $data = new ContactDto();

        $form = $this->createForm(ContactType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $dispatcher = $eventDispatcher->dispatch(new ContactRequestEvent($data));
                $this->addFlash('success', 'Votre message a bien été envoyé, merci !');
                return $this->redirectToRoute('contact');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash(
                    'danger',
                    'Une erreur est survenue lors de l\'envoi du message, veuillez réessayer plus tard.'
                );
            }
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
