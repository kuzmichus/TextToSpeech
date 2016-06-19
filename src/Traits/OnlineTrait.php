<?php
/**
 *
 * PHP version 5.5
 *
 * @package TTS\Traits
 * @author  Sergey V.Kuzin <sergey@kuzin.name>
 * @license MIT
 */

namespace TTS\Traits;


use GuzzleHttp\Exception\RequestException;

trait OnlineTrait
{

    public function reguestGet($params)
    {
        $headers = [
            'User-Agent'    => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.872.0 Safari/535.2'
        ];

        $this->client = new \GuzzleHttp\Client();

        try {
            $response = $this->client->get($this->getUri(), ['debug' => $this->debug, 'query' => $params, 'headers' => $headers]);
        } catch (RequestException $e) {
            echo $e->getMessage();
            var_dump($e->getResponse()->getStatusCode());
            var_dump($e->getResponse()->getHeaders());
            if ($e->hasResponse()) {
            }
            die('error');
        }
        return $response->getBody()->getContents();
    }

    /**
     * Uri до сервера конвертации
     *
     * @return string
     */
    abstract protected function getUri();
}
