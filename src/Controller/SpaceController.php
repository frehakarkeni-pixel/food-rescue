<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SpaceController extends AbstractController
{
    #[Route('/client', name: 'app_client_home')]
    public function client(): Response { return $this->render('space/client.html.twig'); }

    #[Route('/commerce', name: 'app_commerce_home')]
    public function commerce(): Response { return $this->render('space/commerce.html.twig'); }

    #[Route('/admin', name: 'app_admin_dashboard')]
    public function admin(): Response { return $this->render('space/admin.html.twig'); }
}