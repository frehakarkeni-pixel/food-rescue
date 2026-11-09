<?php

namespace App\Controller;

use App\Entity\{Commerce, User};
use App\Form\{RegistrationClientType, RegistrationCommerceType};
use App\Service\VerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription/client', name: 'app_register_client')]
    public function client(Request $req, UserPasswordHasherInterface $h, EntityManagerInterface $em, VerificationService $vs): Response
    {
        $user = (new User())->setRole('ROLE_CLIENT');
        $form = $this->createForm(RegistrationClientType::class, $user)->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($h->hashPassword($user, $user->getPlainPassword()));
            $em->persist($user);
            $em->flush();

            if ($vs->send($user, 'EMAIL')) {
                $this->addFlash('success', 'Un code de validation a été envoyé à ' . $user->getEmail());
            } else {
                $this->addFlash('error', 'Compte créé, mais l\'e-mail n\'a pas pu être envoyé. Cliquez sur « Renvoyer un code ».');
            }

            $req->getSession()->set('verify_user_id', $user->getId());
            return $this->redirectToRoute('app_verify');
        }
        return $this->render('registration/client.html.twig', ['form' => $form]);
    }

    #[Route('/inscription/commerce', name: 'app_register_commerce')]
    public function commerce(Request $req, UserPasswordHasherInterface $h, EntityManagerInterface $em, VerificationService $vs): Response
    {
        $user = (new User())->setRole('ROLE_COMMERCE');
        $form = $this->createForm(RegistrationCommerceType::class, $user)->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($h->hashPassword($user, $user->getPlainPassword()));
            $commerce = (new Commerce())->setUser($user)
                ->setBusinessName($form->get('businessName')->getData())
                ->setBusinessType($form->get('businessType')->getData())
                ->setAddress($form->get('address')->getData())
                ->setCity($form->get('city')->getData());
            $em->wrapInTransaction(function () use ($em, $user, $commerce) {
                $em->persist($user);
                $em->persist($commerce);
            });

            if ($vs->send($user, 'EMAIL')) {
                $this->addFlash('success', 'Un code de validation a été envoyé à ' . $user->getEmail());
            } else {
                $this->addFlash('error', 'Compte créé, mais l\'e-mail n\'a pas pu être envoyé. Cliquez sur « Renvoyer un code ».');
            }

            $req->getSession()->set('verify_user_id', $user->getId());
            return $this->redirectToRoute('app_verify');
        }
        return $this->render('registration/commerce.html.twig', ['form' => $form]);
    }

    #[Route('/validation', name: 'app_verify')]
    public function verify(Request $req, EntityManagerInterface $em, VerificationService $vs): Response
    {
        $user = $em->find(User::class, $req->getSession()->get('verify_user_id') ?? 0);
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($req->isMethod('POST')) {
            if ($vs->verify($user, trim((string) $req->request->get('code')))) {
                $this->addFlash('success', 'Compte activé, vous pouvez vous connecter.');
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('error', 'Code invalide ou expiré.');
        }
        return $this->render('registration/verify.html.twig');
    }

    #[Route('/validation/renvoyer', name: 'app_verify_resend', methods: ['POST'])]
    public function resend(Request $req, EntityManagerInterface $em, VerificationService $vs): Response
    {
        $user = $em->find(User::class, $req->getSession()->get('verify_user_id') ?? 0);

        if ($user && !$user->isActive()) {
            if ($vs->send($user, 'EMAIL')) {
                $this->addFlash('success', 'Nouveau code envoyé à ' . $user->getEmail());
            } else {
                $this->addFlash('error', 'Échec de l\'envoi de l\'e-mail.');
            }
        }

        return $this->redirectToRoute('app_verify');
    }
}