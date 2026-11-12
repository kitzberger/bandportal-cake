<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Vote Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $date_id
 * @property int $idea_id
 * @property int $vote
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Date $date
 * @property \App\Model\Entity\Idea $idea
 */
class Vote extends Entity
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

    protected function _getVoteString()
    {
        return \App\Helper\VoteVote::toString($this->vote);
    }

    public function getOriginalVoteString()
    {
        $vote = $this->getOriginal('vote');
        return \App\Helper\VoteVote::toString($vote);
    }
}
