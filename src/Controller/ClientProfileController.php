<?php
namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

class ClientProfileController extends AbstractController
{
    #[Route('/client/profil', name: 'app_client_profile')]
    public function profile(Request $req, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserProfileType::class, $this->getUser())->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Vos informations ont bien été enregistrées.');

            return $this->redirectToRoute('app_client_profile');
        }

        if ($form->isSubmitted()) {
            $em->refresh($this->getUser());
        }

        return $this->render('client/profile.html.twig', [
            'form' => $form,
            'passwordForm' => $this->createForm(ChangePasswordType::class, null, [
                'action' => $this->generateUrl('app_change_password'),
            ]),
        ]);
    }
}