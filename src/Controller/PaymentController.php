<?php

namespace App\Controller;

use App\Form\PaymentType;
use App\Entity\Transaction;
use App\Service\StripeService;
use Symfony\Component\Mime\Email;
use App\Repository\OffreRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Stripe\Exception\UnexpectedValueException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment')]
    public function index(
        StripeService $stripeService,
        TransactionRepository $transactions,
        Request $request,
        MailerInterface $mailer,
        OffreRepository $offres,
        ClientRepository $clients,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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
                ->subject('Merci de procéder au paiment de votre offre')
                ->html('<div style="background-color: #f4f4f4; padding: 20px; text-align:center;">
                        <h1>Bonjour'.$clients->getNomComplet().'</h1><br><br>
                        <p>Voici le lien poour effectuer le règlement de votre offre :</p><br>
                        <a href="'.$link.'" target="_blank">Payer</a><br>
                        <hr>
                        <p>Ce lien est valable pour une durée limitée</p><br></div>
                    ');

            $mailer->send($email);

            $transactions = new Transaction();
            $transactions->setClient($data['client'])
                        ->setMontant($offre->getMontant())
                        ->setStatut('En attente')
                        ;
            $em->persist($transactions);
            $em->flush();
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
        $stripe = new \Stripe\StripeClient('sk_test_...');
        $endpoint_secret = 'whsec_78280158adcdc73638a532a42fa6ca5e9840cc4de0df4fe2b10ce00b74c7781c';
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
            
        try {
              $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
        } catch(UnexpectedValueException $e) {
            // Invalid payload
        http_response_code(400);
        exit();
        } catch(SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }
        
        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
            $paymentIntent = $event->data->object;
            // ... handle other event types
            default:
            echo 'Received unknown event type ' . $event->type;
        }
        
        http_response_code(200);
    }
    #[Route('/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('payment/cancel.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}
