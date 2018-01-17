<?php

namespace GatenbySanderson\PdfGeneratorSdk\Tests;

use GatenbySanderson\PdfGeneratorSdk\PdfGenerator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class PdfGeneratorTest extends TestCase
{
    public function test_class_is_instantiable()
    {
        $pdfGenerator = $this->getClient();

        $this->assertInstanceOf(PdfGenerator::class, $pdfGenerator);
    }

    public function test_pdf_can_be_generated_with_single_file()
    {
        $pdfGenerator = $this->getClient();

        $response = $pdfGenerator->generate(['test.html' => $this->getFileContents('test.html')]);

        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('type', $response['data']);
        $this->assertArrayHasKey('encoding', $response['data']);
        $this->assertArrayHasKey('content', $response['data']);

        $this->savePdf(__FUNCTION__, $response);
    }

    public function test_pdf_can_be_generated_with_two_files()
    {
        $pdfGenerator = $this->getClient();

        $response = $pdfGenerator->generate([
            'test.html' => $this->getFileContents('test.html'),
            'test_2.html' => $this->getFileContents('test_2.html'),
        ]);

        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('type', $response['data']);
        $this->assertArrayHasKey('encoding', $response['data']);
        $this->assertArrayHasKey('content', $response['data']);

        $this->savePdf(__FUNCTION__, $response);
    }

    public function test_exception_is_thrown_with_no_files()
    {
        $this->expectException(ClientException::class);

        $pdfGenerator = $this->getClient();

        $pdfGenerator->generate([]);
    }

    public function test_exception_is_thrown_with_incorrect_files_structure()
    {
        $this->expectException(\InvalidArgumentException::class);

        $pdfGenerator = $this->getClient();

        $pdfGenerator->generate(['exception.html' => []]);
    }

    /**
     * @return \GatenbySanderson\PdfGeneratorSdk\PdfGenerator
     */
    protected function getClient()
    {
        return new PdfGenerator(new Client(['base_uri' => getenv('PDF_GENERATOR_URL')]));
    }

    /**
     * @param array $response
     * @return string
     */
    protected function decodePdf($response)
    {
        return base64_decode($response['data']['content']);
    }

    /**
     * @param string $testName
     * @param array $response
     * @return void
     */
    protected function savePdf($testName, array $response)
    {
        $this->putFileContents($testName . '.pdf', $this->decodePdf($response));
    }
}
