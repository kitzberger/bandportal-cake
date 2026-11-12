<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Comments Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Dates
 * @property \Cake\ORM\Association\BelongsTo $Ideas
 * @property \Cake\ORM\Association\BelongsTo $Songs
 * @property \Cake\ORM\Association\BelongsTo $SongsVersions
 * @property \Cake\ORM\Association\BelongsTo $Collections
 *
 * @method \App\Model\Entity\Comment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Comment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Comment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Comment|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Comment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Comment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Comment findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CommentsTable extends AbstractTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('comments');
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
        $this->belongsTo('Songs', [
            'foreignKey' => 'song_id'
        ]);
        $this->belongsTo('SongsVersions', [
            'foreignKey' => 'song_version_id'
        ]);
        $this->belongsTo('Collections', [
            'foreignKey' => 'collection_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     */
    #[\Override]
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        $validator
            ->allowEmptyString('text');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     */
    #[\Override]
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['date_id'], 'Dates'));
        $rules->add($rules->existsIn(['idea_id'], 'Ideas'));
        $rules->add($rules->existsIn(['song_id'], 'Songs'));
        $rules->add($rules->existsIn(['song_version_id'], 'SongsVersions'));
        $rules->add($rules->existsIn(['collection_id'], 'Collections'));

        return $rules;
    }

    public function afterSaveCommit(\Cake\Event\Event $event, \Cake\Datasource\EntityInterface $entity, \ArrayObject $options)
    {
        $diff = $this->getDiff($entity, ['text']);

        if (!empty($diff)) {
            $logs = \Cake\ORM\TableRegistry::getTableLocator()->get('Logs');
            $log = $logs->newEntity([
                'user_id' => $entity->modified_by,
                'comment_id' => $entity->id,
                'created' => $entity->modified,
                'diff' => $diff,
            ]);
            if ($entity->date_id) {
                $log->date_id = $entity->date_id;
            }
            if ($entity->idea_id) {
                $log->idea_id = $entity->idea_id;
            }
            if ($entity->song_id) {
                $log->song_id = $entity->song_id;
            }
            if ($entity->song_version_id) {
                $log->song_version_id = $entity->song_version_id;
            }
            if ($entity->collection_id) {
                $log->collection_id = $entity->collection_id;
            }

            $logs->save($log);
        }
    }
}
