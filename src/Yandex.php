<?php
/**
 *
 * PHP version 5.5
 *
 * @package TTS
 * @author  Sergey V.Kuzin <sergey@kuzin.name>
 * @license MIT
 */

namespace TTS;


use Psr\Log\LoggerInterface;
use TTS\Helper\SplitterText;
use TTS\Traits\OnlineTrait;

class Yandex extends TextToSpeechAbstract
{
    use OnlineTrait;

    const SPEAKER_JANE = 'jane';
    const SPEAKER_OKSANA = 'oksana';
    const SPEAKER_ALYSS = 'alyss';
    const SPEAKER_OMAZH = 'omazh';
    const SPEAKER_ZAHAR = 'zahar';
    const SPEAKER_ERMIL = 'ermil';

    protected $driver = 'Yandex';

    protected $vote = self::VOTE_FEMALE;

    protected $speaker = 'oksana';

    protected $apiKey;
    /**
     * @param string $apiKey
     * @param LoggerInterface $logger
     * @param array $options
     */
    public function __construct($apiKey, LoggerInterface $logger = null, array $options = [])
    {
        if (!is_string($apiKey)) {
            throw new \InvalidArgumentException('$apiKey must string');
        }

        $this->apiKey = $apiKey;
        parent::__construct($logger, $options);
    }

    protected function synthesize($text, $outFile)
    {
        $sp = new SplitterText();
        $text = iconv('UTF-8', 'UTF-8', $text);

        $lines = $sp->split($text, 512);
        $content = '';
        foreach ($lines as $line) {
            $query = [
                'format'    => 'mp3',
                'quality'   => 'hi',
                'text'      => $line,
                'speaker'   => $this->speaker, // jane, omazh, zahar, ermil
                'emotion'   => 'good', // neutral (доброжелательный), neutral(нейтральный), evil (злой).
                'lang'      => 'ru‑RU',
                'speed'     => 1.2,
                //'drunk' => 'true',
                //'robot' => 'true',
                //'ill'   => 'true',
                'key'       => $this->apiKey
            ];
            $content .= $this->reguestGet($query);
        }
        file_put_contents($outFile, $content);
        return true;
    }

    public function getUri()
    {
        //return 'http://tts.voicetech.yandex.net/tts';
        return 'https://tts.voicetech.yandex.net/generate';
    }

    /**
     * @return string
     */
    public function getSpeaker()
    {
        return $this->speaker;
    }

    public function setOksanaSpeaker()
    {
        $this->speaker = self::SPEAKER_OKSANA;
        $this->vote = self::VOTE_FEMALE;
    }

    public function setAlyssSpeaker()
    {
        $this->speaker = self::SPEAKER_ALYSS;
        $this->vote = self::VOTE_FEMALE;
    }

    public function setJaneSpeaker()
    {
        $this->speaker = self::SPEAKER_JANE;
        $this->vote = self::VOTE_FEMALE;
    }

    public function setOmazhSpeaker()
    {
        $this->speaker = self::SPEAKER_OMAZH;
        $this->vote = self::VOTE_FEMALE;
    }

    public function setZaharSpeaker()
    {
        $this->speaker = self::SPEAKER_ZAHAR;
        $this->vote = self::VOTE_MALE;
    }

    public function setErmilSpeaker()
    {
        $this->speaker = self::SPEAKER_ERMIL;
        $this->vote = self::VOTE_MALE;
    }

    /**
     * @param string $speaker
     */
    public function setSpeaker($speaker)
    {
        $this->speaker = $speaker;
        return $this;
    }

}
