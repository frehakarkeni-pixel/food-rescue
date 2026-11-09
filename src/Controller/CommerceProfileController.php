<?php
namespace App\Controller;

use App\Entity\Commerce;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\CommerceProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

class CommerceProfileController extends AbstractController
{
    #[Route('/commerce/profil', name: 'app_commerce_profile')]
    public function profile(Request $req, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $commerce = $em->getRepository(Commerce::class)->findOneBy(['user' => $user]);
        if (!$commerce) {
            throw $this->createAccessDeniedException('Aucun commerce associé à ce compte.');
        }

        $form = $this->createForm(CommerceProfileType::class, $commerce);
        $form->get('phone')->setData($user->getPhone());
        $hours = $commerce->getOpeningHours() ?? [];
        foreach (array_keys(CommerceProfileType::DAYS) as $day) {
            $form->get('hours_' . $day)->setData($hours[$day] ?? null);
        }
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPhone(preg_replace('/\s+/', ' ', trim((string) $form->get('phone')->getData())));

            $newHours = [];
            foreach (array_keys(CommerceProfileType::DAYS) as $day) {
                $value = trim((string) $form->get('hours_' . $day)->getData());
                if ($value !== '') {
                    $newHours[$day] = $value;
                }
            }
            $commerce->setOpeningHours($newHours);

            $em->flush();
            $this->addFlash('success', 'Le profil du commerce a bien été enregistré.');

            return $this->redirectToRoute('app_commerce_profile');
        }

        return $this->render('commerce/profile.html.twig', [
            'form' => $form,
            'days' => CommerceProfileType::DAYS,
            'passwordForm' => $this->createForm(ChangePasswordType::class, null, [
                'action' => $this->generateUrl('app_change_password'),
            ]),
        ]);
    }
}