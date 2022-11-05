<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Abstractions\Users;

class User
{
    protected int $id;
    protected string $firstname;

    /**
     * @var array<string> $middlenames
     */
    protected array $middlenames;
    protected string $lastname;
    protected string $email;
    protected string $password;
    protected string $role;

    /**
     * @var array<string> $permissions
     */
    protected array $permissions;

    /**
     * @param array<string> $middlenames
     * @param array<string> $permissions
     */
    public function __construct(
        int $id,
        string $firstname = '',
        array $middlenames = [],
        string $lastname = '',
        string $email = '',
        string $password = '',
        string $role = '',
        array $permissions = []
    ) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->middlenames = $middlenames;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->permissions = $permissions;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstName(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return array<string>
     */
    public function getMiddlenames(): array
    {
        return $this->middlenames;
    }

    /**
     * @param array<string> $middlenames
     */
    public function setMiddlenames(array $middlenames): void
    {
        $this->middlenames = $middlenames;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastName(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string The hashed password.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Sets a new hashed password.
     */
    public function setPassword(string $hash): void
    {
        $this->password = $hash;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    /**
     * @param array<string> $permissions
     */
    public function hasPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission)
        {
            if (!$this->hasPermission($permission))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<string>
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param array<string> $permissions
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }
}
