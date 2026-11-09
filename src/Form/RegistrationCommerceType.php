<?php
namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\{NotBlank};

class RegistrationCommerceType extends RegistrationClientType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        parent::buildForm($b, $o);
        $b->get('phone')->setRequired(true); // téléphone obligatoire pour un commerce
        $b->add('businessName', TextType::class, ['mapped' => false, 'label' => 'Nom du commerce', 'constraints' => [new NotBlank()]])
          ->add('businessType', ChoiceType::class, ['mapped' => false, 'label' => 'Type',
              'choices' => ['Restaurant'=>'Restaurant','Boulangerie'=>'Boulangerie','Café'=>'Cafe',
                            'Pâtisserie'=>'Patisserie','Supermarché'=>'Supermarche','Autre'=>'Autre']])
          ->add('address', TextType::class, ['mapped' => false, 'label' => 'Adresse', 'constraints' => [new NotBlank()]])
          ->add('city', TextType::class, ['mapped' => false, 'label' => 'Ville', 'constraints' => [new NotBlank()]]);
    }
}