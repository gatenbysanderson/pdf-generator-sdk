<?php

namespace GatenbySanderson\PdfGeneratorSdk;

use GuzzleHttp\Client;

class PdfGenerator
{
    const JSON_DECODE_ASSOC = true;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * PdfGenerator constructor.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $files
     * @param array $config
     * @return array
     * @throws \GuzzleHttp\Exception\ClientException
     */
    public function generate(array $files, array $config = [])
    {
        $multipart = $this->getMultipart($files);
        $options = ['multipart' => $multipart] + $config;
        $response = $this->client->post('api/v1/pdf', $options);

        return json_decode((string)$response->getBody(), static::JSON_DECODE_ASSOC);
    }

    /**
     * @param array $files
     * @return array
     */
    protected function getMultipart(array $files)
    {
        $multipart = [];

        foreach ($files as $filename => $contents) {
            $multipart[] = [
                'Content-type' => 'multipart/form-data',
                'name' => 'sources[]',
                'filename' => $filename,
                'contents' => $contents,
            ];
        }

        return $multipart;
    }
}
