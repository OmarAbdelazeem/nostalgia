<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Category Management",
 *     description="API Endpoints for managing categories"
 * )
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/categories",
     *      tags={"Category Management"},
     *      summary="List all categories",
     *      description="Returns a list of all categories",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Electronics"),
     *                      @OA\Property(property="description", type="string", example="Electronic devices and gadgets", nullable=true),
     *                      @OA\Property(property="image_url", type="string", example="/storage/category_images/electronics.jpg", nullable=true),
     *                      @OA\Property(property="created_at", type="string", format="date-time"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json(['data' => $categories]);
    }

    /**
     * @OA\Post(
     *      path="/api/categories",
     *      tags={"Category Management"},
     *      summary="Create a new category",
     *      description="Creates a new category with optional image upload",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="string", example="Electronics", description="Category name"),
     *                  @OA\Property(property="description", type="string", example="Electronic devices and gadgets", description="Category description"),
     *                  @OA\Property(property="image", type="string", format="binary", description="Category image (optional)")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Category created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Electronics"),
     *              @OA\Property(property="description", type="string", example="Electronic devices and gadgets", nullable=true),
     *              @OA\Property(property="image_url", type="string", example="/storage/category_images/electronics.jpg", nullable=true),
     *              @OA\Property(property="created_at", type="string", format="date-time"),
     *              @OA\Property(property="updated_at", type="string", format="date-time")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                  @OA\Property(property="image", type="array", @OA\Items(type="string", example="The image must be an image."))
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $categoryData = [
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
        ];

        $category = new Category($categoryData);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('category_images', 'public');
            $category->image_url = Storage::url($path);
        }

        $category->save();

        return response()->json($category, 201);
    }

    /**
     * @OA\Get(
     *      path="/api/categories/{id}",
     *      tags={"Category Management"},
     *      summary="Get a single category",
     *      description="Returns detailed information about a specific category",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Category ID",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Electronics"),
     *              @OA\Property(property="description", type="string", example="Electronic devices and gadgets", nullable=true),
     *              @OA\Property(property="image_url", type="string", example="/storage/category_images/electronics.jpg", nullable=true),
     *              @OA\Property(property="created_at", type="string", format="date-time"),
     *              @OA\Property(property="updated_at", type="string", format="date-time")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Category not found"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * @OA\Put(
     *      path="/api/categories/{id}",
     *      tags={"Category Management"},
     *      summary="Update a category",
     *      description="Updates an existing category with new information. Use application/json for best compatibility.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Category ID",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="string", example="Updated Electronics", description="Category name"),
     *                  @OA\Property(property="description", type="string", example="Updated electronic devices description", description="Category description")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Category updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Updated Electronics"),
     *              @OA\Property(property="description", type="string", example="Updated electronic devices description", nullable=true),
     *              @OA\Property(property="image_url", type="string", example="/storage/category_images/updated_electronics.jpg", nullable=true),
     *              @OA\Property(property="created_at", type="string", format="date-time"),
     *              @OA\Property(property="updated_at", type="string", format="date-time")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required."))
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Category not found"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function update(Request $request, Category $category)
    {
        // Handle different content types
        if ($request->isJson()) {
            // JSON request - use validated data
            $validatedData = $request->validate([
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'description' => 'nullable|string'
            ]);
        } else {
            // Multipart/form-data request - validate and process differently
            $validatedData = $request->validate([
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
        }

        // Process the data based on content type
        $categoryData = [];
        
        if ($request->isJson()) {
            // JSON request - use validated data
            if (isset($validatedData['name'])) {
                $categoryData['name'] = $validatedData['name'];
            }
            if (isset($validatedData['description'])) {
                $categoryData['description'] = $validatedData['description'];
            }
        } else {
            // Multipart/form-data request - check request directly
            if ($request->has('name') && $request->input('name') !== '') {
                $categoryData['name'] = $request->input('name');
            }
            if ($request->has('description')) {
                $categoryData['description'] = $request->input('description');
            }
        }

        $category->fill($categoryData);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($category->image_url) {
                Storage::delete(str_replace('/storage', 'public', $category->image_url));
            }
            $path = $request->file('image')->store('category_images', 'public');
            $category->image_url = Storage::url($path);
        }

        $category->save();

        return response()->json($category);
    }

    /**
     * @OA\Post(
     *      path="/api/categories/{id}/update",
     *      tags={"Category Management"},
     *      summary="Update a category with multipart/form-data",
     *      description="Updates an existing category with new information and optional image upload. Use this endpoint when you need to upload images.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Category ID",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="string", example="Updated Electronics", description="Category name"),
     *                  @OA\Property(property="description", type="string", example="Updated electronic devices description", description="Category description"),
     *                  @OA\Property(property="image", type="string", format="binary", description="New category image (optional)")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Category updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Updated Electronics"),
     *              @OA\Property(property="description", type="string", example="Updated electronic devices description", nullable=true),
     *              @OA\Property(property="image_url", type="string", example="/storage/category_images/updated_electronics.jpg", nullable=true),
     *              @OA\Property(property="created_at", type="string", format="date-time"),
     *              @OA\Property(property="updated_at", type="string", format="date-time")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required."))
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Category not found"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function updateWithFormData(Request $request, $id)
    {
        // Find the category manually since route model binding might not work with POST
        $category = Category::findOrFail($id);
        
        $validatedData = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Process the data
        $categoryData = [];
        
        if ($request->has('name') && $request->input('name') !== '') {
            $categoryData['name'] = $request->input('name');
        }
        if ($request->has('description')) {
            $categoryData['description'] = $request->input('description');
        }

        $category->fill($categoryData);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($category->image_url) {
                Storage::delete(str_replace('/storage', 'public', $category->image_url));
            }
            $path = $request->file('image')->store('category_images', 'public');
            $category->image_url = Storage::url($path);
        }

        $category->save();

        return response()->json($category);
    }

    /**
     * @OA\Delete(
     *      path="/api/categories/{id}",
     *      tags={"Category Management"},
     *      summary="Delete a category",
     *      description="Deletes a category",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Category ID",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Category deleted successfully"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Category not found"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function destroy(Category $category)
    {
        if ($category->image_url) {
            Storage::delete(str_replace('/storage', 'public', $category->image_url));
        }

        $category->delete();

        return response()->json(null, 204);
    }
}
