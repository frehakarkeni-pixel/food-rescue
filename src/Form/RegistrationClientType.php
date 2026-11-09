<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{EmailType, PasswordType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{Length, NotBlank, Regex};

class RegistrationClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('lastName', TextType::class, ['label' => 'Nom', 'constraints' => [new NotBlank()]])
          ->add('firstName', TextType::class, ['label' => 'Prénom', 'constraints' => [new NotBlank()]])
          ->add('email', EmailType::class)
          ->add('phone', TextType::class, ['required' => false, 'label' => 'Téléphone'])
          ->add('plainPassword', PasswordType::class, [
              'label' => 'Mot de passe',
              'constraints' => [
                  new NotBlank(),
                  new Length(min: 8, minMessage: '8 caractères minimum'),
                  new Regex('/^(?=.*[A-Z])(?=.*\d).+$/', 'Au moins une majuscule et un chiffre'),
              ],
          ]);
    }
    public function configureOptions(OptionsResolver $r): void { $r->setDefaults(['data_class' => User::class]); }
}