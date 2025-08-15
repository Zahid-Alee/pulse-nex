<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{

    protected function sendWelcomeMail(User $user)
    {
        try {
            // The line that sends the email
            Mail::to($user->email)->send(new WelcomeEmail($user));

        } catch (\Exception $e) {
            dd($e->getMessage());
            // If it fails, log the error message
            Log::error("Failed to send welcome email to {$user->email}. Error: " . $e->getMessage());
        }
    }
    /**
     * Show the registration page (public).
     */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }

    /**
     * Handle an incoming registration request (public).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan_name' => 'Free',
            'monitors_limit' => 1,
            'check_interval' => 5,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        event(new Registered($user));

        $this->sendWelcomeMail($user);

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    // -------- Admin CRUD Methods Below --------

    /**
     * Display a listing of users (Admin dashboard).
     */
    public function index(): Response
    {
        $users = User::paginate(15);

        return Inertia::render('admin/users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user (Admin).
     */
    public function createUser(): Response
    {
        return Inertia::render('admin/users/Create');
    }

    /**
     * Store a newly created user (Admin).
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_admin' => 'nullable|boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->input('is_admin', false),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user (Admin).
     */
    public function editUser(User $user): Response
    {
        return Inertia::render('admin/users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user in storage (Admin).
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'is_admin' => 'nullable|boolean',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_admin = $request->input('is_admin', false);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage (Admin).
     */
    public function destroyUser(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
