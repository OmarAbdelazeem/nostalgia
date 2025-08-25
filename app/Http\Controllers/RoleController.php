<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Role Management",
 *     description="API Endpoints for managing roles"
 * )
 */
class RoleController extends Controller
{
    /**
     * @OA\Get(path="/api/roles", tags={"Role Management"}, summary="List all roles", security={{"sanctum":{}}}, @OA\Response(response=200, description="Successful operation"))
     */
    public function index()
    {
// ... existing code ...
    }

    /**
     * @OA\Post(path="/api/roles", tags={"Role Management"}, summary="Create a new role", security={{"sanctum":{}}},
     *      @OA\RequestBody(required=true, @OA\JsonContent(
     *          @OA\Property(property="name", type="string", example="New Role"),
     *          @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="view users"))
     *      )),
     *      @OA\Response(response=201, description="Role created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
// ... existing code ...
    }

    /**
     * @OA\Get(path="/api/roles/{id}", tags={"Role Management"}, summary="Get a single role", security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Role not found")
     * )
     */
    public function show(Role $role)
    {
// ... existing code ...
    }

    /**
     * @OA\Put(path="/api/roles/{id}", tags={"Role Management"}, summary="Update a role", security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(required=true, @OA\JsonContent(
     *          @OA\Property(property="name", type="string", example="Updated Role Name"),
     *          @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="edit users"))
     *      )),
     *      @OA\Response(response=200, description="Role updated successfully"),
     *      @OA\Response(response=404, description="Role not found")
     * )
     */
    public function update(Request $request, Role $role)
    {
// ... existing code ...
    }

    /**
     * @OA\Delete(path="/api/roles/{id}", tags={"Role Management"}, summary="Delete a role", security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Role deleted successfully"),
     *      @OA\Response(response=403, description="Cannot delete a default system role"),
     *      @OA\Response(response=404, description="Role not found")
     * )
     */
    public function destroy(Role $role)
    {
// ... existing code ...
    }
} 