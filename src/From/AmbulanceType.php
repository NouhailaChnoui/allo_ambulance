<?php
namespace App\Form;

use App\Entity\Ambulance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AmbulanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('immatriculation', TextType::class, [
                'label' => 'Immatriculation',
                'attr' => ['class' => 'form-control']
            ])
            ->add('marque', TextType::class, [
                'label' => 'Marque',
                'attr' => ['class' => 'form-control']
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                'attr' => ['class' => 'form-control']
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Urgence' => 'urgence',
                    'Transport' => 'transport',
                    'Multi' => 'multi'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ambulance::class,
        ]);
    }
}
