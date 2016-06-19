<?php


namespace TTS\Driver;

use TTS\Helper\SplitterText;

class Yandex extends AbstractAdapter
{
    protected $name = 'Yandex';
    protected $voice = 'famale';

    protected $languageMap = [
        'en_GB' => 'en_GB',
        'en_EN' => 'en_GB',
        'ru_RU' => 'ru_RU',
        'fr_FR' => 'fr_FR',
        'de_DE' => 'de_DE'
    ];

    public function make($text, $fileName)
    {
        $sp = new SplitterText();
        $text = iconv('UTF-8', 'UTF-8', $text);

        $lines = $sp->split($text, 512);
        $content = '';
        foreach ($lines as $line) {
            $query = [
                'format'    => 'mp3',
                'lang'       => $this->getLanguage(),
                'quality'   => 'hi',
                'text'      => $line,
                'speaker'   => 'omazh', // jane, omazh, zahar, ermil
                'mixed'   => 'mixed', // good (доброжелательный), neutral(нейтральный), evil (злой), mixed (переменная окраска).
                //'drunk' => 'true',
                //'robot' => 'true',
                //'ill'   => 'true',
                'key'       => '7dc83e6f-7a54-4fac-a651-0ef471177aa3'
            ];
            $content .= $this->reguestGet($query);
        }
        return $content;
    }

    public function getUri()
    {
        //return 'http://tts.voicetech.yandex.net/tts';
        return 'https://tts.voicetech.yandex.net/generate';
    }
}
