# Modern CMS
## PHP information
- Minimum PHP version: 8.1.0

Must have the following PHP extensions enabled:

- curl
- fileinfo
- mbstring
- openssl
- sodium

## Hooks
**Actions**<br/>
| Action name | Arguments | Description |
|-------------|-----------|-------------|
| mcms_core_hooks_initialized | | Gets called immediately after all autoloading, config file loading, and hook registering has been done. It is recommended to use this hook for registering actions and filters, and registering action and filter callbacks that need to hook into core system features. |
| mcms_add_dependency_container_definitions | `DI\ContainerBuilder` $builder | Hook for registering definitions for the dependency injection process |
| mcms_register_backend_routes | `Slim\Interfaces\RouteCollectorProxyInterface` $router | Hook for registering routes that are used inside the CMS frontend |
| mcms_backend_base_group | `Slim\Interfaces\RouteGroupInterface` $group | Hook for altering the behavior of the base group route that displays the CMS backend |
| mcms_initialized | | Hook that fires after the system has been initialized. It is recommended to use this hook for registering action and filter callbacks. |
| mcms_configure_class_mappings | `PHPClassMapper\Configuration\MapperConfigurationInterface` $configuration | Configures all available class mappings |
| mcms_configure_array_mappings | `PHPClassMapper\Configuration\ArrayMapperConfigurationInterface` $configuration | Configures all available array mappings |
| mcms_extend_twig_environment | `Twig\Environment` $twig | Used for extending the twig templating system with functions, globals, etc. |
| mcms_register_assets | `ModernCMS\Core\Abstractions\Assets\AssetsStoreInterface` $store | Used for registering all static assets available to the system. |
| mcms_core_routes_registered | | Gets called immediately after all core routes have been registered. |

**Filters**<br/>
| Filter name | Arguments | Description |
|-------------|-----------|-------------|
| mcms_get_twig_template_folder_paths | `array<string>` $paths | Retrieves all twig template folder paths that are fed into the view system |
| mcms_get_migrations | `array<ModernCMS\Core\Abstractions\Migrations\MigrationInterface>` $migrations | Retrieves all available migrations |
| mcms_extend_cms_main_header_right_side | `string` $renderedTemplate | Retrieves rendered templates that are displayed within the CMS system's main header on the right side. |
| mcms_ui_main_sidebar_items | `array<string, SidebarSection> $items` $items | Retrieves all CMS main sidenav items |

## Twig template syntax extensions
The table below displays all extra functions, filters, and global variables that can be used inside a twig template.

| Type | Name | Arguments | Description | Usage |
|------|------|-----------|-------------|-------|
| Function | site_name | | Returns the configured name of the site. This is mainly used in a HTML `<title></title>` tag. | {{ site_name() }} |
| Function | site_url | `string` $path | Prepends the appropiate http protocol and host name to a given path. | {{ site_url('/my-page') }} |
| Function | backend_url | `string` $path | Prepends the backend URL to a given path. | {{ backend_url('/my-backend-page') }} |
| Function | csrf_fields | | Inject HTML csrf fields for protected (POST) form submissions | {{ csrf_fields() }} |
| Function | mcms_ui_cms_main_header_right_side_content | | Injects registered content in the right side of the main header. | {{ mcms_ui_cms_main_header_right_side_content() }} |
| Function | mcms_ui_main_sidenav_items | | Injects registered content into the main sidenav, | {{ mcms_ui_main_sidenav_items() }}
| Function | table_pagination | `ModernCMS\Core\Abstractions\Pagination\\PpaginatedResult` $page | Injects a pagination element that can be used with tables into the template. | {{ table_pagination(YOUR_PAGINATED_RESULT_VAR_HERE) }} |
| Function | mcms_backend_info_popups | | Displays info popups | {{ mcms_backend_info_popups() }} |
# Frontend dev
```bash
$ npx tailwindcss -i ./frontend-src/tailwind.css -o ./public/assets/core/css/main.css --watch
```
