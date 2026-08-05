<?php

namespace Modules\User\Services;

use App\Models\User;
use Modules\User\Contracts\UserLookupContract;

class UserLookupService implements UserLookupContract
{
    public function find(string $id): ?array
    {
        $user = User::find($id);

        return $user ? $this->toArray($user) : null;
    }

    public function findMany(array $ids): array
    {
        return User::whereIn('id', $ids)->get()
            ->mapWithKeys(fn (User $user) => [$user->id => $this->toArray($user)])
            ->all();
    }

    public function hasAnyRole(string $id, array $roles): bool
    {
        $user = User::find($id);

        return (bool) $user?->hasAnyRole($roles);
    }

    private function toArray(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar_url' => $user->avatar_url,
        ];
    }
}
