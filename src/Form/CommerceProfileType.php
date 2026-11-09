<?php
namespace App\Form;

use App\Entity\Commerce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, TextareaType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{Length, NotBlank, Regex};

class CommerceProfileType extends AbstractType
{
    public const DAYS = [
        'lundi' => 'Lundi', 'mardi' => 'Mardi', 'mercredi' => 'Mercredi', 'jeudi' => 'Jeudi',
        'vendredi' => 'Vendredi', 'samedi' => 'Samedi', 'dimanche' => 'Dimanche',
    ];

    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('businessName', TextType::class, [
              'label' => 'Nom du commerce',
              'constraints' => [new NotBlank(message: 'Le nom du commerce est obligatoire.'), new Length(max: 150)],
          ])
          ->add('businessType', ChoiceType::class, ['label' => 'Type', 'choices' => [
              'Restaurant' => 'Restaurant', 'Boulangerie' => 'Boulangerie', 'Café' => 'Cafe',
              'Pâtisserie' => 'Patisserie', 'Supermarché' => 'Supermarche', 'Autre' => 'Autre',
          ]])
          ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false, 'attr' => ['rows' => 3]])
          ->add('address', TextType::class, [
              'label' => 'Adresse',
              'constraints' => [new NotBlank(message: 'L\'adresse est obligatoire.')],
          ])
          ->add('city', TextType::class, [
              'label' => 'Ville',
              'constraints' => [new NotBlank(message: 'La ville est obligatoire.')],
          ])
          ->add('postalCode', TextType::class, ['label' => 'Code postal', 'required' => false, 'constraints' => [new Length(max: 20)]])
          ->add('phone', TextType::class, [
              'label' => 'Téléphone',
              'mapped' => false,
              'constraints' => [
                  new NotBlank(message: 'Le téléphone est obligatoire.'),
                  new Regex('/^\+?[0-9 ]{8,15}$/', 'Numéro de téléphone invalide.'),
              ],
          ]);

        foreach (self::DAYS as $key => $label) {
            $b->add('hours_' . $key, TextType::class, [
                'label' => $label,
                'mapped' => false,
                'required' => false,
                'attr' => ['placeholder' => '08:00-20:00 ou Fermé'],
                'constraints' => [new Regex(
                    '/^(ferm[ée]|([01]\d|2[0-3]):[0-5]\d\s*-\s*([01]\d|2[0-3]):[0-5]\d)$/iu',
                    'Format attendu : 08:00-20:00 ou Fermé.'
                )],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $r): void
    {
        $r->setDefaults(['data_class' => Commerce::class]);
    }
}