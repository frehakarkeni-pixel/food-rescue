<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\{Length, NotBlank, Regex};

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('currentPassword', PasswordType::class, [
              'label' => 'Mot de passe actuel',
              'mapped' => false,
              'constraints' => [
                  new NotBlank(message: 'Saisissez votre mot de passe actuel.'),
                  new UserPassword(message: 'Le mot de passe actuel est incorrect.'),
              ],
          ])
          ->add('newPassword', RepeatedType::class, [
              'type' => PasswordType::class,
              'mapped' => false,
              'first_options' => ['label' => 'Nouveau mot de passe'],
              'second_options' => ['label' => 'Confirmer le nouveau mot de passe'],
              'invalid_message' => 'Les deux mots de passe ne correspondent pas.',
              'constraints' => [
                  new NotBlank(message: 'Le nouveau mot de passe est obligatoire.'),
                  new Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins 8 caractères.'),
                  new Regex('/^(?=.*[A-Za-z])(?=.*\d).+$/', 'Le mot de passe doit contenir au moins une lettre et un chiffre.'),
              ],
          ]);
    }
}