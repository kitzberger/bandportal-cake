<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SongsVersion Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $song_id
 * @property string $title
 * @property int|null $length
 * @property string|null $text
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Song $song
 */
class SongsVersion extends Entity
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
        'song_id' => true,
        'title' => true,
        'length' => true,
        'text' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'song' => true,
    ];

    public function _getCombinedTitle()
    {
        return $this->song->get('title') . ': ' . $this->_fields['title'];
    }
}
