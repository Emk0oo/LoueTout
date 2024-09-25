<?php

namespace App\Listener;

use App\Entity\Instance;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Instance::class)]
class UserPostPersistListener
{
    public function postPersist(Instance $instance, PostPersistEventArgs $event): void
    {
        $sql_id = str_replace('-', '_', $instance->getName() ?: $instance->getId());
        $instance->setSqlUserName("instance_user_{$sql_id}");
        $instance->setSqlDbName("instance_db_{$sql_id}");
        $instance->setSqlDbPass("instance_password_{$sql_id}");

        $event->getObjectManager()->flush();
    }
}
