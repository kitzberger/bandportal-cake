<?php

namespace App\Helper;

class DateStatus
{
    public static function toString($status)
    {
        return match ($status) {
            \App\Model\Entity\Date::STATUS_CANCELED => __('Gig (cancelled)'),
            \App\Model\Entity\Date::STATUS_BLOCKER => __('Blocker'),
            \App\Model\Entity\Date::STATUS_DEFAULT => __('Default'),
            \App\Model\Entity\Date::STATUS_UNCONFIRMED => __('Gig (unconfirmed)'),
            \App\Model\Entity\Date::STATUS_CONFIRMED => __('Gig (confirmed)'),
            default => throw new \Exception('Unknown status!'),
        };
    }
}
