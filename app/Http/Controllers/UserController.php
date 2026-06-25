<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const ROLE_OPTIONS = ['super_admin', 'operator', 'viewer'];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::in(self::ROLE_OPTIONS)],
            'sort' => ['nullable', Rule::in(['name_asc', 'name_desc', 'email_asc', 'latest', 'oldest'])],
        ]);

        $search = $filters['search'] ?? null;
        $role = $filters['role'] ?? null;
        $sort = $filters['sort'] ?? 'name_asc';
        $searchLike = $search ? '%'.$this->escapeLike($search).'%' : null;

        $users = User::query()
            ->when($searchLike, function ($query, string $searchLike) {
                $query->where(function ($query) use ($searchLike) {
                    $query->where('name', 'like', $searchLike)
                        ->orWhere('email', 'like', $searchLike);
                });
            })
            ->when($role, function ($query, string $role) {
                $query->where('role', $role);
            });

        match ($sort) {
            'name_desc' => $users->orderByDesc('name'),
            'email_asc' => $users->orderBy('email'),
            'latest' => $users->latest(),
            'oldest' => $users->oldest(),
            default => $users->orderBy('name'),
        };

        $users = $users->paginate(10)->withQueryString();

        return view('users.index', [
            'users' => $users,
            'roles' => self::ROLE_OPTIONS,
            'filters' => [
                'search' => $search ?? '',
                'role' => $role ?? '',
                'sort' => $sort,
            ],
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => self::ROLE_OPTIONS,
        ]);
    }

    public function store(Request $request)
    {
        $user = User::create($this->validateUser($request));

        ActivityLogger::log(
            $request,
            'create',
            'user',
            'Menambahkan user '.$user->name.'.',
            $user,
            null,
            $user->toArray()
        );

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => self::ROLE_OPTIONS,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validateUser($request, $user);
        $oldValues = $user->toArray();
        $isPasswordReset = filled($data['password'] ?? null);

        if (! $isPasswordReset) {
            unset($data['password']);
        }

        $user->update($data);
        $user->refresh();

        ActivityLogger::log(
            $request,
            'update',
            'user',
            'Memperbarui user '.$user->name.'.',
            $user,
            $oldValues,
            $user->toArray()
        );

        if ($isPasswordReset) {
            ActivityLogger::log(
                $request,
                'reset_password',
                'user',
                'Mereset password user '.$user->name.'.',
                $user
            );
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return redirect()
                ->route('users.index')
                ->withErrors(['user' => 'Super admin tidak boleh menghapus akun dirinya sendiri.']);
        }

        $oldValues = $user->toArray();
        $description = 'Menghapus user '.$user->name.'.';

        $user->delete();

        ActivityLogger::log(
            $request,
            'delete',
            'user',
            $description,
            $user,
            $oldValues,
            null
        );

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'password' => [
                $user ? 'nullable' : 'required',
                'string',
                'max:255',
                Password::min(12)->letters()->mixedCase()->numbers()->symbols(),
            ],
            'role' => ['required', Rule::in(self::ROLE_OPTIONS)],
        ]);
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\%_');
    }
}
