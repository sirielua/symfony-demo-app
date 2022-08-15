<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\Criteria;

#[AsCommand(
    name: 'app:delete-unvefiried-users', 
    description: 'Delete users that failed to verify their email during a defined time frame',
)]
class DeleteUnverifiedUsersByTimeout extends Command
{
    private EntityManagerInterface $entityManager;
    private UserRepository $users;
    private int $timeout;
    
    public function __construct(EntityManagerInterface $entityManager, UserRepository $users, int $timeout = 7200)
    {
        $this->entityManager = $entityManager;
        $this->users = $users;
        $this->timeout = $timeout;
        
        if ($timeout <= 600) {
            throw new \InvalidArgumentException('Timout is too short. Minimum is 600 (10 minutes)');
        }
        
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = $this->getQuery();
        
        while ($user = $query->getOneOrNullResult()) {
            if ($user->isVerified()) {
                throw new \LogicException('User actually is verified o_0');
            }
            
            $output->writeln('Deleting "' . $user->getDisplayName() . '" (id' . $user->getId() . ')');
            
            $this->users->remove($user);
            $this->entityManager->flush();
        }
        
        return Command::SUCCESS;
    }
    
    private function getQuery(): Query
    {
        $alias = 'u';
        
        $qb = $this->users->createQueryBuilder($alias);
        $criteria = $this->getDeadlineCriteria($alias);
        
        return $qb->addCriteria($criteria)->getQuery();
    }
    
    private function getDeadlineCriteria(string $alias): Criteria
    {
        $deadLine = $this->getDeadLine();
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        
        return $criteria
            ->where($expr->eq("$alias.isVerified", false))
            ->andWhere($expr->lt("$alias.createdAt", $deadLine))
            ->setMaxResults(1)
//            ->andWhere($expr->lt('createdAt', $deadLine->format('Y-m-d H:i:s')));
        ;
    }
    
    private function getDeadLine(): \DateTimeInterface
    {
        $timestamp = time() - $this->timeout;
        
        return (new \DateTimeImmutable())
            ->setTimestamp($timestamp);
    }
}
