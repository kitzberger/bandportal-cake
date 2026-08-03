<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Date Entity
 *
 * @property int $id
 * @property boolean $is_fullday
 * @property \Cake\I18n\FrozenTime $begin
 * @property \Cake\I18n\FrozenTime $end
 * @property int $user_id
 * @property int $location_id
 * @property string $title
 * @property string $text
 * @property int $status
 * @property string $uri
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Location $location
 * @property \App\Model\Entity\Comment[] $comments
 * @property \App\Model\Entity\File[] $files
 * @property \App\Model\Entity\Vote[] $votes
 * @property \App\Model\Entity\Share[] $shares
 */
class Date extends Entity
{
    public const STATUS_CANCELED = -2;
    public const STATUS_BLOCKER = -1;
    public const STATUS_DEFAULT = 0;
    public const STATUS_UNCONFIRMED = 1;
    public const STATUS_CONFIRMED = 2;

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
        '*' => true,
        'id' => false
    ];

    public function _getCombinedTitle()
    {
        return $this->_fields['begin']->format('Y-m-d') . ': ' . $this->_fields['title'];
    }

    protected function _getStatusString()
    {
        return \App\Helper\DateStatus::toString($this->status);
    }

    public function getOriginalStatusString()
    {
        $status = $this->getOriginal('status');
        return \App\Helper\DateStatus::toString($status);
    }

    public function isAlreadyPast()
    {
        $now = new \Cake\I18n\FrozenTime();

        if ($this->end != null) {
            if ($this->end <= $now) {
                return true;
            } else {
                return false;
            }
        }

        if ($this->begin && ($this->begin <= $now)) {
            return true;
        }

        return false;
    }
}
