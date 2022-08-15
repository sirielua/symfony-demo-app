<?php

namespace App\Infrastructure\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints as Assert;

class ImageType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'allow_delete' => true,
            'asset_helper' => false,
            'download_link' => null,
            'download_uri' => false,
            'error_bubbling' => false,
            'constraints' => [
                new Assert\File(['mimeTypes' => ['image/*']]),
            ],
        ]);
    }
    
    public function getParent(): string
    {
        return VichImageType::class;
    }
}
