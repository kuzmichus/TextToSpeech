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
use Psr\Log\NullLogger;

abstract class TextToSpeechAbstract
{
    const VOTE_FEMALE = 'female';
    const VOTE_MALE = 'male';

    protected $driver = null;

    protected $vote = null;

    protected $speaker = '';

    /** @var string */
    protected $cacheDir = null;

    protected $isCached = false;

    protected $results = [];

    protected $debug = false;

    /**
     * @var string[]
     */
    protected $lines = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $cache
     * @param string[] $options
     */
    public function __construct(LoggerInterface $logger = null, array $options = [])
    {
        if (null === $logger) {
            $this->logger = new NullLogger();
        } else {
            $this->logger = $logger;
        }

        if (isset($options['cacheDir'])) {
            $this->cacheDir = $options['cacheDir'];
            $this->isCached = true;
        }

        if (isset($options['debug'])) {
            $this->debug = (bool)$options['debug'];
            $this->isCached = true;
        }
    }

    /**
     * @param string $text
     * @return $this
     */
    public function addText($text)
    {
        $text = trim(str_replace(["\r", "\n", '"', "'", '«', '»'], ' ', $text));
        $text = preg_replace('/([ ]{2,})/', ' ', $text);
        $text = mb_strtolower($text);

        $this->lines[] = $text;

        return $this;
    }

    /**
     * @return $this
     */
    public function process()
    {
        $this->convert();

        return $this;
    }

    public function convert()
    {
        if (0 === count($this->lines)) {
            throw new \LogicException('нет текста');
        }

        foreach ($this->lines as $line) {
            $result = new Result();
            $result->setSource($line);
            $result->setCacheKey($this->calcCacheKey($line));

            $fileName = '';
            if ($this->isCached) {
                $pathParts = [];
                $pathParts[] = $this->cacheDir;
                $pathParts[] = $this->driver;
                $pathParts[] = $this->vote;
                $pathParts[] = $this->speaker;

                $pathParts = array_merge($pathParts, str_split(substr($result->getCacheKey(), 0, 4), 2));

                $fileName = implode('/', $pathParts);

                if (!file_exists($fileName) || !is_dir($fileName)) {
                    mkdir($fileName, 0755, true);
                }

                 $fileName .= '/' . $result->getCacheKey() . '.mp3';
            } else {
                $fileName = tempnam(sys_get_temp_dir(), 'tts-') . '.mp3';
            }
            $result->setFile($fileName);
            $result->export();
            if ($this->isCached && file_exists($fileName)) {
                $result->setCached(true);
                $this->results[] = $result;
            } else {
                $this->synthesize($line, $fileName);
                $this->results[] = $result;
            }
        }

        $this->lines = [];
        return $this;
    }

    public function getResult()
    {
        return $this->results;
    }

    private function calcCacheKey($text)
    {
        return sha1($text);
    }

    /**
     * Синтезирует фразу $text
     *
     * @param string $text Текст для преоброзование
     * @param string $outFile Имя файла с полным путём
     *
     * @return bool true если успешно
     */
    abstract protected function synthesize($text, $outFile);
}
