<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Routes\Backend\Users\Manage;

use ModernCMS\Module\CoreAuth\Abstractions\Users\User;
use Psr\Http\Message\ResponseInterface;

use function ModernCMS\Core\APIs\Sessions\delete_session_if_exists;
use function ModernCMS\Core\APIs\Sessions\get_session_data;
use function ModernCMS\Core\APIs\Sessions\session_exists;
use function ModernCMS\Core\APIs\Views\view_response;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_user_by_id;

final class GET
{
    public function __invoke(string $userId): ResponseInterface
    {
        $templateData = [
            'user' => null,
        ];

        if (session_exists('validation_errors') && session_exists('filled_data'))
        {
            $validationErrors = get_session_data('validation_errors');
            $filledData = get_session_data('filled_data');

            $templateData['validation_errors'] = $validationErrors;

            $templateData['user'] = new User(
                0,
                $filledData['firstname'],
                [],
                $filledData['lastname'],
                $filledData['email'],
                $filledData['password']
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