<?php

declare(strict_types=1);

namespace Bogdra\FourByte\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

class HttpProvider
{
    private ClientInterface $client;

    public function __construct(
        readonly string $baseUri,
        readonly string $relativePath,
        readonly int $timeout
    ) {
        $this->client = new Client(['base_uri' => $baseUri]);
    }

    /**
     * @throws GuzzleException|JsonException
     *
     * @return array<string>
     */
    public function get(string $hex): array
    {
        $options = [
            'headers' => ['Content-Type' => 'application/json'],

            'timeout' => $this->timeout,
            'connect_timeout' => $this->timeout,
        ];

        $stream = ($this->client->request('GET', $this->relativePath . $hex, $options))->getBody();
        $responseArray = \json_decode($stream->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $stream->close();

        return $responseArray;
    }
}
