<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Product Management",
 *     description="API Endpoints for managing products"
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/products",
     *      tags={"Product Management"},
     *      summary="List all products",
     *      description="Returns a paginated list of products with optional filtering and search capabilities",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="category_id",
     *          in="query",
     *          description="Filter by category ID",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          description="Search in product name, description, and product number",
     *          @OA\Schema(type="string", example="vintage camera")
     *      ),
     *      @OA\Parameter(
     *          name="min_price",
     *          in="query",
     *          description="Minimum price filter",
     *          @OA\Schema(type="number", format="float", example=100.00)
     *      ),
     *      @OA\Parameter(
     *          name="max_price",
     *          in="query",
     *          description="Maximum price filter",
     *          @OA\Schema(type="number", format="float", example=500.00)
     *      ),
     *      @OA\Parameter(
     *          name="available",
     *          in="query",
     *          description="Filter by availability",
     *          @OA\Schema(type="boolean", example=true)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Vintage Camera"),
     *                      @OA\Property(property="description", type="string", example="Beautiful vintage camera from the 1950s"),
     *                      @OA\Property(property="product_number", type="string", example="VC-001"),
     *                      @OA\Property(property="image_url", type="string", example="/storage/products/vintage_camera.jpg", nullable=true),
     *                      @OA\Property(property="price", type="number", format="float", example=299.99),
     *                      @OA\Property(property="discount", type="number", format="float", example=10.00, nullable=true),
     *                      @OA\Property(property="final_price", type="number", format="float", example=269.99),
     *                      @OA\Property(property="manufacturing_material", type="string", example="Metal and Leather", nullable=true),
     *                      @OA\Property(property="manufacturing_country", type="string", example="Germany", nullable=true),
     *                      @OA\Property(property="stock_quantity", type="integer", example=5),
     *                      @OA\Property(property="is_available", type="boolean", example=true),
     *                      @OA\Property(property="category_id", type="integer", example=1),
     *                      @OA\Property(property="created_at", type="string", format="date-time"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time"),
     *                      @OA\Property(property="category", type="object",
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="name", type="string", example="Electronics")
     *                      ),
     *                      @OA\Property(property="product_images", type="array",
     *                          @OA\Items(
     *                              @OA\Property(property="id", type="integer", example=1),
     *                              @OA\Property(property="image_url", type="string", example="/storage/products/vintage_camera_1.jpg"),
     *                              @OA\Property(property="alt_text", type="string", example="Vintage Camera Front View")
     *                          )
     *                      )
     *                  )
     *              ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="first", type="string", example="http://localhost:8000/api/products?page=1"),
     *                  @OA\Property(property="last", type="string", example="http://localhost:8000/api/products?page=3"),
     *                  @OA\Property(property="prev", type="string", nullable=true),
     *                  @OA\Property(property="next", type="string", example="http://localhost:8000/api/products?page=2")
     *              ),
     *              @OA\Property(property="meta", type="object",
     *                  @OA\Property(property="current_page", type="integer", example=1),
     *                  @OA\Property(property="from", type="integer", example=1),
     *                  @OA\Property(property="last_page", type="integer", example=3),
     *                  @OA\Property(property="per_page", type="integer", example=20),
     *                  @OA\Property(property="to", type="integer", example=20),
     *                  @OA\Property(property="total", type="integer", example=58)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'productImages']);

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('product_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        $products = $query->paginate(20);

        return response()->json($products);
    }

    /**
     * @OA\Post(
     *      path="/api/products",
     *      tags={"Product Management"},
     *      summary="Create a new product",
     *      description="Creates a new product with JSON data. Use separate endpoint for image uploads.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="string", example="Vintage Camera"),
     *                  @OA\Property(property="description", type="string", example="Beautiful vintage camera from the 1950s"),
     *                  @OA\Property(property="product_number", type="string", example="VC-001"),
     *                  @OA\Property(property="price", type="number", example=299.99),
     *                  @OA\Property(property="discount", type="number", example=10.00),
     *                  @OA\Property(property="manufacturing_material", type="string", example="Metal and Leather"),
     *                  @OA\Property(property="manufacturing_country", type="string", example="Germany"),
     *                  @OA\Property(property="stock_quantity", type="integer", example=5),
     *                  @OA\Property(property="is_available", type="boolean", example=true),
     *                  @OA\Property(property="category_id", type="integer", example=1)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Product created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Vintage Camera"),
     *              @OA\Property(property="description", type="string", example="Beautiful vintage camera from the 1950s"),
     *              @OA\Property(property="product_number", type="string", example="VC-001"),
     *              @OA\Property(property="price", type="number", example=299.99),
     *              @OA\Property(property="discount", type="number", example=10.00),
     *              @OA\Property(property="manufacturing_material", type="string", example="Metal and Leather"),
     *              @OA\Property(property="manufacturing_country", type="string", example="Germany"),
     *              @OA\Property(property="stock_quantity", type="integer", example=5),
     *              @OA\Property(property="is_available", type="boolean", example=true),
     *              @OA\Property(property="category_id", type="integer", example=1),
     *              @OA\Property(property="image_url", type="string", nullable=true),
     *              @OA\Property(property="final_price", type="number", example=269.99),
     *              @OA\Property(property="created_at", type="string", format="date-time"),
     *              @OA\Property(property="updated_at", type="string", format="date-time"),
     *              @OA\Property(property="category", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Electronics")
     *              ),
     *              @OA\Property(property="product_images", type="array", @OA\Items())
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
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'product_number' => 'required|string|max:255|unique:products',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'manufacturing_material' => 'nullable|string|max:255',
            'manufacturing_country' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'is_available' => 'nullable|in:true,false,1,0,on,off',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Create product data
        $productData = [
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'product_number' => $validatedData['product_number'],
            'price' => $validatedData['price'],
            'discount' => $validatedData['discount'] ?? 0,
            'manufacturing_material' => $validatedData['manufacturing_material'] ?? null,
            'manufacturing_country' => $validatedData['manufacturing_country'] ?? null,
            'stock_quantity' => $validatedData['stock_quantity'],
            'is_available' => isset($validatedData['is_available']) ? filter_var($validatedData['is_available'], FILTER_VALIDATE_BOOLEAN) : true,
            'category_id' => $validatedData['category_id'],
        ];

        $product = Product::create($productData);

        return response()->json($product->load(['category', 'productImages']), 201);
    }

    /**
     * @OA\Get(path="/api/products/{id}", tags={"Product Management"}, summary="Get a single product", security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function show(Product $product)
    {
        return response()->json($product->load(['category', 'productImages']));
    }

    /**
     * @OA\Put(path="/api/products/{id}", tags={"Product Management"}, summary="Update a product", security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(
     *          @OA\Property(property="name", type="string"),
     *          @OA\Property(property="description", type="string"),
     *          @OA\Property(property="product_number", type="string"),
     *          @OA\Property(property="price", type="number"),
     *          @OA\Property(property="discount", type="number"),
     *          @OA\Property(property="manufacturing_material", type="string"),
     *          @OA\Property(property="manufacturing_country", type="string"),
     *          @OA\Property(property="stock_quantity", type="integer"),
     *          @OA\Property(property="is_available", type="boolean"),
     *          @OA\Property(property="category_id", type="integer"),
     *          @OA\Property(property="image", type="string", format="binary"),
     *          @OA\Property(property="images[]", type="array", @OA\Items(type="string", format="binary"))
     *      ))),
     *      @OA\Response(response=200, description="Product updated successfully"),
     * )
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'product_number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'price' => 'sometimes|required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'manufacturing_material' => 'nullable|string|max:255',
            'manufacturing_country' => 'nullable|string|max:255',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'is_available' => 'sometimes|in:true,false,1,0,on,off',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Only fill fields that exist in the database table
        $productData = [];
        if (isset($validatedData['name'])) {
            $productData['name'] = $validatedData['name'];
        }
        if (isset($validatedData['description'])) {
            $productData['description'] = $validatedData['description'];
        }
        if (isset($validatedData['product_number'])) {
            $productData['product_number'] = $validatedData['product_number'];
        }
        if (isset($validatedData['price'])) {
            $productData['price'] = $validatedData['price'];
        }
        if (isset($validatedData['discount'])) {
            $productData['discount'] = $validatedData['discount'];
        }
        if (isset($validatedData['manufacturing_material'])) {
            $productData['manufacturing_material'] = $validatedData['manufacturing_material'];
        }
        if (isset($validatedData['manufacturing_country'])) {
            $productData['manufacturing_country'] = $validatedData['manufacturing_country'];
        }
        if (isset($validatedData['stock_quantity'])) {
            $productData['stock_quantity'] = $validatedData['stock_quantity'];
        }
        if (isset($validatedData['is_available'])) {
            $productData['is_available'] = $validatedData['is_available'];
        }
        if (isset($validatedData['category_id'])) {
            $productData['category_id'] = $validatedData['category_id'];
        }

        $product->fill($productData);

        // Handle main product image update
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image_url) {
                Storage::delete(str_replace('/storage', 'public', $product->image_url));
            }
            $path = $request->file('image')->store('product_images', 'public');
            $product->image_url = Storage::url($path);
        }

        $product->save();

        // Handle additional product images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public');
                $product->productImages()->create([
                    'image_url' => Storage::url($path),
                    'alt_text' => $product->name
                ]);
            }
        }

        return response()->json($product->load(['category', 'subCategory', 'productImages']));
    }

    /**
     * @OA\Delete(path="/api/products/{id}", tags={"Product Management"}, summary="Delete a product", security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=204, description="Product deleted successfully"),
     * )
     */
    public function destroy(Product $product)
    {
        // Delete main product image
        if ($product->image_url) {
            Storage::delete(str_replace('/storage', 'public', $product->image_url));
        }

        // Delete all product images
        foreach ($product->productImages as $image) {
            Storage::delete(str_replace('/storage', 'public', $image->image_url));
        }

        $product->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *      path="/api/products/{id}/upload-image",
     *      tags={"Product Management"},
     *      summary="Upload images for a product",
     *      description="Upload main image and additional images for an existing product",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="image", type="string", format="binary", description="Main product image (optional)"),
     *                  @OA\Property(property="images[]", type="array", @OA\Items(type="string", format="binary"), description="Additional product images (optional)")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Images uploaded successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Images uploaded successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="image_url", type="string", example="/storage/product_images/abc123.jpg", nullable=true),
     *                  @OA\Property(property="product_images", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="image_url", type="string", example="/storage/product_images/additional1.jpg"),
     *                          @OA\Property(property="alt_text", type="string", example="Product Name")
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="image", type="array", @OA\Items(type="string", example="The image field must be an image."))
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function uploadImage(Request $request, Product $product)
    {
        // Log request details for debugging
        \Log::info('Image Upload Request Debug', [
            'product_id' => $product->id,
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
            'content_type' => $request->header('Content-Type'),
            'user_agent' => $request->header('User-Agent'),
            'authorization' => $request->header('Authorization') ? 'Bearer token present' : 'No authorization',
            'all_headers' => $request->headers->all(),
            'request_all_data' => $request->all(),
            'request_files' => $request->allFiles(),
            'has_file_image' => $request->hasFile('image'),
            'has_file_images' => $request->hasFile('images'),
            'file_image_details' => $request->hasFile('image') ? [
                'name' => $request->file('image')->getClientOriginalName(),
                'size' => $request->file('image')->getSize(),
                'mime_type' => $request->file('image')->getMimeType(),
                'extension' => $request->file('image')->getClientOriginalExtension(),
            ] : null,
            'file_images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'file_images_details' => $request->hasFile('images') ? array_map(function($file) {
                return [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                ];
            }, $request->file('images')) : [],
        ]);

        try {
            $validatedData = $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Log validation results
            \Log::info('Image Upload Validation Results', [
                'validated_data' => $validatedData,
                'validation_passed' => true,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            \Log::error('Image Upload Validation Failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'request_files' => $request->allFiles(),
            ]);
            
            throw $e; // Re-throw the validation exception
        }

        $uploadedImages = [
            'image_url' => null,
            'product_images' => []
        ];

        // Handle main product image
        if ($request->hasFile('image')) {
            \Log::info('Processing main image upload', [
                'file_name' => $request->file('image')->getClientOriginalName(),
                'file_size' => $request->file('image')->getSize(),
            ]);

            // Delete old main image if it exists
            if ($product->image_url) {
                Storage::delete(str_replace('/storage', 'public', $product->image_url));
                \Log::info('Deleted old main image', ['old_url' => $product->image_url]);
            }
            
            $path = $request->file('image')->store('product_images', 'public');
            $product->image_url = Storage::url($path);
            $product->save();
            
            $uploadedImages['image_url'] = $product->image_url;
            
            \Log::info('Main image uploaded successfully', [
                'storage_path' => $path,
                'public_url' => $product->image_url,
            ]);
        } else {
            \Log::info('No main image provided in request');
        }

        // Handle additional product images
        if ($request->hasFile('images')) {
            \Log::info('Processing additional images upload', [
                'count' => count($request->file('images')),
            ]);

            foreach ($request->file('images') as $index => $image) {
                \Log::info('Processing additional image', [
                    'index' => $index,
                    'file_name' => $image->getClientOriginalName(),
                    'file_size' => $image->getSize(),
                ]);

                $path = $image->store('product_images', 'public');
                $productImage = $product->productImages()->create([
                    'image_url' => Storage::url($path),
                    'alt_text' => $product->name
                ]);
                
                $uploadedImages['product_images'][] = [
                    'id' => $productImage->id,
                    'image_url' => $productImage->image_url,
                    'alt_text' => $productImage->alt_text
                ];
                
                \Log::info('Additional image uploaded successfully', [
                    'index' => $index,
                    'storage_path' => $path,
                    'public_url' => $productImage->image_url,
                    'product_image_id' => $productImage->id,
                ]);
            }
        } else {
            \Log::info('No additional images provided in request');
        }

        // Log final response
        \Log::info('Image upload completed successfully', [
            'final_response' => [
                'success' => true,
                'message' => 'Images uploaded successfully',
                'data' => $uploadedImages
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully',
            'data' => $uploadedImages
        ]);
    }
} 