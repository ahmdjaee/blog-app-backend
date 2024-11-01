<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function list(Request $request): ResourceCollection
    {
        $limit = $request->query('limit', 5);
        $page = $request->query('page', 1);
        $keyword = $request->query('keyword', '');

        $query = User::query();

        $query->where("name", "like", "%{$keyword}%");

        $users = $query->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page)
            ->onEachSide(0)
            ->withQueryString();

        return $this->paginationResponse(UserResource::collection($users), 'User get successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        return $this->successResponse(new UserResource($user), 'User created successfully');
    }


    public function update(Request $request, int $id): JsonResponse
    {

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'old_password' => ['nullable', 'string', 'min:8'],
            'new_password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $user = User::find($id);

        if (isset($data['old_password']) && isset($data['new_password'])) {
            if (!Hash::check($data['old_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'old_password' => ['The old password is incorrect.'],
                ]);
            }

            $data['password'] = bcrypt($data['new_password']);
        }


        $user->update($data);
        return $this->successResponse(new UserResource($user), 'User updated successfully');
    }
    public function destroy(int $id)
    {
        if (auth()->user()->id == $id) {
            return $this->errorResponse('You can not delete yourself', 403);
        }

        User::destroy($id);
        return $this->successResponse(true, 'User deleted successfully');
    }
}
