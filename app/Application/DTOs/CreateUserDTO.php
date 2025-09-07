<?php

namespace App\Application\DTOs;

class CreateUserDTO
{
    public string $name;
    public string $email;
    public string $role;
    public string $password;

    public function __construct(string $name, string $email, string $role, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
    }
}