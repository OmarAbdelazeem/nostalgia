<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="User Management",
 *     description="API Endpoints for managing users"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/users",
     *      operationId="getUsersList",
     *      tags={"User Management"},
     *      summary="Get list of users",
     *      description="Returns list of users",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(type="object"))
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json($users);
    }

    /**
     * @OA\Get(
     *      path="/api/users/{id}",
     *      operationId="getUserById",
     *      tags={"User Management"},
     *      summary="Get user information",
     *      description="Returns user data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="object")
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function show(User $user)
    {
        $user->load('roles');
        return response()->json($user);
    }

    /**
     * @OA\Put(
     *      path="/api/users/{id}",
     *      operationId="updateUser",
     *      tags={"User Management"},
     *      summary="Update existing user",
     *      description="Returns updated user data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Updated Name"),
     *              @OA\Property(property="email", type="string", format="email", example="updated@example.com"),
     *              @OA\Property(property="roles", type="array", @OA\Items(type="string", example="User"))
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="object")
     *       )
     * )
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'sometimes|array'
        ]);

        if ($request->has('name')) {
            $user->name = $validatedData['name'];
        }

        if ($request->has('email')) {
            $user->email = $validatedData['email'];
        }

        $user->save();

        if ($request->has('roles')) {
            $user->syncRoles($validatedData['roles']);
        }

        return response()->json($user->load('roles'));
    }

    /**
     * @OA\Delete(
     *      path="/api/users/{id}",
     *      operationId="deleteUser",
     *      tags={"User Management"},
     *      summary="Delete existing user",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function destroy(User $user)
    {
        // Prevent users from deleting themselves
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User successfully deleted.']);
    }
}
