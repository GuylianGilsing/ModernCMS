<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Assets;

use ErrorException;
use Psr\Http\Message\ResponseInterface;

interface AssetsStoreInterface
{
    /**
     * Checks if the store has an asset.
     */
    public function has(string $moduleName, AssetType $assetType, string $fileId): bool;

    /**
     * Registers an asset.
     *
     * @param string $filePath An ID that the file can be referenced by, without the file extension.
     *
     * @throws ErrorException when the given modulename, assettype, or fileId does exist.
     */
    public function register(string $moduleName, AssetType $assetType, string $fileId, string $filePath): void;

    /**
     * Creates a response with the asset content and header type.
     *
     * @throws ErrorException when the given modulename, assettype, or fileId does not exist.
     */
    public function serve(string $moduleName, AssetType $assetType, string $fileId): ResponseInterface;
}
