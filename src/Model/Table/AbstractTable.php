<?php

namespace App\Model\Table;

class AbstractTable extends \Cake\ORM\Table
{
    protected function getDiff($entity, $fields = null)
    {
        $diffs = [];

        if ($fields !== null) {
            foreach ($fields as $field) {
                if ($entity->isNew() === false) {
                    $old = (string)$entity->getOriginal($field);
                } else {
                    $old = '';
                }
                $new = (string)$entity->get($field);

                if ($old === $new) {
                    continue;
                }

                $diffs[] = '<b>' . $field . '</b>: ' . \App\Helper\Diff::htmlDiff($old, $new);
            }
        }

        return implode('<br>', $diffs);
    }

    public function isOwnedBy($id, $userId)
    {
        return $this->exists(['id' => $id, 'user_id' => $userId]);
    }
}
