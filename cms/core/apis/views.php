<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Views;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use function ModernCMS\Core\APIs\Hooks\Actions\dispatch_action;
use function ModernCMS\Core\APIs\Hooks\Filters\dispatch_filter;

function get_twig_environment_instance(): Environment
{
    static $env;

    if (!isset($env))
    {
        $viewFolderPaths = dispatch_filter('mcms_get_twig_template_folder_paths', []);

        $loader = new FilesystemLoader($viewFolderPaths);
        $env = new Environment($loader);

        dispatch_action('mcms_extend_twig_environment', [$env]);
    }

    return $env;
}

/**
 * Renders a new view from the `cms\core\views\twig` folder.
 *
 * @param string $path The path to the template file, starting from the `cms\core\views\twig` folder.
 * @param array<mixed> $templateData Data that needs to be passed to the template.
 */
function render_view(string $path, array $templateData = []): string
{
    $twig = get_twig_environment_instance();

    return $twig->render($path, $templateData);
}

/**
 * Renders a new view from the `cms\core\views\twig` folder and returns it as a response.
 *
 * @param string $path The path to the template file, starting from the `cms\core\views\twig` folder.
 * @param array<mixed> $templateData Data that needs to be passed to the template.
 */
function view_response(string $path, array $templateData = []): ResponseInterface
{
    $response = new Response();

    $response->getBody()->write(render_view($path, $templateData));

    return $response;
}
