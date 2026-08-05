<?php

namespace Modules\User\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('core::components.layouts.public', ['title' => 'Active Sessions'])]
class ActiveSessions extends Component
{
    public function revoke(string $sessionId): void
    {
        // Revoking your own current session here would just log you out
        // mid-page with no feedback — that's what the Logout button is for.
        if ($sessionId === session()->getId()) {
            return;
        }

        DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->delete();
    }

    public function revokeOthers(): void
    {
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', session()->getId())
            ->delete();
    }

    protected function describeAgent(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown device';
        }

        $browser = match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') && ! str_contains($userAgent, 'Chrome/') => 'Safari',
            default => 'Unknown browser',
        };

        $platform = match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS') => 'Mac',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown OS',
        };

        return "{$browser} on {$platform}";
    }

    public function render()
    {
        $sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) {
                $session->is_current_device = $session->id === session()->getId();
                $session->device = $this->describeAgent($session->user_agent);
                $session->last_active = \Illuminate\Support\Carbon::createFromTimestamp($session->last_activity)->diffForHumans();

                return $session;
            });

        return view('user::livewire.active-sessions', ['sessions' => $sessions]);
    }
}
