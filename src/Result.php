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


class Result
{
    protected $source;

    protected $cacheKey;

    protected $file;

    /** @var \DateTime */
    protected $createdAt;

    /** @var \DateTime */
    protected $lastAccess;

    protected $count = 0;

    protected $cached = false;

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * @param mixed $cacheKey
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
        $this->import();
        return $this;
    }

    public function setCached($cached = true)
    {
        $this->cached = (bool)$cached;
        return $this;
    }

    public function isCached()
    {
        return $this->cached;
    }

    public function import()
    {
        $file = $this->file . '.json';

        if (file_exists($file)) {
            $data = json_decode(
                file_get_contents($file),
                true
            );
            $this->createdAt = new \DateTime($data['createdAt']);
            $this->count = $data['count'];
        } else {
            $this->createdAt = new \DateTime();
            $this->count = 1;
        }
    }

    /**
     *
     */
    public function export()
    {
        $file = $this->file . '.json';

        file_put_contents($file, json_encode([
            'createdAt' => $this->createdAt->format(\DateTime::RFC3339),
            'source' => $this->source,
            'count' => ++$this->count,
            'lastAccess' => date(\DateTime::RFC3339)
        ], JSON_UNESCAPED_UNICODE));

    }
}
