<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Mappings\Array;

use ModernCMS\Module\CoreAuth\Abstractions\Users\User;
use PHPClassMapper\ArrayMapperInterface;
use PHPClassMapper\Configuration\FromArrayMappingInterface;

use function ModernCMS\Core\Helpers\String\string_contains_text;

final class ToUserTypeMapping implements FromArrayMappingInterface
{
    /**
     * @param array<string, mixed> $source The class that needs to be mapped to a different class.
     * @param array<string, mixed> $contextData An associative array (key => value) that gives the mapper additional
     * data to work with.
     * @param ArrayMapperInterface $mapper An extra mapper instance to map nested arrays with.
     */
    public function mapObject(array $source, array $contextData, ArrayMapperInterface $mapper): object
    {
        $user = new User($source['id']);

        $user->setFirstName($source['firstname']);
        $user->setMiddlenames($this->mapMiddleNames($source['middlenames']));
        $user->setLastName($source['lastname']);
        $user->setEmail($source['email']);
        $user->setPassword($source['password']);

        return $user;
    }

    private function mapMiddleNames(?string $middlenames): array
    {
        if ($middlenames === null)
        {
            return [];
        }

        if (!string_contains_text($middlenames))
        {
            return [];
        }

        return explode(',', $middlenames);
    }
}
