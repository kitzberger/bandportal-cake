<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * CollectionsFiles Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Collections
 * @property \Cake\ORM\Association\BelongsTo $Files
 *
 * @method \App\Model\Entity\CollectionsFile get($primaryKey, $options = [])
 * @method \App\Model\Entity\CollectionsFile newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CollectionsFile[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CollectionsFile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CollectionsFile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CollectionsFile[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CollectionsFile findOrCreate($search, callable $callback = null)
 */
class CollectionsFilesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('collections_files');
        $this->setDisplayField('collection_id');
        $this->setPrimaryKey(['collection_id', 'file_id']);

        $this->belongsTo('Collections', [
            'foreignKey' => 'collection_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Files', [
            'foreignKey' => 'file_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['collection_id'], 'Collections'));
        $rules->add($rules->existsIn(['file_id'], 'Files'));

        return $rules;
    }
}
