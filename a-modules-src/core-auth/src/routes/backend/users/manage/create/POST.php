<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Routes\Backend\Users\Manage\Create;

use ModernCMS\Module\CoreAuth\Abstractions\Users\User;
use PHPValidation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function ModernCMS\Core\APIs\InfoPopups\register_info_popup;
use function ModernCMS\Core\APIs\Passwords\hash_password;
use function ModernCMS\Core\APIs\Validation\array_validator;
use function ModernCMS\Core\APIs\Validation\convert_error_messages_into_frontend_format;
use function ModernCMS\Core\Helpers\HTTP\form_error_response;
use function ModernCMS\Core\Helpers\HTTP\redirect_response;
use function ModernCMS\Module\CoreAuth\APIs\Users\create_user;
use function PHPValidation\Functions\email;
use function PHPValidation\Functions\maxLength;
use function PHPValidation\Functions\notEmpty;
use function PHPValidation\Functions\required;

/**
 * Request handler that creates users when given the correct request data.
 */
final class POST
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $formFields = $request->getParsedBody();
        $validator = $this->getCreateUserPOSTDataValidator();

        if (!$validator->isValid($formFields))
        {
            $errorMessages = convert_error_messages_into_frontend_format($validator->getErrorMessages());

            return form_error_response('/cms/users/new', $errorMessages, $formFields);
        }

        $user = new User(
            0,
            $formFields['firstname'],
            [],
            $formFields['lastname'],
            $formFields['email'],
            hash_password($formFields['password']),
        );

        $user = create_user($user);

        if ($user === null)
        {
            return form_error_response('/cms/users/new', ['global' => 'User could not be created'], $formFields);
        }

        register_info_popup('User created successfully');

        return redirect_response("/cms/users/{$user->getId()}");
    }

    private function getCreateUserPOSTDataValidator(): ValidatorInterface
    {
        $validators = [
            'firstname' => [required(), notEmpty(), maxLength(32)],
            'lastname' => [required(), notEmpty(), maxLength(32)],
            'email' => [required(), notEmpty(), email()],
            'password' => [required(), notEmpty(), maxLength(256)],
        ];

        $errorMessages = [
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
                'required' => "Enter a password",
                'notEmpty' => "Enter a password",
                'maxLength' => "Password cannot be longer than 256 characters",
            ],
        ];

        return array_validator($validators, $errorMessages);
    }
}
