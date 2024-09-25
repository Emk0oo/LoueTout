<?php

namespace App\DataFixtures;

use App\Entity\Instance;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class AppFixtures extends Fixture
{
    public function __construct(private KernelInterface $kernel)
    {
    }

    public function load(ObjectManager $manager): void
    {

        $application = new Application($this->kernel);
        $application->setAutoExit(false);
      
        for ($i = 0; $i < 2; $i++) {
            $instance = new Instance();
            $instance->setName('instance-'.$i);

            $manager->persist($instance);
            
            $input = new ArrayInput([
                'command' => 'app:database:create',
                'id' => $instance->getId(),
            ]);
            
            $application->run($input, new NullOutput());
            
        }
        
        $manager->flush();
        
        $instances = $manager->getRepository(Instance::class)->findAll();
        foreach ($instances as $instance) {
            echo $instance->getName()."\n";
        }


    
    }
}
