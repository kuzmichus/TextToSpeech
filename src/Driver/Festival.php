<?php
namespace TTS\Driver;

/**
 * Class Festival
 * @package TTS\Driver
 */
class Festival extends AbstractAdapter
{
    protected $name = 'Festival';
    protected $voice = 'male';

    public function make($text, $fileName)
    {
        system('echo \''. $text .'\'  | /usr/bin/text2wave -o ' . $fileName);
        return $fileName;
    }

    public function getUri()
    {
        return null;
    }
}
