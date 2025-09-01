<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Product Image Management",
 *     description="API Endpoints for managing product images"
 * )
 */
class ProductImageController extends Controller
{
    /**
     * @OA\Get(path="/api/products/{product}/images", tags={"Product Image Management"}, summary="Get all images for a product", security={{"sanctum":{}}},
     *      @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"))
     */
    public function index(Product $product)
    {
        $images = $product->productImages;
        return response()->json(['data' => $images]);
    }

    /**
     * @OA\Post(path="/api/products/{product}/images", tags={"Product Image Management"}, summary="Add images to a product", security={{"sanctum":{}}},
     *      @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(
     *          @OA\Property(property="images[]", type="array", @OA\Items(type="string", format="binary"), description="Product images"),
     *          @OA\Property(property="alt_text", type="string", description="Alt text for all images")
     *      ))),
     *      @OA\Response(response=201, description="Images added successfully"))
     */
    public function store(Request $request, Product $product)
    {
        // Check if images are provided
        if (!$request->hasFile('images') || empty($request->file('images'))) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'images' => ['The images field is required.']
                ]
            ], 422);
        }

        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alt_text' => 'nullable|string|max:255'
        ]);

        $altText = $request->alt_text ?? $product->name;
        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('product_images', 'public');
            $productImage = $product->productImages()->create([
                'image_url' => Storage::url($path),
                'alt_text' => $altText
            ]);
            $uploadedImages[] = $productImage;
        }

        return response()->json(['data' => $uploadedImages], 201);
    }

    /**
     * @OA\Get(path="/api/products/{product}/images/{image}", tags={"Product Image Management"}, summary="Get a specific product image", security={{"sanctum":{}}},
     *      @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="image", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"))
     */
    public function show(Product $product, ProductImage $image)
    {
        // Ensure the image belongs to the specified product
        if ($image->product_id !== $product->id) {
            return response()->json(['message' => 'Image not found for this product'], 404);
        }

        return response()->json($image);
    }

    /**
     * @OA\Put(path="/api/products/{product}/images/{image}", tags={"Product Image Management"}, summary="Update a product image", security={{"sanctum":{}}},
     *      @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="image", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(
     *          @OA\Property(property="image", type="string", format="binary", description="New image file"),
     *          @OA\Property(property="alt_text", type="string", description="Alt text for the image")
     *      ))),
     *      @OA\Response(response=200, description="Image updated successfully"))
     */
    public function update(Request $request, Product $product, ProductImage $image)
    {
        // Ensure the image belongs to the specified product
        if ($image->product_id !== $product->id) {
            return response()->json(['message' => 'Image not found for this product'], 404);
        }

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alt_text' => 'nullable|string|max:255'
        ]);

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image
            Storage::delete(str_replace('/storage', 'public', $image->image_url));
            
            // Store new image
            $path = $request->file('image')->store('product_images', 'public');
            $image->image_url = Storage::url($path);
        }

        // Update alt text if provided
        if ($request->filled('alt_text')) {
            $image->alt_text = $request->alt_text;
        }

        $image->save();

        return response()->json($image);
    }

    /**
     * @OA\Delete(path="/api/products/{product}/images/{image}", tags={"Product Image Management"}, summary="Delete a product image", security={{"sanctum":{}}},
     *      @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="image", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=204, description="Image deleted successfully"))
     */
    public function destroy(Product $product, ProductImage $image)
    {
        // Ensure the image belongs to the specified product
        if ($image->product_id !== $product->id) {
            return response()->json(['message' => 'Image not found for this product'], 404);
        }

        // Delete the image file
        Storage::delete(str_replace('/storage', 'public', $image->image_url));
        
        // Delete the database record
        $image->delete();

        return response()->json(null, 204);
    }
} 