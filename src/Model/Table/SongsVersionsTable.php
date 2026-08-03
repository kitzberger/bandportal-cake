<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * SongsVersions Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\SongsTable&\Cake\ORM\Association\BelongsTo $Songs
 * @property \Cake\ORM\Association\HasMany $Comments
 * @property \Cake\ORM\Association\HasMany $Files
 *
 * @method \App\Model\Entity\SongsVersion get($primaryKey, $options = [])
 * @method \App\Model\Entity\SongsVersion newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SongsVersion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SongsVersion|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SongsVersion saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SongsVersion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SongsVersion[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SongsVersion findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SongsVersionsTable extends AbstractTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('songs_versions');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Footprint');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Songs', [
            'foreignKey' => 'song_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'song_version_id'
        ]);
        $this->hasMany('Files', [
            'foreignKey' => 'song_version_id'
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
            ->requirePresence('song_id', 'create')
            ->notEmptyString('song_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->integer('length')
            ->allowEmptyString('length');

        $validator
            ->scalar('text')
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
        $rules->add($rules->existsIn(['song_id'], 'Songs'));

        return $rules;
    }

    public function beforeMarshal(\Cake\Event\Event $event, \ArrayObject $data, \ArrayObject $options)
    {
        $length = trim((string) $data['length']);

        if (is_numeric($length)) {
            if ($length > 15) {
                // it's probably secords, so we keep it as entered.
                $data['length'] = $length;
            } else {
                // it's probably minutes!
                $data['length'] = $length * 60;
            }
        } else {
            if (preg_match('/^(\d+)[^\d+](\d+)$/', $length, $matches)) {
                $minutes = $matches[1];
                $seconds = $matches[2] ?: 0;

                $data['length'] = ($minutes * 60) + $seconds;
            }
        }
    }

    public function afterSaveCommit(\Cake\Event\Event $event, \Cake\Datasource\EntityInterface $entity, \ArrayObject $options)
    {
        $diff = $this->getDiff($entity, ['title', 'length', 'text']);

        if (!empty($diff)) {
            $logs = \Cake\ORM\TableRegistry::getTableLocator()->get('Logs');
            $log = $logs->newEntity([
                'user_id'         => $entity->modified_by,
                'song_version_id' => $entity->id,
                'created'         => $entity->modified,
                'diff'            => $diff,
            ]);

            $logs->save($log);
        }
    }
}
