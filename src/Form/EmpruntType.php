<?php

namespace App\Form;

use App\Entity\Livre;
use App\Entity\Abonne;
use App\Entity\Emprunt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class EmpruntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortie', DateType::class, [
                "widget" => "single_text",
                "label" => "Dated'emprunt"
            ])
            ->add('retour', DateType::class, [
                "widget" => "single_text",
                "label" => "Date de retour",
                "required" => false
            ])
            ->add('abonne', EntityType::class, [
                "class" => Abonne::class,
                "choice_label"=> "pseudo",
                "label" => "Abonne",
                "placeholder"=> ""
            ])
            ->add('livre', EntityType::class, [
                "class" => Livre::class,
                "choice_label"=> "titre",
                "placeholder"=> ""
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emprunt::class,
        ]);
    }
}
