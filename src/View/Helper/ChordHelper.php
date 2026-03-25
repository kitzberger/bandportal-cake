<?php

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * TODO
 */
class ChordHelper extends Helper
{
    private const CAPO_PATTERN = '/^(capo\s+)([\d|i|v|x]+)$/im';

    /**
     * @param string $text
     * @return string
     */
    public function render($text = '', array $options = [])
    {
        if (empty($text)) {
            return '';
        }

        // Replace windows linebreaks with linux ones (necessary for capo detection later!)
        $text = str_replace("\r\n", "\n", $text);

        $transposeBy = 0;
        if (isset($options['transposeBy']) && $options['transposeBy']) {
            $transposeBy = $this->determineTransposeByParameter($text, $options['transposeBy']);
        }

        return $this->transposeText($text, $transposeBy, $options['mode']);
    }

    public static function determineCapo(string $text): int
    {
        $capo = 0;
        if (preg_match(self::CAPO_PATTERN, $text, $matches)) {
            switch (strtolower($matches[2])) {
                case 'i':    $capo = 1; break;
                case 'ii':   $capo = 2; break;
                case 'iii':  $capo = 3; break;
                case 'iv':   $capo = 4; break;
                case 'v':    $capo = 5; break;
                case 'vi':   $capo = 6; break;
                case 'vii':  $capo = 7; break;
                case 'viii': $capo = 8; break;
                case 'ix':   $capo = 9; break;
                case 'x':    $capo = 10; break;
                case 'xi':   $capo = 11; break;
                default:     $capo = (int)$matches[2];
            }
        }

        return $capo;
    }

    private function determineTransposeByParameter(string &$text, mixed $transposeBy)
    {
        $originalCapo = self::determineCapo($text);

        if ($transposeBy === 'auto') {
            $transposeBy = $originalCapo;
        }

        // Change string "Capo X" in text
        $text = preg_replace_callback(
            self::CAPO_PATTERN,
            function ($match) use ($originalCapo, $transposeBy) {
                $newCapo = ($originalCapo - $transposeBy);
                $newCapo = self::modulo($newCapo, 12);
                return $match[1] . $newCapo;
            },
            $text
        );

        return $transposeBy;
    }

    /**
     * Transpose a multiline $text by $transposeBy halfnotes
     *
     * @param string $text
     * @param int $transposeBy
     * @param string $mode
     *
     * @return string
     */
    private function transposeText($text, $transposeBy = null, $mode = 'full')
    {
        if (empty($text)) {
            throw new \Exception('No $text given!');
        }

        $out = '';
        // optional spaces and commas
        // root note (X)
        // optional shifter (b or #)
        // optional modifier (m, sus, etc.)
        // optional base note (/Y)
        $spaces = '[\s,\(\)\[\]]';
        $notes = '[ABHCDEFG]';
        $shifter = '[b#]';
        $modifier = 'm|sus|add|dim|maj|aug|2|3|4|5|6|7|9|11|13|#5|b5|b9|#9|#11|°';
        $pattern = "/^$spaces*($notes+$shifter?($modifier){0,4}(\/$notes+$shifter?)?$spaces*)+\$/i";

        foreach (explode("\n", $text) as $line) {
            if (preg_match($pattern, $line)) {
                // chord line!
                if (in_array($mode, ['full', 'chords'])) {
                    $chordLine = preg_replace_callback(
                        $pattern,
                        function ($match) use ($transposeBy) {
                                return call_user_func_array(
                                    __NAMESPACE__ . '\ChordHelper::transposeChordLine',
                                    [$match[0], $transposeBy]
                                );
                            },
                        $line
                    );

                    if ($mode === 'chords') {
                        $chordLine = trim(preg_replace('/\s(\s+)/', ' ', $chordLine), "\t") . PHP_EOL;
                    }

                    $out .= $chordLine . PHP_EOL;
                }
            } elseif (preg_match('/^(#|capo)/i', $line)) {
                // headline or capo line!
                $out .= $line . PHP_EOL;
            } else {
                // text line!
                if (in_array($mode, ['full', 'text'])) {
                    $out .= $line . PHP_EOL;
                }
            }
        }

        return $out;
    }

    /**
     * Transpose a single $line by $transposeBy halfnotes
     *
     * @param string $text
     * @param int $transposeBy
     * @return string
     */
    public static function transposeChordLine($line, $transposeBy = null)
    {
        //print("Transposing line '" . $line . "':");

        $keepSpacesUntouched = false;
        if (!preg_match('/[ ]{2}/i', $line)) {
            $keepSpacesUntouched = true;
        }

        # Process each chord (incl. following whitespace)
        $line = preg_replace_callback(
            '/([ABHCDEFG][^\s]*\s{0,2})/i',
            function ($match) use ($transposeBy, $keepSpacesUntouched) {
                return call_user_func_array(
                    __NAMESPACE__ . '\ChordHelper::transposeChord',
                    [$match[0], $transposeBy, $keepSpacesUntouched]
                );
            },
            $line
        );

        // //print("Done. Now it is: '" + string.rstrip() + "'.\n")
        return rtrim($line);
    }

    #
    # matchObj.group(0):
    # - E
    # - Dm
    # - H7
    # - Bb
    # - A#m7
    # - ...
    #
    /**
     * @param string $text
     * @param int $transposeBy
     * @param bool $keepSpacesUntouched
     * @return string
     */
    private static function transposeChord($chord, $transposeBy, $keepSpacesUntouched)
    {
        $chordLen = strlen($chord);

        #var_dump($chord);

        # Full chords
        preg_match('/([ABHCDEFG][b#]?)([^\/]*)(\/)?(.*)?/i', $chord, $match);
        # Root note only
        $note = $match[1];
        # Appendix, e.g. '7', 'm7' or 'sus2'
        $appendix = $match[2];
        # Optional bass note
        $bass = $match[4];

        // var_dump($chord, $note, $appendix, $bass);
        // die();

        if ($bass) {
            # transpose bass note only and keepSpacesUntouched=True
            # since the spaces will be handled for the full chord later.
            $bass = '/' . self::transposeChord($bass, $transposeBy, true);
        }

        $halftones = ['A', 'Bb', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
        # special handling for german H
        if ($note == 'H') {
            $halftones = ['A', 'Bb', 'H', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
        }

        $index = array_search($note, $halftones);
        $newindex = self::modulo($index+$transposeBy, count($halftones));

        $newchord = $halftones[$newindex] . $appendix . $bass;

        # in case the new chord lost or gained a 'b' or '#'
        if (!$keepSpacesUntouched && (strlen($newchord) != $chordLen)) {
            $diff = $chordLen-strlen($newchord);
            # in case the new chord lost something,
            # add one or more spaces after it
            if ($diff > 0) {
                $newchord .= str_pad(' ', $diff);
            # in case the new chord has gained length
            } else {
                # and it's one or more tailing spaces,
                # cut off the diff-numbered spaces
                while ($diff < 0) {
                    $diff += 1;
                    if (substr($newchord, -1) == ' ') {
                        $newchord = substr($newchord, 0, -1);
                    }
                }
            }
        }

        // print("- transpose " + ("'" + chord.group(0) + "'").ljust(15) + " by " + str(transposeBy) + " to '" + newchord + "'" + (""," (keepSpacesUntouched)")[keepSpacesUntouched])

        return $newchord;
    }

    /**
     * Workaround for weird php modulo returning negative results
     */
    private static function modulo($x, $y)
    {
        $r = $x % $y;
        if ($r < 0) {
            $r += abs($y);
        }
        return $r;
    }
}
