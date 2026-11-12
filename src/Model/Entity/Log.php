<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Log Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $song_id
 * @property int $song_version_id
 * @property int $date_id
 * @property int $idea_id
 * @property int $comment_id
 * @property int $collection_id
 * @property int $vote_id
 * @property int $file_id
 * @property int $share_id
 * @property string $diff
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Song $song
 * @property \App\Model\Entity\SongsVersion $songVersion
 * @property \App\Model\Entity\Date $date
 * @property \App\Model\Entity\Idea $idea
 * @property \App\Model\Entity\Comment $comment
 * @property \App\Model\Entity\Collection $collection
 * @property \App\Model\Entity\Vote $vote
 * @property \App\Model\Entity\File $file
 * @property \App\Model\Entity\Share $share
 */
class Log extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     */
    protected array $_accessible = [
        '*' => true,
        'id' => false
    ];
}
