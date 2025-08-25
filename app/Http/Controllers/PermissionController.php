<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

/**
 * @OA\Tag(
 *     name="Permission Management",
 *     description="API Endpoints for listing permissions"
 * )
 */
class PermissionController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/permissions",
     *      operationId="getPermissionsList",
     *      tags={"Permission Management"},
     *      summary="Get list of permissions",
     *      description="Returns list of all available permissions",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(type="object"))
     *       ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }
}
