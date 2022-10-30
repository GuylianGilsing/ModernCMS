<?php

declare(strict_types=1);

namespace ModernCMS\Core\Stores\Assets;

use ErrorException;
use ModernCMS\Core\Abstractions\Assets\AssetsStoreInterface;
use ModernCMS\Core\Abstractions\Assets\AssetType;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

final class AssetsStore implements AssetsStoreInterface
{
    /**
     * @var array<string, array<string, array<string, string>>> $assets
     */
    private array $assets = [];

    public function has(string $moduleName, AssetType $assetType, string $fileId): bool
    {
        if (!array_key_exists($moduleName, $this->assets))
        {
            return false;
        }

        if (!array_key_exists($assetType->value, $this->assets[$moduleName]))
        {
            return false;
        }

        if (!array_key_exists($fileId, $this->assets[$moduleName][$assetType->value]))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws ErrorException when the given modulename, assettype, or fileId does exist.
     */
    public function register(string $moduleName, AssetType $assetType, string $fileId, string $filePath): void
    {
        if ($this->has($moduleName, $assetType, $fileId))
        {
            throw new ErrorException("
                Asset, with module name \"{$moduleName}\" type \"{$assetType}\" and file ID \"{$fileId}\",
                has already been registered.
            ");
        }

        if (!array_key_exists($moduleName, $this->assets))
        {
            $this->assets[$moduleName] = [];
        }

        if (!array_key_exists($assetType->value, $this->assets[$moduleName]))
        {
            $this->assets[$moduleName][$assetType->value] = [];
        }

        $this->assets[$moduleName][$assetType->value][$fileId] = $filePath;
    }

    /**
     * @throws ErrorException when the given modulename, assettype, or fileId does not exist.
     */
    public function serve(string $moduleName, AssetType $assetType, string $fileId): ResponseInterface
    {
        if (!$this->has($moduleName, $assetType, $fileId))
        {
            throw new ErrorException("
                Asset, with module name \"{$moduleName}\" type \"{$assetType->value}\" and file ID \"{$fileId}\",
                hasn't been registered.
            ");
        }

        $file = $this->assets[$moduleName][$assetType->value][$fileId];
        $content = file_get_contents($file);

        $response = new Response();
        $response->getBody()->write($content);

        switch ($assetType)
        {
            case AssetType::CSS:
                $response = $response->withAddedHeader('Content-Type', 'text/css');
                break;

            case AssetType::JAVASCRIPT:
                $response = $response->withAddedHeader('Content-Type', 'text/javascript');
                break;

            case AssetType::IMAGE:
                $response = $response->withAddedHeader('Content-Type', mime_content_type($file));
                break;
        }

        return $response->withHeader('Content-Length', filesize($file))
                        ->withHeader('Content-Disposition', 'inline; filename='.basename($file));
    }
}
