<?php

namespace App\Helper;

class Diff
{
    public static function diffArray(array $old, array $new)
    {
        $matrix = [];
        $maxlen = 0;
        foreach ($old as $oindex => $ovalue) {
            $nkeys = array_keys($new, $ovalue);
            foreach ($nkeys as $nindex) {
                $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ? $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                if ($matrix[$oindex][$nindex] > $maxlen) {
                    $maxlen = $matrix[$oindex][$nindex];
                    $omax = $oindex + 1 - $maxlen;
                    $nmax = $nindex + 1 - $maxlen;
                }
            }
        }
        if ($maxlen == 0) {
            return [['d'=>$old, 'i'=>$new]];
        }
        return array_merge(
            self::diffArray(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
            array_slice($new, $nmax, $maxlen),
            self::diffArray(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen))
        );
    }

    public static function htmlDiff(string $old, string $new)
    {
        $ret = '';
        $diff = self::diffArray(explode(' ', $old), explode(' ', $new));
        foreach ($diff as $k) {
            if (is_array($k)) {
                $ret .= (!empty($k['d']) ? '<del>' . implode(' ', $k['d']) . '</del> ' : '') . (!empty($k['i']) ? '<ins>' . implode(' ', $k['i']) . '</ins> ' : '');
            } else {
                $ret .= $k . ' ';
            }
        }
        return $ret;
    }
}
