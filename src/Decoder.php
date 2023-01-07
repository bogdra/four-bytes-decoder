<?php

declare(strict_types=1);

namespace Bogdra\FourByte;

use Bogdra\FourByte\Exception\InvalidFunctionSignatureException;
use Bogdra\FourByte\Exception\ReadPermissionException;
use Bogdra\FourByte\Exception\WritePermissionException;
use Bogdra\FourByte\Provider\FourByteProvider;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

final class Decoder
{
    public const DS = DIRECTORY_SEPARATOR;
    private const SIGNATURE_REGEX = '/0x[a-fx0-9]{8}/i';
    private const SIGNATURE_SEPARATOR = ';';
    private string $functionsSignaturePath;

    public function __construct()
    {
        $this->functionsSignaturePath = dirname(__DIR__) . self::DS . 'src' . self::DS . 'signatures' . self::DS . 'function';
    }

    /**
     * @throws InvalidFunctionSignatureException
     * @throws WritePermissionException
     * @throws ReadPermissionException
     *
     * @return array<string>
     */
    public function decode(string $hex): array
    {
        if (!preg_match(self::SIGNATURE_REGEX, $hex)) {
            throw new InvalidFunctionSignatureException('Provide a valid hex signature like 0xffffffff');
        }

        $signatures = $this->retrieveFromStorage($hex);
        if (!empty($signatures)) {
           return $signatures;
        }

        $signatures = $this->retrieveFromFourBytes($hex);
        if (!empty($signatures)) {
            $this->saveToStorage($hex, $signatures);
            return $signatures;
        }

        return [];
    }

    /**
     * @throws ReadPermissionException
     *
     * @return array<string>
     */
    private function retrieveFromStorage(string $hex): array
    {
        $hexFile = $this->functionsSignaturePath . self::DS . substr($hex, 2);

        if (file_exists($hexFile) && !is_readable($hexFile)) {
            throw new ReadPermissionException(sprintf('The file %s is not readable', $hexFile));
        }

        if (!file_exists($hexFile) || ($signatures = file_get_contents($hexFile)) === false)
        {
            return [];
        }

        if (str_contains(self::SIGNATURE_SEPARATOR, $signatures)) {
           return explode(self::SIGNATURE_SEPARATOR, $signatures);
        }

        return [$signatures];
    }

    /**
     * @return array<string>
     */
    private function retrieveFromFourBytes(string $hex): array
    {
        try{
            return (new FourByteProvider())->getSignatures($hex);
        } catch (GuzzleException|JsonException) {
            return [];
        }
    }

    /**
     * @param array<string> $signatures
     *
     * @throws WritePermissionException
     */
    private function saveToStorage(string $hex, array $signatures): void
    {
        $cleanHex = substr($hex, 2);
        $isSaved = file_put_contents(
            $this->functionsSignaturePath . self::DS . $cleanHex,
            implode(self::SIGNATURE_SEPARATOR, $signatures)
        );

        if (!$isSaved) {
            throw new WritePermissionException(
                sprintf('The file %s is not writable', $this->functionsSignaturePath. self::DS . $cleanHex)
            );
        }
    }
}
