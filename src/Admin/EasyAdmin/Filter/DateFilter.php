<?php

// src/Controller/Admin/Filter/DateCalendarFilter.php
namespace App\Admin\EasyAdmin\Filter;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\ComparisonType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\DateTimeFilterType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DateFilter implements FilterInterface
{
    use FilterTrait;
    
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(DateTimeFilterType::class)
            ->setFormTypeOption('translation_domain', 'EasyAdminBundle')
            ->setFormTypeOption('value_type', DateType::class)
            ->renderAsSingleText()
            ;
    }
    
    public function renderAsSingleText(): self
    {
        return $this->setFormTypeOption('value_type_options', [
            'widget' => 'single_text',
        ]);
    }
    
    public function renderAsChoice(): self
    {
        return $this->setFormTypeOption('value_type_options', [
            'widget' => 'choice',
        ]);
    }
    
    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        $alias = $filterDataDto->getEntityAlias();
        $property = $filterDataDto->getProperty();
        $comparison = $filterDataDto->getComparison();
        $parameterName = $filterDataDto->getParameterName();
        $parameter2Name = $filterDataDto->getParameter2Name();
        $value = $filterDataDto->getValue();
        $value2 = $filterDataDto->getValue2();

        if (ComparisonType::BETWEEN === $comparison) {
            $queryBuilder->andWhere(sprintf('DATE_FORMAT(%s.%s, \'%%Y-%%m-%%d\') BETWEEN :%s and :%s', $alias, $property, $parameterName, $parameter2Name))
                ->setParameter($parameterName, (new \DateTimeImmutable($value))->format('Y-m-d'))
                ->setParameter($parameter2Name, (new \DateTimeImmutable($value2))->format('Y-m-d'));
        } else {
            $queryBuilder->andWhere(sprintf('DATE_FORMAT(%s.%s, \'%%Y-%%m-%%d\') %s :%s', $alias, $property, $comparison, $parameterName))
                ->setParameter($parameterName, (new \DateTimeImmutable($value))->format('Y-m-d'));
        }
    }
}