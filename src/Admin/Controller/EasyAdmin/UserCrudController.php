<?php

namespace App\Admin\Controller\EasyAdmin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions {
        return parent::configureActions($actions)
            ->add('index', 'detail');
    }
    
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm()
        ;
        
        yield TextField::new('email');
        yield TextField::new('displayName');
        yield BooleanField::new('isVerified')
            ->setFormTypeOption('confirmable', true)
            ->onlyOnIndex()
        ;
        yield BooleanField::new('isVerified')
            ->hideOnIndex()
        ;
        yield DateField::new('createdAt')
            ->hideOnForm()
        ;
    }
    
}