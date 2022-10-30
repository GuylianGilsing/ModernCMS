<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\UUID;

use Ramsey\Uuid\Uuid;

function generate_uuid(): string
{
    return Uuid::uuid4()->toString();
}

function generated_uuid_is_valid(string $uuid): bool
{
    return Uuid::isValid($uuid);
}
