<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Validation;

use PHPValidation\Builders\ValidatorBuilder;
use PHPValidation\Strategies\DefaultValidationStrategy;
use PHPValidation\ValidatorInterface;

/**
 * Creates a new array validator instance.
 *
 * @param array<string, FieldValidatorInterface|array<string, mixed>> $validators An infinite associative array
 * where each level has a key => FieldValidatorInterface pair.
 * ```
 * [
 *     'field1' => [required(), notEmpty(), minLength(6), maxLength(32)],
 *     'nestedField' => [
 *         'field1' => [isNumber(), between(4, 21)],
 *     ],
 * ]
 * ```
 *
 * @param array<string, string|array<string, mixed>> $errorMessages An infinite associative array
 * where each field has a key => string pair that displays a custom error message.
 * ```
 * [
 *     'field1' => [
 *         'required' => "Field1 is required",
 *         'notEmpty' => "Field1 must be filled in",
 *         'minLength' => "Field1 must be at least 6 characters long",
 *         'maxLength' => "Field1 cannot be longer than 32 characters",
 *     ],
 *     'nestedField' => [
 *         'field1' => [
 *             'isNumber' => "Field1 must be a number",
 *             'between' => "Field1 must be between 4 and 21",
 *         ],
 *     ],
 * ]
 * ```
 */
function array_validator(array $validators, array $errorMessages = []): ValidatorInterface
{
    static $builder;

    if (!isset($builder))
    {
        $builder = new ValidatorBuilder();

        $builder->setStrategy(new DefaultValidationStrategy());
        $builder->setValidators($validators);
        $builder->setErrorMessages($errorMessages);
    }

    return $builder->build();
}

/**
 * @param array<string, string|array<string, mixed>> $errorMessages An infinite associative array
 * where each field has a key => string pair that displays a custom error message.
 * ```
 * [
 *     'field1' => [
 *         'required' => "Field1 is required",
 *         'notEmpty' => "Field1 must be filled in",
 *         'minLength' => "Field1 must be at least 6 characters long",
 *         'maxLength' => "Field1 cannot be longer than 32 characters",
 *     ],
 *     'nestedField' => [
 *         'field1' => [
 *             'isNumber' => "Field1 must be a number",
 *             'between' => "Field1 must be between 4 and 21",
 *         ],
 *     ],
 * ]
 * ```
 *
 * @return array<string, string>
 */
function convert_error_messages_into_frontend_format(array $errorMessages): array
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
