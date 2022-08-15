<?php

namespace App\Pagination;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\Blog\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\Criteria;

use App\Entity\User as Author;

class PostPaginator extends AbstractPaginator
{    
    // knp pager params
    public array $searchableFields = ['p.title', 'a.displayName'];
    
    public function __construct(
        PostRepository $repository, 
        RequestStack $requestStack, 
        PaginatorInterface $paginator,
    ) {
        parent::__construct($repository, $requestStack, $paginator);
    }
    
    protected function createBuilder(): QueryBuilder
    {
        return $this->repository->createQueryBuilder('p')
            ->addSelect('a')
            ->innerJoin('p.author', 'a')
            ->orderBy('p.createdAt', 'desc');
    }
    
    protected function applyOptions(array $options = [])
    {
        $isPublished = $options['isPublished'] ?? null;
        if (null !== $isPublished) {
            $expr = Criteria::expr();
            
            $this->getCriteria()->andWhere($expr->andX(
                $expr->eq('p.isPublished', true),
                $expr->eq('a.isVerified', true)
            ));
        }
        
        $author = $options['author'] ?? null;
        if (null !== $author) {
            $expr = Criteria::expr();
            
            $authorId = ($author instanceof Author) ? $author->getId() : $author;
            $this->getCriteria()->andWhere(
                $expr->eq('author', $authorId),
            );
        }
    }
}
