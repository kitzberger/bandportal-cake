<?php

namespace App\Helper;

class VoteVote
{
    public static function toString($vote)
    {
        return match ($vote) {
            -1 => __('Negative'),
            0 => __('Neutral'),
            1 => __('Positive'),
            default => throw new \Exception('Unknown vote!'),
        };
    }
}
