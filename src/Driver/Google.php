<?php


namespace TTS\Driver;

use TTS\Helper\SplitterText;

class Google extends AbstractAdapter
{
    protected $name = 'Google';
    protected $voice = 'famale';

    protected $languageMap = [
        'en_GB' => 'en',
        'en_EN' => 'en',
        'ru_RU' => 'ru'
    ];



    public function make($text, $fileName)
    {
        $sp = new SplitterText();
        $text = iconv('UTF-8', 'UTF-8', $text);

        $lines = $sp->split($text, 100);
        $content = '';
        foreach ($lines as $line) {
            $query = [
                'tl'        => $this->getLanguage(),
                'ttsspeed'  => $this->getSpeed(),
                'q'         => $line,
            ];
            $content .= $this->reguestGet($query);
        }
        return $content;
    }

    public function getUri()
    {
        return 'http://translate.google.com/translate_tts';
    }
}
