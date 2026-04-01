<?php

namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Files Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Dates
 * @property \Cake\ORM\Association\BelongsTo $Ideas
 * @property \Cake\ORM\Association\BelongsTo $Songs
 * @property \Cake\ORM\Association\BelongsTo $SongsVersion
 * @property \Cake\ORM\Association\BelongsToMany $Collections
 * @property \Cake\ORM\Association\HasMany $Shares
 *
 * @method \App\Model\Entity\File get($primaryKey, $options = [])
 * @method \App\Model\Entity\File newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\File[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\File|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\File patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\File[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\File findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FilesTable extends AbstractTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('files');
        $this->setDisplayField('title');
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
        $this->belongsToMany('Collections', [
            'foreignKey' => 'file_id',
            'targetForeignKey' => 'collection_id',
            'joinTable' => 'collections_files'
        ]);
        $this->hasMany('Shares', [
            'foreignKey' => 'files_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
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
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->requirePresence('file', 'create')
            ->notEmpty('file');

        $validator
            ->allowEmptyString('regions')
            ->add('regions', 'regionsAreJson', [
                'rule' => function ($value, array $context) {
                    $json = json_decode($value);
                    if (json_last_error()) {
                        return json_last_error_msg();
                    }
                    return true;
                }
            ]);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['date_id'], 'Dates'));
        $rules->add($rules->existsIn(['idea_id'], 'Ideas'));
        $rules->add($rules->existsIn(['song_id'], 'Songs'));
        $rules->add($rules->existsIn(['song_version_id'], 'SongsVersions'));

        return $rules;
    }

    public function beforeMarshal(Event $event, \ArrayObject $data, \ArrayObject $options)
    {
        if ($data['regions'] ?? false) {
            // Make sure NULL is saved instead of empty string
            $data['regions'] = $data['regions'] ?: null;
        }
    }

    public function afterSaveCommit(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        $diff = $this->getDiff($entity, ['title', 'file', 'is_public']);

        if (!empty($diff)) {
            $logs = \Cake\ORM\TableRegistry::get('Logs');
            $log = $logs->newEntity([
                'user_id' => $entity->modified_by,
                'file_id' => $entity->id,
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

            $logs->save($log);
        }
    }
}
