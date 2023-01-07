<?php

declare(strict_types=1);

namespace Bogdra\FourByte\Provider;

use GuzzleHttp\Exception\GuzzleException;
use \JsonException;

final class FourByteProvider extends HttpProvider implements ProviderInterface
{
    private const BASE_URI = 'https://www.4byte.directory/';
    private const RELATIVE_URI = 'api/v1/signatures/?hex_signature=';
    private  const TIMEOUT = 5;
    public function __construct()
    {
        parent::__construct(self::BASE_URI, self::RELATIVE_URI, self::TIMEOUT);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     *
     * @return array<string>
     */
    public function getSignatures(string $fourBytesHex): array
    {
       $output = [];
       foreach ($this->get($fourBytesHex)['results'] as $result){
           $output[] = $result['text_signature'];
       }

       return $output;
    }
}
