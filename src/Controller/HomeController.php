<?php

namespace App\Controller;

use App\Entity\Instance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    public function __construct(private KernelInterface $kernel, private EntityManagerInterface $manager)
    {
    }

    #[Route('/home', name: 'app_home')]
    public function index(): Response
    { 
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
      
        $instance_uuid = uniqid();

        $instance = new Instance();
        $instance->setName('instance-'.$instance_uuid);

        $this->manager->persist($instance);
        $this->manager->flush();
        
        $input = new ArrayInput([
            'command' => 'app:database:create',
            'id' => $instance->getId(),
        ]);
        
        $application->run($input, new NullOutput());
        
        echo 'Instance created with id: '.$instance->getName();
 
 
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

}
