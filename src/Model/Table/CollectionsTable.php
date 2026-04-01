<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Collections Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsToMany $Files
 * @property \Cake\ORM\Association\BelongsToMany $Songs
 * @property \Cake\ORM\Association\HasMany $Comments
 * @property \Cake\ORM\Association\HasMany $Shares
 *
 * @method \App\Model\Entity\Collection get($primaryKey, $options = [])
 * @method \App\Model\Entity\Collection newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Collection[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Collection|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Collection patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Collection[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Collection findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CollectionsTable extends AbstractTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('collections');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Footprint');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsToMany('Files', [
            'foreignKey' => 'collection_id',
            'targetForeignKey' => 'file_id',
            'joinTable' => 'collections_files',
            'sort' => 'sorting',
        ]);
        $this->belongsToMany('Songs', [
            'foreignKey' => 'collection_id',
            'targetForeignKey' => 'song_id',
            'joinTable' => 'collections_songs',
            'sort' => 'sorting',
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'collection_id'
        ]);
        $this->hasMany('Shares', [
            'foreignKey' => 'collection_id'
        ]);
    }

    public function beforeMarshal(\Cake\Event\Event $event, \ArrayObject $data, \ArrayObject $options)
    {
        foreach (['files', 'songs'] as $relation) {
            if (!empty($data[$relation]['_ids'])) {
                $sorting = array_flip($data[$relation]['_ids']);

                $joinData = [];
                foreach ($sorting as $id => $index) {
                    $joinData[] = ['id' => $id, '_joinData' => ['sorting' => $index+1]];
                }
                $data[$relation] = $joinData;
            }
        }
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
            ->requirePresence('title', 'create')
            ->notEmpty('title');

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

        return $rules;
    }

    protected function getDiff($entity, $fields = null)
    {
        $diffs = [];
        if ($diff = parent::getDiff($entity, $fields)) {
            $diffs[] = $diff;
        }

        foreach (['files', 'songs'] as $field) {
            if ($entity->isNew() === false) {
                $old = count($entity->getOriginal($field));
            } else {
                $old = '';
            }

            $new = count($entity->get($field));

            if ($old !== $new) {
                $diffs[] = '<b>' . $field . '</b>: ' . \App\Helper\Diff::htmlDiff($old, $new);
            }
        }

        return implode('<br>', $diffs);
    }

    public function afterSaveCommit(\Cake\Event\Event $event, \Cake\Datasource\EntityInterface $entity, \ArrayObject $options)
    {
        $diff = $this->getDiff($entity, ['title']);

        if (!empty($diff)) {
            $logs = \Cake\ORM\TableRegistry::get('Logs');
            $log = $logs->newEntity([
                'user_id' => $entity->modified_by,
                'collection_id' => $entity->id,
                'created' => $entity->modified,
                'diff' => $diff,
            ]);

            $logs->save($log);
        }
    }
}
