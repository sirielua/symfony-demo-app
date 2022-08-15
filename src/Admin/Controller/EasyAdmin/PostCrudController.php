<?php

namespace App\Admin\Controller\EasyAdmin;

use App\Entity\Blog\Post;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field as EaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter as EaFilter;
use App\Admin\EasyAdmin\Filter\DateFilter as DateFilter;

use App\Infrastructure\Form\ImageType;
use App\Form\Blog\PostImageType;

class PostCrudController extends AbstractCrudController
{
    private $users;
    
    public function __construct(\App\Repository\UserRepository $users)
    {
        $this->users = $users;
    }
    
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }
    
    public function configureActions(Actions $actions): Actions {
        return parent::configureActions($actions)
            ->add('index', 'detail');
    }

    public function configureFields(string $pageName): iterable
    {
        yield EaField\IdField::new('id')
            ->hideOnForm()
        ;
        
        yield EaField\TextField::new('title');
        
        yield EaField\AssociationField::new('author', 'Author')
            ->setFormTypeOption('query_builder', $this->users->getSortedQueryBuilder())
            ->setFormTypeOption('placeholder', '')
        ;
        
        yield EaField\TextField::new('slug')
            ->hideOnForm()
        ;
        
        yield EaField\TextEditorField::new('content')
            ->hideOnIndex()
        ;
        
        yield EaField\Field::new('image')
            ->setFormType(ImageType::class)
            ->onlyOnForms()
        ;
        
        yield EaField\Field::new('image')
            ->setTemplatePath('@admin/easyadmin/crud/field/image_preview.html.twig')
            ->onlyOnDetail()
        ;
        
        yield EaField\CollectionField::new('images')
            ->setEntryType(PostImageType::class)
            ->onlyOnForms()
        ;
        
        yield EaField\CollectionField::new('images')
            ->setTemplatePath('@admin/easyadmin/crud/field/images_preview.html.twig')
            ->onlyOnDetail()
        ;
        
        yield EaField\TextField::new('imageName')
            ->hideOnForm()
        ;
        
        yield EaField\BooleanField::new('isPublished')
            ->setFormTypeOption('confirmable', true)
            ->onlyOnIndex()
        ;
        
        yield EaField\BooleanField::new('isPublished')
            ->hideOnIndex()
        ;
        
        yield EaField\DateField::new('createdAt')
            ->hideOnForm()
        ;
        
        yield EaField\DateField::new('updatedAt')
            ->hideOnForm()
        ;
    }
    
    public function configureFilters(Filters $filters): Filters
    {        
        return $filters
            ->add(EaFilter\TextFilter::new('title', 'Title'))
            ->add(EaFilter\EntityFilter::new('author', 'Author')
                ->setFormTypeOption('value_type_options.choice_label', 'displayName')
                ->setFormTypeOption('value_type_options.query_builder', $this->users->getSortedQueryBuilder())
                ->setFormTypeOption('value_type_options.placeholder', 'label.form.empty_value')
            )
            ->add(EaFilter\BooleanFilter::new('isPublished', 'Is Published?'))
            ->add(DateFilter::new('createdAt', 'Create Date'))
            ->add(DateFilter::new('updatedAt', 'Update Date'))
        ;
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields([
                'id',
                'title',
                'slug',
                'author.displayName',
            ]);
    }
}
