<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Core\Support\RoleGroups;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request. Matches the source's
     * register flow: always creates a `student` role account, no role
     * selection, and always lands on /student (app/auth/register/page.tsx).
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        // A user with no role is a broken account (every screen in this app
        // assumes exactly one role) — atomic so a role-assignment failure
        // can't leave one behind.
        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole(RoleGroups::STUDENT);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect('/student');
    }
}
