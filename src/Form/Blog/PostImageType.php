<?php

namespace App\Form\Blog;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Infrastructure\Form\ImageType;
use App\Entity\Blog\PostImage;

class PostImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', ImageType::class, [
            'allow_delete' => false,
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostImage::class,
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'collection_file';
    }
}
