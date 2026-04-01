<?php

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * TODO
 */
class DiffHelper extends Helper
{
    /**
     * @param string $text
     * @return string
     */
    public function render($text = '', array $options = [])
    {
        $text = htmlspecialchars($text);

        return str_replace(
            ['&lt;del&gt;', '&lt;/del&gt;', '&lt;ins&gt;', '&lt;/ins&gt;', '&lt;b&gt;', '&lt;/b&gt;', '&lt;br&gt;'],
            ['<del>', '</del>', '<ins>', '</ins>', '<b>', '</b>', '<br>'],
            $text
        );
    }
}
