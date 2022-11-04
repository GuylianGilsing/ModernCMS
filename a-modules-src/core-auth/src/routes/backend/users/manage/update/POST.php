<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Routes\Backend\Users\Manage\Update;

use PHPValidation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function ModernCMS\Core\APIs\InfoPopups\register_info_popup;
use function ModernCMS\Core\APIs\Passwords\hash_password;
use function ModernCMS\Core\APIs\Validation\array_validator;
use function ModernCMS\Core\APIs\Validation\convert_error_messages_into_frontend_format;
use function ModernCMS\Core\Helpers\HTTP\form_error_response;
use function ModernCMS\Core\Helpers\HTTP\redirect_response;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_user_by_id;
use function ModernCMS\Module\CoreAuth\APIs\Users\update_user;
use function PHPValidation\Functions\email;
use function PHPValidation\Functions\isInt;
use function PHPValidation\Functions\isNumeric;
use function PHPValidation\Functions\maxLength;
use function PHPValidation\Functions\notEmpty;
use function PHPValidation\Functions\required;

/**
 * Request handler that updates users when given the correct request data.
 */
final class POST
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $formFields = $request->getParsedBody();
        $validator = $this->getCreateUserPOSTDataValidator();

        if (array_key_exists('password', $formFields) && strlen($formFields['password']) === 0)
        {
            unset($formFields['password']);
        }

        if (!$validator->isValid($formFields))
        {
            return $this->errorResponse($validator->getErrorMessages(), $formFields);
        }

        $userId = intval($formFields['id']);
        $user = get_user_by_id($userId);

        if ($user === null)
        {
            return $this->errorResponse(['global' => 'User does not exist.'], $formFields);
        }

        $user->setFirstName($formFields['firstname']);
        $user->setMiddlenames([]);
        $user->setLastName($formFields['lastname']);
        $user->setEmail($formFields['email']);

        if (array_key_exists('password', $formFields))
        {
            $hash = hash_password($formFields['password']);
            $user->setPassword($hash);
        }

        if (!update_user($user))
        {
            register_info_popup('No data has been updated');

            return redirect_response("/cms/users/{$user->getId()}");
        }

        register_info_popup('User updated successfully');

        return redirect_response("/cms/users/{$user->getId()}");
    }

    private function getCreateUserPOSTDataValidator(): ValidatorInterface
    {
        $validators = [
            'id' => [required(), isNumeric(), isInt()],
            'firstname' => [required(), notEmpty(), maxLength(32)],
            'lastname' => [required(), notEmpty(), maxLength(32)],
            'email' => [required(), notEmpty(), email()],
            'password' => [notEmpty(), maxLength(256)],
        ];

        $errorMessages = [
            'id' => [
                'required' => "ID is missing",
                'isNumeric' => "ID must be numeric",
                'isInt' => "ID must be an integer",
            ],
            'firstname' => [
                'required' => "Enter a firstname",
                'notEmpty' => "Enter a firstname",
                'maxLength' => "Firstname cannot be longer than 32 characters",
            ],
            'lastname' => [
                'required' => "Enter a lastname",
                'notEmpty' => "Enter a lastname",
                'maxLength' => "Lastname cannot be longer than 32 characters",
            ],
            'email' => [
                'required' => "Enter an email address",
                'notEmpty' => "Enter an email address",
                'email' => "Enter a valid email address",
            ],
            'password' => [
                'notEmpty' => "Enter a password",
                'maxLength' => "Password cannot be longer than 256 characters",
            ],
        ];

        return array_validator($validators, $errorMessages);
    }

    /**
     * @param array<string, mixed> $errorMessages Raw validator error messages.
     * @param array<string, mixed> $formFields Passed form fields.
     */
    private function errorResponse(array $errorMessages, array $formFields)
    {
        $redirectUrl = '/cms/users/new';
        $errorMessages = convert_error_messages_into_frontend_format($errorMessages);

        if (array_key_exists('id', $formFields) && is_numeric($formFields['id']))
        {
            $redirectUrl = '/cms/users/'.(intval($formFields['id']));
        }

        return form_error_response($redirectUrl, $errorMessages, $formFields);
    }
}
