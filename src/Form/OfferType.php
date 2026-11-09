<?php
namespace App\Form;

use App\Entity\Category;
use App\Entity\Offer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{DateType, IntegerType, NumberType, TextareaType, TextType, TimeType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('name', TextType::class, ['label' => 'Nom du produit'])
          ->add('description', TextareaType::class, ['label' => 'Description', 'required' => true, 'attr' => ['rows' => 3]])
          ->add('category', EntityType::class, [
              'class' => Category::class,
              'label' => 'Catégorie',
              'placeholder' => '— Choisir —',
              'query_builder' => fn (EntityRepository $r) => $r->createQueryBuilder('c')->where('c.isActive = 1')->orderBy('c.name'),
          ])
          ->add('quantityInitial', IntegerType::class, ['label' => 'Quantité', 'attr' => ['min' => 1]])
          ->add('originalPrice', NumberType::class, [
              'label' => 'Prix initial (TND)', 'scale' => 2, 'html5' => true, 'attr' => ['step' => '0.01', 'min' => '0'],
          ])
          ->add('discountedPrice', NumberType::class, [
              'label' => 'Prix réduit (TND)', 'scale' => 2, 'html5' => true, 'attr' => ['step' => '0.01', 'min' => '0'],
          ])
          ->add('availableDate', DateType::class, [
              'label' => 'Date de disponibilité', 'widget' => 'single_text', 'input' => 'datetime_immutable',
              'constraints' => $o['is_new'] ? [new GreaterThanOrEqual('today', message: 'La date ne peut pas être dans le passé.')] : [],
          ])
          ->add('startTime', TimeType::class, [
              'label' => 'Disponible à partir de', 'widget' => 'single_text', 'input' => 'datetime_immutable',
          ])
          ->add('pickupDeadline', TimeType::class, [
              'label' => 'Retrait possible jusqu\'à', 'widget' => 'single_text', 'input' => 'datetime_immutable',
          ])
          ->add('expiryDate', DateType::class, [
              'label' => 'Date d\'expiration du produit (facultatif)', 'required' => false,
              'widget' => 'single_text', 'input' => 'datetime_immutable',
          ]);
    }

    public function configureOptions(OptionsResolver $r): void
    {
        $r->setDefaults(['data_class' => Offer::class, 'is_new' => true]);
        $r->setAllowedTypes('is_new', 'bool');
    }
}