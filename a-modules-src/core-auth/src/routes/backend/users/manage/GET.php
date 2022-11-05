<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Routes\Backend\Users\Manage;

use ModernCMS\Module\CoreAuth\Abstractions\Users\User;
use Psr\Http\Message\ResponseInterface;

use function ModernCMS\Core\APIs\Sessions\delete_session_if_exists;
use function ModernCMS\Core\APIs\Sessions\get_session_data;
use function ModernCMS\Core\APIs\Sessions\session_exists;
use function ModernCMS\Core\APIs\Views\view_response;
use function ModernCMS\Module\CoreAuth\APIs\Authorization\get_registered_permissions;
use function ModernCMS\Module\CoreAuth\APIs\Authorization\get_registered_roles;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_user_by_id;

final class GET
{
    public function __invoke(string $userId): ResponseInterface
    {
        $templateData = [
            'user' => null,
            'roles' => get_registered_roles(),
            'permissions' => get_registered_permissions(),
        ];

        if (session_exists('validation_errors') && session_exists('filled_data'))
        {
            $validationErrors = get_session_data('validation_errors');
            $filledData = get_session_data('filled_data');

            $templateData['validation_errors'] = $validationErrors;
            $id = 0;

            if (array_key_exists('id', $filledData) && is_numeric($filledData['id']))
            {
                $id = intval($filledData['id']);
            }

            $permissions = [];

            if (array_key_exists('permissions', $filledData))
            {
                $permissions = array_keys($filledData['permissions']);
            }

            $templateData['user'] = new User(
                $id,
                $filledData['firstname'],
                [],
                $filledData['lastname'],
                $filledData['email'],
                role: $filledData['role'],
                permissions: $permissions
            );
        }
        else if (is_numeric($userId))
        {
            $templateData['user'] = get_user_by_id(intval($userId));
        }

        $response = view_response('/backend/users/manage.twig', $templateData);

        delete_session_if_exists('validation_errors');
        delete_session_if_exists('filled_data');

        return $response;
    }
}
