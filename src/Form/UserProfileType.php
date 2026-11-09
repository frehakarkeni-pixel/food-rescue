<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{EmailType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{Length, NotBlank, Regex};

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('lastName', TextType::class, [
              'label' => 'Nom',
              'constraints' => [new NotBlank(message: 'Le nom est obligatoire.'), new Length(max: 100)],
          ])
          ->add('firstName', TextType::class, [
              'label' => 'Prénom',
              'constraints' => [new NotBlank(message: 'Le prénom est obligatoire.'), new Length(max: 100)],
          ])
          ->add('email', EmailType::class, [
              'label' => 'E-mail',
              'constraints' => [new NotBlank(message: 'L\'e-mail est obligatoire.')],
          ])
          ->add('phone', TextType::class, [
              'label' => 'Téléphone',
              'required' => false,
              'constraints' => [new Regex('/^\+?[0-9 ]{8,15}$/', 'Numéro de téléphone invalide.')],
          ]);
    }

    public function configureOptions(OptionsResolver $r): void
    {
        $r->setDefaults(['data_class' => User::class]);
    }
}