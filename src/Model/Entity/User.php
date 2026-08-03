<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Authentication\PasswordHasher\DefaultPasswordHasher;

/**
 * User Entity
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $notified
 * @property string $username
 * @property bool $is_admin
 * @property bool $is_active
 * @property bool $is_passive
 *
 * @property \App\Model\Entity\Comment[] $comments
 * @property \App\Model\Entity\Date[] $dates
 * @property \App\Model\Entity\File[] $files
 * @property \App\Model\Entity\Idea[] $ideas
 * @property \App\Model\Entity\Collection[] $collections
 * @property \App\Model\Entity\Vote[] $votes
 * @property \App\Model\Entity\Share[] $shares
 */
class User extends Entity
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

    /**
     * Fields that are excluded from JSON versions of the entity.
     */
    protected array $_hidden = [
        'password'
    ];

    protected function _setPassword($value)
    {
        $hasher = new DefaultPasswordHasher();
        return $hasher->hash($value);
    }
}
