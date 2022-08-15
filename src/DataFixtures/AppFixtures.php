<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Factory\UserFactory;
use App\Factory\PostFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()
            ->verified()
            ->create([
                'displayName' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'test',
            ]);
        
        UserFactory::createMany(20);
        PostFactory::createMany(250);
        
        $manager->flush();
    }
}
