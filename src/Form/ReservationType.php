<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Resource;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // https://symfony.com/doc/current/reference/forms/types.html
        $builder
            ->add('start_time', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date et heure de début',
            ])
            ->add('end_time', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date et heure de fin',
            ])
            ->add('purpose', TextareaType::class, [
                'label' => 'Motif de la réservation',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'pending',
                    'Approuvée' => 'approved',
                    'Rejeté' => 'rejected',
                    'Annulée' => 'cancelled',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('resource', EntityType::class, [
                'class' => Resource::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir une ressource',
                'label' => 'Ressource',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('reservedBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'placeholder' => 'Choisir un utilisateur',
                'label' => 'Réservé par',
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
