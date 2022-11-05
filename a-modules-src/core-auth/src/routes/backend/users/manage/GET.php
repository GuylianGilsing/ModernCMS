<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Routes\Backend\Users\Manage;

use ModernCMS\Module\CoreAuth\Abstractions\Authorization\UserPermissions;
use ModernCMS\Module\CoreAuth\Abstractions\Authorization\UserRoles;
use ModernCMS\Module\CoreAuth\Abstractions\Users\User;
use Psr\Http\Message\ResponseInterface;

use function ModernCMS\Core\APIs\Sessions\delete_session_if_exists;
use function ModernCMS\Core\APIs\Sessions\get_session_data;
use function ModernCMS\Core\APIs\Sessions\session_exists;
use function ModernCMS\Core\APIs\Views\view_response;
use function ModernCMS\Core\Helpers\HTTP\redirect_response;
use function ModernCMS\Module\CoreAuth\APIs\Authorization\get_registered_permissions;
use function ModernCMS\Module\CoreAuth\APIs\Authorization\get_registered_roles;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_logged_in_user;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_user_by_id;

final class GET
{
    public function __invoke(string $userId): ResponseInterface
    {
        $loggedInUser = get_logged_in_user();

        if ($loggedInUser === null)
        {
            return redirect_response('/cms');
        }

        if (!$loggedInUser->hasPermissions([UserPermissions::EDIT_USER_INFO->value]))
        {
            return redirect_response('/cms/users');
        }

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

        if (
            $templateData['user']->getRole() === UserRoles::ADMINISTRATOR->value &&
            !$loggedInUser->hasPermission(UserPermissions::EDIT_ADMINISTRATORS->value))
        {
            return redirect_response('/cms/users');
        }

        $response = view_response('/backend/users/manage.twig', $templateData);

        delete_session_if_exists('validation_errors');
        delete_session_if_exists('filled_data');

        return $response;
    }
}
