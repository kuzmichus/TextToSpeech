<?php

namespace TTS\Helper;

class SplitterText extends AbstractHelper
{
    public function split($text, $numSymbols)
    {
        $lines = [];
        $char = 0;
        $lastPos = 0;
        $goodChar = 0;

        for ($i = 0; $i < mb_strlen($text); $i++) {
            $char++;

            if (mb_substr($text, $i, 1) == ' ') {
                $goodChar = $i;
            }

            if ($char > $numSymbols) {
                $lines[] = trim(mb_substr($text, $lastPos, $goodChar - $lastPos));
                $char = 0;
                $lastPos = $goodChar;
            } elseif (mb_substr($text, $i, 2) == '. ' && ($goodChar - $lastPos > 5)) {
                $goodChar = $i + 1;
                $lines[] = trim(mb_substr($text, $lastPos, $goodChar - $lastPos));
                $char = 0;
                $lastPos = $goodChar;
            } elseif (mb_substr($text, $i, 2) == '! ' && ($goodChar - $lastPos > 5)) {
                $goodChar = $i + 1;
                $lines[] = trim(mb_substr($text, $lastPos, $goodChar - $lastPos));
                $char = 0;
                $lastPos = $goodChar;
            }
        }
        $lines[] = trim(mb_substr($text, $lastPos, $lastPos + $char));
        return $lines;
    }
}
