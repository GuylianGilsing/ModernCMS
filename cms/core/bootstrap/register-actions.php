<?php

declare(strict_types=1);

namespace ModernCMS\Core\Bootstrap;

use function ModernCMS\Core\APIs\Hooks\Actions\register_action;

register_action('mcms_core_hooks_initialized');
register_action('mcms_add_dependency_container_definitions');
register_action('mcms_register_backend_routes');
register_action('mcms_backend_base_group');
register_action('mcms_initialized');
register_action('mcms_configure_class_mappings');
register_action('mcms_configure_array_mappings');
register_action('mcms_extend_twig_environment');
register_action('mcms_register_assets');
register_action('mcms_core_routes_registered');

require_once __DIR__.'/register-actions/extend-twig.php';
require_once __DIR__.'/register-actions/register_dependency-definitions.php';
require_once __DIR__.'/register-actions/register-assets.php';
