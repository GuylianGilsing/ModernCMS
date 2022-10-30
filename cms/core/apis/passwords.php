<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Passwords;

function hash_password(string $plainTextPassword): string
{
    return password_hash($plainTextPassword, PASSWORD_BCRYPT);
}

function password_matches_hash(string $plainTextPassword, string $hash): bool
{
    return password_verify($plainTextPassword, $hash);
}
