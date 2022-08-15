<?php

namespace App\Form\Blog;

use App\Entity\Blog\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Infrastructure\Form\ImageType;
use App\Infrastructure\Form\RelatedCollectionType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('isPublished')
            ->add('content')
            ->add('image', ImageType::class)
            ->add('images', RelatedCollectionType::class, [
                'entry_type' => PostImageType::class,
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
