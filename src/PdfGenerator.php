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
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\ClientException
     */
    public function generate(array $files, array $options = [])
    {
        $files = $this->getFiles($files);
        $options = $this->getOptions($options);
        $multipart = array_merge($files, $options);
        $data = ['multipart' => $multipart];

        $response = $this->client->post('api/v1/pdf', $data);

        return json_decode((string)$response->getBody(), static::JSON_DECODE_ASSOC);
    }

    /**
     * @param array $files
     * @return array
     */
    protected function getFiles(array $files)
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

    /**
     * @param array $options
     * @return array
     */
    protected function getOptions(array $options)
    {
        if (empty($options)) {
            return [];
        }

        return $this->flatten($options, 'options[', ']');
    }

    /**
     * Used for turning an array into a PHP friendly name.
     * @link https://stackoverflow.com/a/46276858 Source of solution
     *
     * @param $array
     * @param string $prefix
     * @param string $suffix
     * @param int $i
     * @return array
     */
    protected function flatten($array, $prefix = '[', $suffix = ']', $i = 0)
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($i === 0) {
                    $result = $result + $this->flatten($value, $key . $prefix, $suffix, $i);
                } else {
                    foreach ($this->flatten($value, $prefix . $key . $suffix . '[', $suffix, $i) as $k => $v) {
                        $result[] = $v;
                    }
                }
            } else {
                $result[] = ['name' => $prefix . $key . $suffix, 'contents' => $value];
            }

            $i++;
        }

        return $result;
    }

    public function fail_on_purpose(){
        echo 'this should fail on purpose'  ;
    }
}
