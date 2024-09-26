<?php

namespace App\Listener;

use App\Repository\InstanceRepository;
use App\Services\GlobalVariableService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use function PHPUnit\Framework\stringStartsWith;

#[AsEventListener(event: 'kernel.request', method: 'onKernelRequest')]
class KernelRequestEvent
{
    
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage, 
        private readonly ManagerRegistry $registry, 
        private InstanceRepository $instanceRepository, 
        private GlobalVariableService $globalVariableService,
        )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // si la requete commence par /super-admin
        if(preg_match('#^/super-admin#', $event->getRequest()->getRequestUri())){
            $doctrineConnection = $this->registry->getConnection('default');
            $doctrineConnection->changeDatabase([
                'dbname' => "app",
                'user' => "user",
                'password' => "password"
            ]);
            
        }
        
        if($event->getRequest()->attributes->has('instance')) {

            $instance_name = $event->getRequest()->attributes->get('instance');

            $instance = $this->instanceRepository->findOneBy([
                'name' => $instance_name
            ]);

            if($instance === null) {
                throw new \Exception('Instance not found');
            }

            $doctrineConnection = $this->registry->getConnection('default');
            $doctrineConnection->changeDatabase([
                'dbname' => $instance->getSqlDbName(),
                'user' => $instance->getSqlUserName(),
                'password' => $instance->getSqlDbPass(),
            ]);

            $this->globalVariableService->set('current_instance', $instance);
  
        }
    }
}
