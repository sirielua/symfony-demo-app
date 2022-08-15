<?php

namespace App\Pagination;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\Criteria;

abstract class AbstractPaginator
{
    public array $searchableFields = [];
    
    // knp pager params
    public string $filterFieldParameterName = '';
    public string $filterValueParameterName = 'search';
    public array $filterFieldAllowList = [];
    
    private ?Criteria $criteria = null;
    
    public function __construct(
        protected ServiceEntityRepositoryInterface $repository, 
        protected RequestStack $requestStack, 
        protected PaginatorInterface $paginator,
    ) {}
    
    protected function createBuilder(): QueryBuilder
    {
        return $this->repository->createQueryBuilder('t');
    }
    
    public function paginate(int $page = 1, ?int $limit = null, array $options = [])
    {
        $builder = $this->configureBuilder($options);
        
        $pagination = $this->paginator->paginate($builder, $page, $limit, [
            'filterFieldParameterName' => $this->filterFieldParameterName,
            'filterValueParameterName' => $this->filterValueParameterName,
            'filterFieldAllowList' => $this->filterFieldAllowList,
        ]);
        
        $this->reset();
        
        return $pagination;
    }
    
    protected function configureBuilder(array $options = []): QueryBuilder
    {
        $builder = static::createBuilder();
        $criteria = $this->getCriteria();
        
        static::applyOptions($options);
        static::applyFilter();
        
        $builder->addCriteria($criteria);
        
        return $builder;
    }
    
    protected function getCriteria()
    {
        if (null === $this->criteria) {
            $this->criteria = Criteria::create();
        }
        
        return $this->criteria;
    }
    
    protected function applyOptions(array $options = [])
    {
        
    }
    
    protected function applyFilter()
    {
        if(empty($this->searchableFields)) {
            return;
        }
        
        $criteria = $this->getCriteria();
        
        $search = $this->requestStack->getCurrentRequest()
                ->query->get($this->filterValueParameterName, null);
        
        if (null !== $search) {
            $expr = Criteria::expr();
            
            $searchExpr = array_map(function($field) use ($expr, $search) {
                return $expr->contains($field, $search);
            }, $this->searchableFields);
            
            $criteria->andWhere($expr->orX(...$searchExpr));
        }
    }
    
    protected function reset()
    {
        $this->criteria = null;
    }
}
