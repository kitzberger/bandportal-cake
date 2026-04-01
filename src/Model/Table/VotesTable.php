<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Votes Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Dates
 * @property \Cake\ORM\Association\BelongsTo $Ideas
 *
 * @method \App\Model\Entity\Vote get($primaryKey, $options = [])
 * @method \App\Model\Entity\Vote newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Vote[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Vote|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Vote patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Vote[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Vote findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VotesTable extends AbstractTable
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

        $this->setTable('votes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Footprint');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT'
        ]);
        $this->belongsTo('Dates', [
            'foreignKey' => 'date_id'
        ]);
        $this->belongsTo('Ideas', [
            'foreignKey' => 'idea_id'
        ]);
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
            ->integer('vote')
            ->allowEmpty('vote');

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
        $rules->add($rules->existsIn(['date_id'], 'Dates'));
        $rules->add($rules->existsIn(['idea_id'], 'Ideas'));

        return $rules;
    }

    protected function getDiff($entity, $fields = null)
    {
        $diffs = [];

        if ($entity->isNew() === false) {
            $old = $entity->getOriginalVoteString();
        } else {
            $old = '';
        }

        $new = $entity->get('vote_string');

        if ($old !== $new) {
            $diffs[] = '<b>vote</b>: ' . \App\Helper\Diff::htmlDiff($old, $new);
        }

        return implode('<br>', $diffs);
    }

    public function afterSaveCommit(\Cake\Event\Event $event, \Cake\Datasource\EntityInterface $entity, \ArrayObject $options)
    {
        $diff = $this->getDiff($entity);

        if (!empty($diff)) {
            $logs = \Cake\ORM\TableRegistry::get('Logs');
            $log = $logs->newEntity([
                'user_id' => $entity->modified_by,
                'vote_id' => $entity->id,
                'created' => $entity->modified,
                'diff' => $diff,
            ]);
            if ($entity->date_id) {
                $log->date_id = $entity->date_id;
            }
            if ($entity->idea_id) {
                $log->idea_id = $entity->idea_id;
            }

            $logs->save($log);
        }
    }
}
