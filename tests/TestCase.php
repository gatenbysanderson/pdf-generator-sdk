<?php

namespace GatenbySanderson\PdfGeneratorSdk\Tests;


use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        (new Dotenv(dirname(__DIR__)))->load();
    }

    /**
     * @param string $filename
     * @param string $directory
     * @return string
     */
    protected function getFileContents($filename, $directory = 'storage')
    {
        $path = sprintf('%s/%s/%s', dirname(__FILE__), $directory, $filename);

        return file_get_contents($path);
    }

    /**
     * @param $filename
     * @param string $contents
     * @param string $directory
     * @return string
     */
    protected function putFileContents($filename, $contents, $directory = 'results')
    {
        $path = sprintf('%s/%s/%s', dirname(__FILE__), $directory, $filename);

        var_dump($path);

        return file_put_contents($path, $contents);
    }
}
