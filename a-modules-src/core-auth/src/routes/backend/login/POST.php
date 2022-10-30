<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Routes\Backend\Login;

use PHPValidation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function ModernCMS\Core\APIs\Passwords\password_matches_hash;
use function ModernCMS\Core\APIs\Sessions\set_session_data;
use function ModernCMS\Core\APIs\Validation\array_validator;
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
            $errorMessages = $validator->getErrorMessages();

            set_session_data('login_errors', $this->convertErrorMessagesIntoFrontendFormat($errorMessages));
            set_session_data('login_filled_data', $formFields);

            return redirect_response('/cms');
        }

        $user = get_user_by_email($formFields['email']);

        if ($user === null)
        {
            set_session_data('login_errors', ['global' => 'Email and password combination not found']);

            return redirect_response('/cms');
        }

        if (!password_matches_hash($formFields['password'], $user->getPassword()))
        {
            set_session_data('login_errors', ['global' => 'Email and password combination not found']);

            return redirect_response('/cms');
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

    private function convertErrorMessagesIntoFrontendFormat(array $errorMessages): array
    {
        $formattedErrorMessages = [];

        foreach ($errorMessages as $fieldKey => $errors)
        {
            if (!is_array($errors) || count($errors) === 0)
            {
                continue;
            }

            // Select the first result in the error values
            $formattedErrorMessages[$fieldKey] = array_values($errors)[0];
        }

        return $formattedErrorMessages;
    }
}
