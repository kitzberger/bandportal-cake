<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * CollectionsSongs Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Collections
 * @property \Cake\ORM\Association\BelongsTo $Songs
 *
 * @method \App\Model\Entity\CollectionsSong get($primaryKey, $options = [])
 * @method \App\Model\Entity\CollectionsSong newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CollectionsSong[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CollectionsSong|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CollectionsSong patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CollectionsSong[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CollectionsSong findOrCreate($search, callable $callback = null)
 */
class CollectionsSongsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('collections_songs');
        $this->setDisplayField('collection_id');
        $this->setPrimaryKey(['collection_id', 'song_id']);

        $this->belongsTo('Collections', [
            'foreignKey' => 'collection_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Songs', [
            'foreignKey' => 'song_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('SongsVersions', [
            'foreignKey' => 'song_version_id',
            'joinType' => 'INNER'
        ]);
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
        $rules->add($rules->existsIn(['collection_id'], 'Collections'));
        $rules->add($rules->existsIn(['song_id'], 'Songs'));

        return $rules;
    }
}
