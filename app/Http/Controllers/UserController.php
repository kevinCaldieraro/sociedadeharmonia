<?php

namespace App\Http\Controllers;

use App\Mail\UserCreated;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Mail;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render(
            'ManageUsers',
            [
                'users' => User::all()
            ]
        );
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        Mail::to($user->email)->queue(new UserCreated($user));

        return response()->json([
            'message' => 'Usuário cadastrado com sucesso.',
            'newUser' => $user
        ], 201);
    }

    public function destroy(User $user): JsonResponse
    {
        $id = $user->id;
        $name = $user->name;
        $user->delete();

        return response()->json([
            'message' => "Usuário $name excluído.",
            'id' => $id
        ], 200);
    }

    public function update()
    {

    }
}
