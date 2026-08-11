<?php

namespace Modules\Cms\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Cms\Contracts\TeamMemberLookupContract;
use Modules\Cms\Models\TeamMember;

class TeamMemberLookupService implements TeamMemberLookupContract
{
    public function findFounder(): ?array
    {
        return Cache::remember('team_members.founder', now()->addMinutes(5), fn () => TeamMember::query()
            ->where('is_active', true)
            ->where('title', 'Founder')
            ->orderBy('order_index')
            ->first()
            ?->toArray());
    }
}
