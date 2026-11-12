<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Location Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $text
 * @property string $url
 * @property string $email
 * @property string $address
 * @property string $city
 * @property string $zip
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 */
class Location extends Entity
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

    #[\Override]
    public function __toString(): string
    {
        $tmp[] = $this->title;
        $tmp[] = $this->address;
        $tmp[] = $this->zip . ' ' . $this->city;
        return implode(', ', $tmp);
    }
}
