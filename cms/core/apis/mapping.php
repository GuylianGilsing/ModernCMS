<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Mapping;

use PHPClassMapper\ArrayMapper;
use PHPClassMapper\ArrayMapperInterface;
use PHPClassMapper\Configuration\ArrayMapperConfiguration;
use PHPClassMapper\Configuration\MapperConfiguration;
use PHPClassMapper\Mapper;
use PHPClassMapper\MapperInterface;

use function ModernCMS\Core\APIs\Hooks\Actions\dispatch_action;

function get_class_mapper_instance(): MapperInterface
{
    static $mapper;

    if (!isset($mapper))
    {
        $config = new MapperConfiguration();

        dispatch_action('mcms_configure_class_mappings', [$config]);

        $mapper = new Mapper($config);
    }

    return $mapper;
}

function get_array_mapper_instance(): ArrayMapperInterface
{
    static $mapper;

    if (!isset($mapper))
    {
        $config = new ArrayMapperConfiguration();

        dispatch_action('mcms_configure_array_mappings', [$config]);

        $mapper = new ArrayMapper($config);
    }

    return $mapper;
}
