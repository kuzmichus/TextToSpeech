<?php
namespace TTS\Driver;

use GuzzleHttp\Exception\RequestException;

abstract class AbstractAdapter
{
    const DEFAULT_LANG = 'en_GB';

    protected $name = null;
    protected $voice = null;
    /** @var \GuzzleHttp\Client $client */
    protected $client = null;

    protected $language = null;
    protected $speed = null;

    private $debug = false;

    protected $languageMap = [
    ];


    public function __construct($language = self::DEFAULT_LANG, $speed = '0.5')
    {
        if ($this->validateLanguage($language)) {
            throw new \Exception('The language is not supported');
        }

        $this->client = new \GuzzleHttp\Client();
        $this->language = $this->languageMap[$language];
    }

    public function setLanguage($language)
    {
        if ($this->validateLanguage($language)) {
            throw new \Exception('The language is not supported');
        }

        $this->language = $this->languageMap[$language];
        return $this;
    }

    protected function validateLanguage($language)
    {
        return !array_key_exists($language, $this->languageMap);
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setSpeed($speed)
    {
        $this->speed = $speed;
        return $this;
    }

    public function getSpeed()
    {
        return $this->speed;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVoice()
    {
        return $this->voice;
    }

    public function reguestGet($params)
    {
        $headers = [
            'User-Agent'    => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.872.0 Safari/535.2'
        ];

        try {
            $response = $this->client->get($this->getUri(), ['debug' => $this->debug, 'query' => $params, 'headers' => $headers]);
        } catch (RequestException $e) {
            echo $e->getMessage();

            if ($e->hasResponse()) {
            }
            die('error');
        }
        return $response->getBody()->getContents();
    }

    abstract public function getUri();
    abstract public function make($text, $fileName);
}
