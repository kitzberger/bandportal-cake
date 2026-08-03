<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Share Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $date_id
 * @property int|null $idea_id
 * @property int|null $song_id
 * @property int|null $collection_id
 * @property int|null $file_id
 * @property string|null $comment
 * @property \Cake\I18n\FrozenTime|null $expire_date
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Date $date
 * @property \App\Model\Entity\Idea $idea
 * @property \App\Model\Entity\Collection $collection
 * @property \App\Model\Entity\File $file
 */
class Share extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected array $_accessible = [
        'user_id' => true,
        'date_id' => true,
        'idea_id' => true,
        'song_id' => true,
        'collection_id' => true,
        'file_id' => true,
        'comment' => true,
        'expire_date' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'date' => true,
        'idea' => true
    ];
}
