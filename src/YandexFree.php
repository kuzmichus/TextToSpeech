<?php
/**
 *
 * PHP version 5.5
 *
 * @author  Sergey V.Kuzin <sergey@kuzin.name>
 * @license MIT
 */
namespace TTS;

use TTS\Traits\OnlineTrait;

class YandexFree extends TextToSpeechAbstract
{
    use OnlineTrait;

    protected $driver = 'YandexFree';

    protected $vote = 'female';

    /**
     * @param $text
     * @param $outFile
     * @return bool
     */
    protected function synthesize($text, $outFile)
    {
        $result = false;
        $sp = new \TTS\Helper\SplitterText();
        $text = iconv('UTF-8', 'UTF-8', $text);

        $lines = $sp->split($text, 512);
        $content = '';
        foreach ($lines as $line) {
            $query = [
                'quality'   => 'hi',
                'text'      => $line,
                'format'    => 'mp3',
                'lang'      => 'ru_RU',
                'platform'  => 'web',
                'application' => 'translate'
            ];
            $content .= $this->reguestGet($query);
        }
        if (strlen($content) > 0) {
            $result = true;
            file_put_contents($outFile, $content);
        }
        return $result;
    }

    protected function getUri()
    {
        return 'https://tts.voicetech.yandex.net/tts';
    }
}
