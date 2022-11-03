<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Routes\Backend\Login;

use PHPValidation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function ModernCMS\Core\APIs\Passwords\password_matches_hash;
use function ModernCMS\Core\APIs\Validation\array_validator;
use function ModernCMS\Core\APIs\Validation\convert_error_messages_into_frontend_format;
use function ModernCMS\Core\Helpers\HTTP\form_error_response;
use function ModernCMS\Core\Helpers\HTTP\redirect_response;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\delete_expired_blacklisted_tokens;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\login_user;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_user_by_email;
use function PHPValidation\Functions\email;
use function PHPValidation\Functions\notEmpty;
use function PHPValidation\Functions\required;

final class POST
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $formFields = $request->getParsedBody();
        $validator = $this->getValidator();

        if (!$validator->isValid($formFields))
        {
            $errorMessages = convert_error_messages_into_frontend_format($validator->getErrorMessages());

            return form_error_response('/cms', $errorMessages, $formFields);
        }

        $user = get_user_by_email($formFields['email']);

        if ($user === null)
        {
            return form_error_response('/cms', ['global' => 'Email and password combination not found']);
        }

        if (!password_matches_hash($formFields['password'], $user->getPassword()))
        {
            return form_error_response('/cms', ['global' => 'Email and password combination not found']);
        }

        delete_expired_blacklisted_tokens();
        login_user($user->getId());

        return redirect_response('/cms');
    }

    private function getValidator(): ValidatorInterface
    {
        $validators = [
            'email' => [required(), notEmpty(), email()],
            'password' => [required(), notEmpty()],
        ];

        $errorMessages = [
            'email' => [
                'required' => 'Enter an email address',
                'notEmpty' => 'Enter an email address',
                'email' => 'Enter a valid email address',
            ],
            'password' => [
                'required' => 'Enter a password',
                'notEmpty' => 'Enter a password',
            ],
        ];

        return array_validator($validators, $errorMessages);
    }
}
