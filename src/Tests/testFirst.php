<?php

namespace App\Tests;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class testFirst extends TestCase
{
    public function testFirst() {
        $test = 'test';
        $this->assertTrue($test === 'test' , "Fail");
    }
}