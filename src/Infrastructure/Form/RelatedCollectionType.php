<?php

namespace App\Infrastructure\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RelatedCollectionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'by_reference' => false,
            'allow_add' => true,
            'allow_delete' => true,
        ]);
    }
    
    public function getParent(): string
    {
        return CollectionType::class;
    }
}
