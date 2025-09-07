<?php

namespace App\Infrastructure\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function save(User $user): void
    {
        $model = UserModel::find($user->getId()) ?? new UserModel();

        $model->fill([
            'name' => $user->getName(),
            'email' => $user->getEmail()->getValue(),
            'role' => $user->getRole()->getValue(),
            'password' => $user->getPasswordHash(),
        ]);

        $model->save();

        // Update the entity ID if it's a new user
        if (!$user->getId()) {
            $user->setId($model->id);
        }
    }

    public function findById(int $id): ?User
    {
        $model = UserModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function findByEmail(Email $email): ?User
    {
        $model = UserModel::where('email', $email->getValue())->first();

        if (!$model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function findByRole(UserRole $role): array
    {
        $models = UserModel::where('role', $role->getValue())->get();
        
        return $models->map(function ($model) {
            return $this->mapToEntity($model);
        })->toArray();
    }

    public function delete(User $user): void
    {
        UserModel::destroy($user->getId());
    }

    public function existsByEmail(Email $email): bool
    {
        return UserModel::where('email', $email->getValue())->exists();
    }

    private function mapToEntity(UserModel $model): User
    {
        return new User(
            $model->id,
            $model->name,
            new Email($model->email),
            UserRole::fromString($model->role),
            $model->password
        );
    }
}