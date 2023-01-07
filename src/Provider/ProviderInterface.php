<?php

declare(strict_types=1);

namespace Bogdra\FourByte\Provider;

interface ProviderInterface
{
    /**
     * @return array<string>
     */
    public function getSignatures(string $fourBytesHex): array;
}