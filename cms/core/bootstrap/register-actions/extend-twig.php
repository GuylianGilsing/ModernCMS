<?php

declare(strict_types=1);

namespace ModernCMS\Core\Bootstrap;

use ModernCMS\Core\Abstractions\Pagination\PaginatedResult;
use ModernCMS\Core\Abstractions\UI\Sidebar\SidebarSection;
use Twig\Environment;

use function ModernCMS\Core\APIs\Application\get_dependency_container_instance;
use function ModernCMS\Core\APIs\Hooks\Actions\register_action_callback;
use function ModernCMS\Core\APIs\Hooks\Filters\dispatch_filter;
use function ModernCMS\Core\Helpers\HTTP\http_protocol_and_host_name;

register_action_callback('mcms_extend_twig_environment', function (Environment $twig)
{
    $twig->addFunction(new \Twig\TwigFunction('site_name', fn() => "ModernCMS"));
    $twig->addFunction(new \Twig\TwigFunction('site_url', fn(string $path = '') => http_protocol_and_host_name().$path));
    $twig->addFunction(new \Twig\TwigFunction('backend_url', fn(string $path = '') => http_protocol_and_host_name().'/cms'.$path));

    $twig->addFunction(
        new \Twig\TwigFunction('csrf_fields', function (): string
        {
            if (!DO_CSRF)
            {
                return '';
            }

            /**
                * @var ?Guard $csrf
                */
            $csrf = get_dependency_container_instance()->get('csrf');

            if ($csrf === null)
            {
                return '';
            }

            return "
                <input type=\"hidden\" name=\"{$csrf->getTokenNameKey()}\" value=\"{$csrf->getTokenName()}\">
                <input type=\"hidden\" name=\"{$csrf->getTokenValueKey()}\" value=\"{$csrf->getTokenValue()}\">
            ";
        },
        ['is_safe' => ['html']])
    );

    $twig->addFunction(
        new \Twig\TwigFunction('mcms_ui_cms_main_header_right_side_content', function (): string
        {
            return dispatch_filter('mcms_extend_cms_main_header_right_side', '');
        },
        ['is_safe' => ['html']])
    );

    $twig->addFunction(
        new \Twig\TwigFunction('mcms_ui_main_sidenav_items', function () use($twig): string
        {
            $items = dispatch_filter('mcms_ui_main_sidebar_items', []);

            return $twig->render('/backend/partials/sidebar-content.twig', ['items' => $items]);
        },
        ['is_safe' => ['html']])
    );

    $twig->addFunction(new \Twig\TwigFunction('table_pagination', function (PaginatedResult $page) use($twig): string
    {
        return $twig->render('/backend/partials/table-pagination.twig', ['page' => $page]);
    },
    ['is_safe' => ['html']]));
});

