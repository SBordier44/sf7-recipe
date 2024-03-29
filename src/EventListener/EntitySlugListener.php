<?php

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsDoctrineListener(event: Events::prePersist, connection: 'default')]
#[AsDoctrineListener(event: Events::preUpdate, connection: 'default')]
final readonly class EntitySlugListener
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function prePersist(PrePersistEventArgs $event): void
    {
        $this->setSlug($event);
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $this->setSlug($event);
    }

    private function setSlug(PrePersistEventArgs|PreUpdateEventArgs $event): void
    {
        $entity = $event->getObject();

        $field = match (true) {
            method_exists($entity, 'getName') => 'getName',
            method_exists($entity, 'getTitle') => 'getTitle',
            default => null,
        };

        if (method_exists($entity, 'setSlug')) {
            $entity->setSlug($this->slugger->slug($entity->$field())->ascii()->lower());
        }
    }
}
