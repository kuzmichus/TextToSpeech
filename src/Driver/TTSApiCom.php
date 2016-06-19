<?php
/**
 *
 * PHP version 5.5
 *
 * @package TTS\Driver
 * @author  Sergey V.Kuzin <sergey@kuzin.name>
 * @license MIT
 */

namespace TTS\Driver;


class TTSApiCom extends AbstractAdapter
{
    protected $name = 'TTSApiCom';
    protected $voice = 'male';

    protected $speed = 0;

    protected $languageMap = [
        'en_GB' => 'en'
    ];

    public function getUri()
    {
        return 'http://tts-api.com/tts.mp3';
    }

    public function make($text, $fileName)
    {
        $sp = new SplitterText();
        $text = iconv('UTF-8', 'UTF-8', $text);

        $lines = $sp->split($text, 1000);
        $content = '';
        foreach ($lines as $line) {
            $query = [
                'q' => $line
            ];
            $content .= $this->reguestGet($query);
        }
        return $content;
    }
}

