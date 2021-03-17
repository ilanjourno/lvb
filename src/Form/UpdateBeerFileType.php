<?php

namespace App\Form;

use App\Entity\BeerFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateBeerFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom <span class="text-danger">*</span> :',
                'label_html' => true,
                'required' => true
            ])
            ->add('district', TextType::class, [
                'label' => 'Région <span class="text-danger">*</span> :',
                'label_html' => true,
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BeerFile::class,
        ]);
    }
}
