<?php
namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ChangePasswordController extends AbstractController
{
    #[Route('/mot-de-passe', name: 'app_change_password')]
    public function change(Request $req, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class)->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($hasher->hashPassword($user, (string) $form->get('newPassword')->getData()));
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été modifié.');

            $route = match (true) {
                $this->isGranted('ROLE_ADMIN') => 'app_admin_profile',
                $this->isGranted('ROLE_COMMERCE') => 'app_commerce_profile',
                default => 'app_client_profile',
            };

            return $this->redirectToRoute($route);
        }

        return $this->render('security/change_password.html.twig', ['form' => $form]);
    }
}