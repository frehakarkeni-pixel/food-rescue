<?php
namespace App\Controller;

use App\Entity\{Commerce, Offer};
use App\Form\OfferType;
use App\Repository\OfferRepository;
use App\Security\OfferVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commerce/offres')]
class CommerceOfferController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private OfferRepository $offers)
    {
    }

    private function currentCommerce(): Commerce
    {
        $commerce = $this->em->getRepository(Commerce::class)->findOneBy(['user' => $this->getUser()]);
        if (!$commerce) {
            throw $this->createAccessDeniedException('Aucun commerce associé à ce compte.');
        }

        return $commerce;
    }

    #[Route('', name: 'app_commerce_offers')]
    public function index(): Response
    {
        $commerce = $this->currentCommerce();

        return $this->render('commerce/offer/index.html.twig', [
            'commerce' => $commerce,
            'offers' => $this->offers->findByCommerce($commerce),
        ]);
    }

    #[Route('/nouvelle', name: 'app_commerce_offer_new')]
    public function new(Request $req): Response
    {
        $commerce = $this->currentCommerce();
        if (!$commerce->isApproved()) {
            $this->addFlash('error', 'Votre commerce doit être validé par un administrateur avant de publier des offres.');

            return $this->redirectToRoute('app_commerce_offers');
        }

        $offer = (new Offer())->setCommerce($commerce);
        $form = $this->createForm(OfferType::class, $offer, ['is_new' => true])->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $offer->setQuantityAvailable((int) $offer->getQuantityInitial());
            $offer->setStatus(Offer::STATUS_ACTIVE);
            $this->em->persist($offer);
            $this->em->flush();
            $this->addFlash('success', 'Offre publiée avec succès.');

            return $this->redirectToRoute('app_commerce_offers');
        }

        return $this->render('commerce/offer/form.html.twig', ['form' => $form, 'offer' => null]);
    }

    #[Route('/{id}/modifier', name: 'app_commerce_offer_edit', requirements: ['id' => '\d+'])]
    public function edit(#[MapEntity] Offer $offer, Request $req): Response
    {
        $this->denyAccessUnlessGranted(OfferVoter::EDIT, $offer);

        // quantité déjà réservée, calculée AVANT de lire le formulaire
        $sold = (int) $offer->getQuantityInitial() - $offer->getQuantityAvailable();
        $form = $this->createForm(OfferType::class, $offer, ['is_new' => false])->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $available = (int) $offer->getQuantityInitial() - $sold;
            if ($available < 0) {
                $form->get('quantityInitial')->addError(new FormError(
                    sprintf('La quantité ne peut pas être inférieure à la quantité déjà réservée (%d).', $sold)
                ));
            } else {
                $offer->setQuantityAvailable($available);
                if ($offer->getStatus() === Offer::STATUS_ACTIVE && $available === 0) {
                    $offer->setStatus(Offer::STATUS_SOLD_OUT);
                } elseif ($offer->getStatus() === Offer::STATUS_SOLD_OUT && $available > 0) {
                    $offer->setStatus(Offer::STATUS_ACTIVE);
                }
                $this->em->flush();
                $this->addFlash('success', 'Offre mise à jour.');

                return $this->redirectToRoute('app_commerce_offers');
            }
        }

        return $this->render('commerce/offer/form.html.twig', ['form' => $form, 'offer' => $offer]);
    }

    #[Route('/{id}/desactiver', name: 'app_commerce_offer_deactivate', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deactivate(#[MapEntity] Offer $offer, Request $req): Response
    {
        $this->denyAccessUnlessGranted(OfferVoter::EDIT, $offer);
        if (!$this->isCsrfTokenValid('offer_status' . $offer->getId(), (string) $req->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $offer->setStatus(Offer::STATUS_CANCELLED);
        $this->em->flush();
        $this->addFlash('success', 'Offre désactivée : elle n\'est plus réservable.');

        return $this->redirectToRoute('app_commerce_offers');
    }

    #[Route('/{id}/reactiver', name: 'app_commerce_offer_reactivate', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function reactivate(#[MapEntity] Offer $offer, Request $req): Response
    {
        $this->denyAccessUnlessGranted(OfferVoter::EDIT, $offer);
        if (!$this->isCsrfTokenValid('offer_status' . $offer->getId(), (string) $req->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $offer->setStatus($offer->getQuantityAvailable() > 0 ? Offer::STATUS_ACTIVE : Offer::STATUS_SOLD_OUT);
        $this->em->flush();
        $this->addFlash('success', 'Offre réactivée.');

        return $this->redirectToRoute('app_commerce_offers');
    }
}