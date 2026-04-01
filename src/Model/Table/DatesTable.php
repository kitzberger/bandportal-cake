<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Dates Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Location
 * @property \Cake\ORM\Association\HasMany $Comments
 * @property \Cake\ORM\Association\HasMany $Files
 * @property \Cake\ORM\Association\HasMany $Votes
 * @property \Cake\ORM\Association\HasMany $Shares
 *
 * @method \App\Model\Entity\Date get($primaryKey, $options = [])
 * @method \App\Model\Entity\Date newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Date[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Date|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Date patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Date[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Date findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DatesTable extends AbstractTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('dates');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Footprint');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Locations', [
            'foreignKey' => 'location_id',
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'date_id'
        ]);
        $this->hasMany('Files', [
            'foreignKey' => 'date_id'
        ]);
        $this->hasMany('Votes', [
            'foreignKey' => 'date_id'
        ]);
        $this->hasMany('Shares', [
            'foreignKey' => 'date_id'
        ]);
    }

    public function beforeMarshal(\Cake\Event\Event $event, \ArrayObject $data, \ArrayObject $options)
    {
        if (isset($data['begin'])) {
            if (strlen($data['begin']) === 10) {
                $data['begin'] .= ' 00:00';
            }
        }
        if (isset($data['end'])) {
            if (strlen($data['end']) === 10) {
                $data['end'] .= ' 23:59';
            }
        }
        $data['is_fullday'] = $data['is_fullday'] ?? 0;
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('user_id', 'create')
            ->notEmpty('user_id');

        $validator
            ->allowEmpty('location_id');

        $validator
            ->boolean('is_fullday')
            ->notEmpty('is_fullday');

        $validator
            ->dateTime('begin')
            ->notEmpty('begin');

        $validator
            ->dateTime('end')
            ->allowEmpty('end');

        $validator
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->allowEmpty('text');

        $validator
            ->allowEmpty('uri');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    protected function getDiff($entity, $fields = null)
    {
        $diffs = [];

        // --------------------
        // parent diff
        // --------------------
        if ($diff = parent::getDiff($entity, $fields)) {
            $diffs[] = $diff;
        }

        // --------------------
        // date.status
        // --------------------
        if ($entity->isNew() === false) {
            $old = $entity->getOriginalStatusString();
        } else {
            $old = '';
        }

        $new = $entity->get('status_string');

        if ($old !== $new) {
            $diffs[] = '<b>status</b>: ' . \App\Helper\Diff::htmlDiff($old, $new);
        }

        // --------------------
        // date.location
        // --------------------
        if ($entity->isNew() === false) {
            $old = (int)$entity->getOriginal('location_id');
        } else {
            $old = 0;
        }
        $new = (int)$entity->get('location_id');

        if ($old !== $new) {
            $locations = \Cake\ORM\TableRegistry::get('Locations');
            if ($old > 0) {
                $old = $locations->get($old);
                $old = $old['title'];
            } else {
                $old = '';
            }
            if ($new > 0) {
                $new = $locations->get($new);
                $new = $new['title'];
            } else {
                $new = '';
            }
            $diffs[] = '<b>location</b>: ' . \App\Helper\Diff::htmlDiff($old, $new);
        }

        return implode('<br>', $diffs);
    }

    public function afterSaveCommit(\Cake\Event\Event $event, \Cake\Datasource\EntityInterface $entity, \ArrayObject $options)
    {
        $diff = $this->getDiff($entity, ['title', 'text', 'begin', 'end']);

        if (!empty($diff)) {
            $logs = \Cake\ORM\TableRegistry::get('Logs');
            $log = $logs->newEntity([
                'user_id' => $entity->modified_by,
                'date_id' => $entity->id,
                'created' => $entity->modified,
                'diff' => $diff,
            ]);

            $logs->save($log);
        }
    }
}
