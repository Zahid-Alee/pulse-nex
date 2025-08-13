<?php


namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function view()
    {
        $users = User::orderBy('name')
            ->with('subscription')
            ->get()
            ->map(function ($user) {
                return [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'is_admin'       => $user->is_admin,
                    'plan_name'      => optional($user->subscription)->plan_name ?? 'No Plan',
                    'amount'         => optional($user->subscription)->amount ?? 0,
                    'monitors_limit' => optional($user->subscription)->monitors_limit ?? 1,
                    'check_interval' => optional($user->subscription)->check_interval ?? 5,
                ];
            });

        return Inertia::render('users/list', [
            'users' => $users,
        ]);
    }


    public function show(User $user)
    {
        $user->load(['subscription', 'websites']);

        return Inertia::render('users/view', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'created_at' => $user->created_at->format('Y-m-d'),
                'plan_name' => optional($user->subscription)->plan_name ?? 'No Plan',
            ],
            'websites' => $user->websites->map(function ($website) {
                return [
                    'id' => $website->id,
                    'name' => $website->name,
                    'url' => $website->url,
                    'status' => $website->status,
                    'check_interval' => $website->check_interval,
                ];
            }),
        ]);
    }


    public function deleteWebShowUser(User $user)
    {
        $user->load(['subscription', 'websites']);

        return Inertia::render('users/view', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'created_at' => $user->created_at->format('Y-m-d'),
                'plan_name' => optional($user->subscription)->plan_name ?? 'No Plan',
            ],
            'websites' => $user->websites->map(function ($website) {
                return [
                    'id' => $website->id,
                    'name' => $website->name,
                    'url' => $website->url,
                    'status' => $website->status,
                    'check_interval' => $website->check_interval,
                ];
            }),
        ]);
    }


    public function createView()
    {
        return Inertia::render('users/create');
    }

    public function editView(User $user)
    {
        return Inertia::render('users/edit', [
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'is_admin' => 'nullable|boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['is_admin'] = $request->input('is_admin', false);

        $user = User::create($validated);

        $users = User::orderBy('name')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
            ];
        });

        Subscription::create([
            'user_id' => $user->id,
            'plan_name' => 'Free',
            'monitors_limit' => 1,
            'check_interval' => 5,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        return Inertia::render('users/list', [
            'users' => $users,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'is_admin' => 'nullable|boolean',
        ]);

        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_admin = $request->input('is_admin', false);

        $user->save();

        return Inertia::render('users/edit', [
            'user' => $user,
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        $users = User::orderBy('name')
            ->with('subscription')
            ->get()
            ->map(function ($user) {
                return [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'is_admin'       => $user->is_admin,
                    'plan_name'      => optional($user->subscription)->plan_name ?? 'No Plan',
                    'amount'         => optional($user->subscription)->amount ?? 0,
                    'monitors_limit' => optional($user->subscription)->monitors_limit ?? 1,
                    'check_interval' => optional($user->subscription)->check_interval ?? 5,
                ];
            });

        return Inertia::render('users/list', [
            'users' => $users,
        ]);
    }

    public function upgradePlan(Request $request, User $user)
    {
        $validated = $request->validate([
            'plan_name'       => 'required|string',
            'amount'          => 'required|numeric|min:0',
            'monitors_limit'  => 'required|integer|min:1',
            'check_interval'  => 'required|integer|min:1',
        ]);

        Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'plan_name'      => $validated['plan_name'],
                'amount'         => $validated['amount'],
                'monitors_limit' => $validated['monitors_limit'],
                'check_interval' => $validated['check_interval'],
                'starts_at'      => now(),
                'ends_at'        => now()->addMonth(),
            ]
        );

        return back()->with('success', 'User plan updated successfully.');
    }

    public function downgradePlan(User $user)
    {
        $user->subscription()?->delete();
        return back()->with('success', 'User plan removed/downgraded successfully.');
    }
}
