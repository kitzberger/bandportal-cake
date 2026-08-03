<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Shares Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\DatesTable|\Cake\ORM\Association\BelongsTo $Dates
 * @property \App\Model\Table\IdeasTable|\Cake\ORM\Association\BelongsTo $Ideas
 * @property \App\Model\Table\SongsTable|\Cake\ORM\Association\BelongsTo $Songs
 * @property \App\Model\Table\CollectionsTable|\Cake\ORM\Association\BelongsTo $Collections
 * @property \App\Model\Table\FilesTable|\Cake\ORM\Association\BelongsTo $Files
 *
 * @method \App\Model\Entity\Share get($primaryKey, $options = [])
 * @method \App\Model\Entity\Share newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Share[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Share|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Share|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Share patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Share[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Share findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SharesTable extends AbstractTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('shares');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Footprint');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
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
        $this->belongsTo('Collections', [
            'foreignKey' => 'collection_id'
        ]);
        $this->belongsTo('Files', [
            'foreignKey' => 'file_id'
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
            ->scalar('comment')
            ->allowEmptyString('comment');

        $validator
            ->dateTime('expire_date')
            ->allowEmptyDateTime('expire_date');

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
        $rules->add($rules->existsIn(['collection_id'], 'Collections'));
        $rules->add($rules->existsIn(['file_id'], 'Files'));

        return $rules;
    }

    public function afterSaveCommit(\Cake\Event\Event $event, \Cake\Datasource\EntityInterface $entity, \ArrayObject $options)
    {
        $diff = $this->getDiff($entity, ['user_id', 'date_id', 'idea_id', 'song_id', 'collection_id', 'file_id', 'comment', 'expire_date']);

        if (!empty($diff)) {
            $logs = \Cake\ORM\TableRegistry::getTableLocator()->get('Logs');
            $log = $logs->newEntity([
                'user_id' => $entity->modified_by,
                'share_id' => $entity->id,
                'created' => $entity->modified,
                'diff' => $diff,
            ]);

            $logs->save($log);
        }
    }

    /**
     * Determines whether or not a record has been shared with a given user
     *
     * @param  string        $type
     * @param  int|array     $id
     * @param  int           $user_id
     *
     * @return boolean
     */
    public function sharedWithUser($type, $id, $user_id)
    {
        if (!in_array($type, ['date', 'idea', 'song', 'file', 'collection'])) {
            throw new \Exception('Wrong type parameter!');
        }

        // try to find a share for that record
        $shares = $this->find('all', [
            'conditions' => [
                'user_id' => $user_id,
                $type . '_id IN' => $id, // int or array of ints
            ],
        ]);

        // if no direct share for file/song has been found,
        // try looking for a share for a collection containing that record
        if ($shares->count() === 0 && in_array($type, ['file', 'song'])) {
            $this->Collections = \Cake\ORM\TableRegistry::getTableLocator()->get('Collections' . ucfirst($type) . 's');
            $collections = $this->Collections->find('all', [
                'contain' => ['Collections'],
                'conditions' => [
                    $type . '_id' => $id,
                ]
            ]);
            if ($collections->count()) {
                $ids = array_map(fn($item) => $item->collection_id, $collections->toArray());
                return $this->sharedWithUser('collection', $ids, $user_id);
            }
        }

        return $shares->count();
    }
}
