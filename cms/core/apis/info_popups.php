<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\InfoPopups;

use function ModernCMS\Core\APIs\Sessions\delete_session_if_exists;
use function ModernCMS\Core\APIs\Sessions\get_session_data;
use function ModernCMS\Core\APIs\Sessions\session_exists;
use function ModernCMS\Core\APIs\Sessions\set_session_data;
use function ModernCMS\Core\APIs\Views\get_twig_environment_instance;

/**
 * Registers an info popup that will be displayed inside the backend layout.
 *
 * @param string $message The message that you want to display inside the info popup.
 */
function register_info_popup(string $message): void
{
    $registeredInfoPopups = [];

    if (session_exists('mcms_backend_info_popups'))
    {
        $rawInfoPopups = get_session_data('mcms_backend_info_popups');

        if (is_array($rawInfoPopups))
        {
            $registeredInfoPopups = $rawInfoPopups;
        }
    }

    $registeredInfoPopups[] = $message;

    set_session_data('mcms_backend_info_popups', $registeredInfoPopups);
}

/**
 * Renders all registered info popups to a safe HTML string.
 */
function render_info_popups(): string
{
    $infoPopupMessages = [];
    $twig = get_twig_environment_instance();

    if (session_exists('mcms_backend_info_popups'))
    {
        $infoPopupMessages = get_session_data('mcms_backend_info_popups');
    }

    return $twig->render('/backend/partials/info-popups.twig', ['messages' => $infoPopupMessages]);
}

/**
 * Clears all registered info popups.
 */
function clear_info_popups(): void
{
    delete_session_if_exists('mcms_backend_info_popups');
}
