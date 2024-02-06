<?php

namespace App\Controller;

use App\Form\PaymentType;
use App\Service\StripeService;
use Symfony\Component\Mime\Email;
use App\Repository\OffreRepository;
use App\Repository\ClientRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment')]
    public function index(
        StripeService $stripeService,
        TransactionRepository $transactions,
        Request $request,
        MailerInterface $mailer,
        OffreRepository $offres,
        ClientRepository $clients
    ): Response
    {
        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();
            $offre = $offres->findOneBy(['id' => $data['offre']->getId()]);
            $clientEmail = $clients->findOneBy(['id' => $data['client']->getId()])->getEmail();
            $apiKey = $this->getParameter('STRIPE_API_KEY_SECRET');
            $link = $stripeService->makePayment(
                $apiKey,
                $offre->getMontant(),
                $offre->getTitre(),
                $clientEmail
            );
            $email = (new Email())
                ->from('hello@tinycrm.app')
                ->to($clientEmail)
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Merci de procéder au paiment de votre offre')
                ->html('<div style="background-color: #f4f4f4; padding: 20px; text-align:center;">
                        <h1>Bonjour</h1><br><br>
                        <p>Voici le lien poour effectuer le règlement de votre ofrre :</p><br>
                        <a href="$link">Payer</a><br>
                        <hr>
                        <p>Ce lien est valable pour une durée limitée</p><br></div>
                    ');

        $mailer->send($email);

        
        }


        
        return $this->render('payment/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/success', name: 'payment_success')]
        public function success(): Response
        {
            return $this->render('payment/success.html.twig', [
                'controller_name' => 'PaymentController',
            ]);
        }

    #[Route('/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('payment/cancel.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}
