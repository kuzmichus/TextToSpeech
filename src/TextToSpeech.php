<?php
namespace TTS;

use TTS\Driver\Google;
use TTS\Exceptions\Exception;

/**
 * Class TextToSpeech
 * @package TTS
 */
class TextToSpeech
{
    private $cacheDir = null;
    private $driver = null;
    private $voice = 'famale';

    private $lines = array();
    private $files = array();

    protected $text = '';

    public function __construct($options = null)
    {
        if ($options['cacheDir']) {
            $this->cacheDir = $options['cacheDir'];
        }

        if (isset($options['driver'])) {
            $adapterClass = __NAMESPACE__. '\\Driver\\' . $options['driver'];
        } else {
            $adapterClass = Google::class;
        }

        $this->driver = new $adapterClass('ru_RU', '0.5');
    }

    public function setText($text)
    {
        $text = trim(str_replace(["\r", "\n", '"', "'", '«', '»'], ' ', $text));
        $text = preg_replace('/([ ]{2,})/', ' ', $text);
        $text = mb_strtolower($text);

        $this->text = $text;
        return $this;
    }

    public function clear()
    {
        $this->lines = array();
        $this->files = array();
        return $this;
    }

    public function process()
    {
        $this->textToFile($this->text);
        var_dump($this->files);

        return $this;
    }

    private function textToFile($text)
    {
        if (!$text) {
            return array();
        }
        var_dump($text);
        $cacheDir = $this->getCacheDir();
        if (!is_dir($cacheDir) || !is_writable($cacheDir)) {
            throw new Exception('Can not write to ' . $cacheDir);
        }
        $cacheDir = dirname($this->prepareFileName($text));
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $fileName = $this->prepareFileName($text);
        if (true || !is_file($fileName)) {
            $content = $this->driver->make($text, $fileName);
            file_put_contents($fileName, $content);
            var_dump($fileName);
        }
        //$ttsCache = \TtsCache::fundByUiq($uiq);
        //if ($ttsCache) {
        //    $ttsCache->incrementTimes()->save();
        // } else {
        //    $ttsCache = new \TtsCache();
        //    if (!$ttsCache->setUiq($uiq)->setString($text)->setVoiceFemale()->save()) {
        //        $message = 'SQL Eroor: '. PHP_EOL;
        //        foreach ($ttsCache->getMessages() as $m) {
        //            $message .= $m . PHP_EOL;
        //        }
        //        throw new \TTS\Exceptions($message);
        //    }
        //}
        //unset($ttsCache);


        $this->files[] = $fileName;


        return $this;
    }

    protected function getTempFile()
    {
        return tempnam(sys_get_temp_dir(), 'tts');
    }

    public function getFiles()
    {
        return $this->files;
    }

    private function getCacheDir()
    {
        return $this->cacheDir
        . '/' . strtolower($this->driver->getName())
        . '/' .$this->driver->getVoice() . '/';
    }

    private function prepareFileName($text)
    {
        $fileName = $this->prepareUiq($text);
        return $this->getCacheDir() . '/' . substr($fileName, 0, 2) . '/' . substr(
            $fileName,
            2,
            2
        ) . '/' . $fileName . '.mp3';
    }

    private function prepareUiq($text)
    {
        return sha1($text);
    }
}
