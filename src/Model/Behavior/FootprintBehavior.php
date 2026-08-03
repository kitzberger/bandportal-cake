<?php

namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;

class FootprintBehavior extends Behavior
{
    public function getFootprint(EntityInterface $entity)
    {
        $userId = $_SESSION['Auth']['id'] ?? null;
        if ($userId) {
            if ($entity->isNew()) {
                $entity->set('created_by', $userId);
                $entity->set('modified_by', $userId);
            } else {
                $entity->set('modified_by', $userId);
            }
        }
    }

    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        $this->getFootprint($entity);
    }
}
