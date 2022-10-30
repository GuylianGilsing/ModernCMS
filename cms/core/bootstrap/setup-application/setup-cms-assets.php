<?php

declare(strict_types=1);

namespace ModernCMS\Core\Bootstrap;

use ModernCMS\Core\Abstractions\Assets\AssetType;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

use function ModernCMS\Core\APIs\Application\get_application_instance;
use function ModernCMS\Core\APIs\Assets\get_assets_store;
use function ModernCMS\Core\Helpers\HTTP\notfound_response;

$app = get_application_instance();

$app->group('/cms/assets', function (RouteCollectorProxy $group)
{
    $group->get('/{moduleName}/{assetType}/{fileId}', function (string $moduleName, string $assetType, string $fileId)
    {
        $assetsStore = get_assets_store();

        if (!$assetsStore->has($moduleName, AssetType::from($assetType), $fileId))
        {
            return notfound_response();
        }

        return $assetsStore->serve($moduleName, AssetType::from($assetType), $fileId);
    });
});
