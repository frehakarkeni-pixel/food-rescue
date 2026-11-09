<?php
namespace App\Controller;

use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientOfferController extends AbstractController
{
    #[Route('/client/offres', name: 'app_client_offers')]
    public function index(OfferRepository $repo): Response
    {
        return $this->render('client/offer/index.html.twig', ['offers' => $repo->findCatalogue()]);
    }
}