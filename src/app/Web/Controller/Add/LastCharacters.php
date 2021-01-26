<?php
namespace App\Web\Controller\Add;

trait LastCharacters
{
    public function getLastChar(string $needle, string $haystack): string
    {
        $position = strpos($haystack,$needle) + 1;

        return substr($haystack, $position);
    }
}