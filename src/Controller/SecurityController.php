<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $u): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $u->getLastUsername(),
            'error' => $u->getLastAuthenticationError(),
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): void { throw new \LogicException('Géré par le firewall.'); }

    #[Route('/acces-refuse', name: 'app_access_denied')]
    public function denied(): Response
    {
        return $this->render('security/denied.html.twig', [], new Response('', 403));
    }
}