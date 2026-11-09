<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\VerificationCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class VerificationService
{
    public ?string $lastCode = null;   // AJOUT 1 : garde le dernier code pour l'afficher

    public function __construct(
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
    ) {
    }

    public function send(User $user, string $channel = 'EMAIL'): bool
    {
        $code = (string) random_int(100000, 999999);
        $this->lastCode = $code;       // AJOUT 2 : on mémorise le code

        $this->em->persist(new VerificationCode(
            $user, 'EMAIL', password_hash($code, PASSWORD_DEFAULT), new \DateTimeImmutable('+15 minutes')
        ));
        $this->em->flush();

        try {
            $this->mailer->send(
                (new Email())
                    ->from('charefzainab@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Food Rescue : votre code de validation')
                    ->text("Votre code : $code (valable 15 minutes)")
            );
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function verify(User $user, string $code): bool
{
    $code = preg_replace('/\D/', '', $code);   // garde seulement les chiffres
    $now = new \DateTimeImmutable();

    $codes = $this->em->getRepository(VerificationCode::class)
        ->findBy(['user' => $user, 'usedAt' => null], ['id' => 'DESC']);

    foreach ($codes as $vc) {
        if ($vc->getExpiresAt() >= $now && password_verify($code, $vc->getCodeHash())) {
            $vc->markUsed();
            $user->setIsActive(true)->setEmailVerifiedAt($now);
            $this->em->flush();
            return true;
        }
    }

    return false;
}
}