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

use TTS\Helper\SplitterText;


class iSpeech extends AbstractAdapter
{
    protected $name = 'iSpeech';
    protected $voice = 'male';

    protected $speed = 0;

    protected $languageMap = [
        'en_GB' => 'en',
        'en_EN' => 'en',
        'ru_RU' => 'rurussianmale'
    ];

    public function getUri()
    {
        return 'http://www.ispeech.org/p/generic/getaudio';
    }

    public function make($text, $fileName)
    {
        $sp = new SplitterText();
        $text = iconv('UTF-8', 'UTF-8', $text);

        $lines = $sp->split($text, 100);
        $content = '';
        foreach ($lines as $line) {
            $query = [
                'voice'     => $this->getLanguage(),
                'speed'     => $this->getSpeed(),
                'text'      => $line,
                'action'    => 'convert'
            ];
            $content .= $this->reguestGet($query);
        }
        return $content;
    }
}
