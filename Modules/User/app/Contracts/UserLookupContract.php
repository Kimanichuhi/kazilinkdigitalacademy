<?php

namespace Modules\User\Contracts;

/**
 * The only surface other modules may use to read user/profile data.
 * Other modules must depend on this interface, never on App\Models\User
 * directly, so User internals can change without breaking callers.
 */
interface UserLookupContract
{
    /**
     * @return array{id: string, name: string, email: string, phone: ?string, avatar_url: ?string}|null
     */
    public function find(string $id): ?array;

    /**
     * @param  list<string>  $ids
     * @return array<string, array{id: string, name: string, email: string, phone: ?string, avatar_url: ?string}>
     */
    public function findMany(array $ids): array;

    public function hasAnyRole(string $id, array $roles): bool;
}
