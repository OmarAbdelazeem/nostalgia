# Products & Categories API Specifications

This document outlines the API endpoints, data structures, and functionalities required for managing Products and Categories in the application. This API should be built using Laravel 11 and integrated with the existing authentication system.

##  baseURL

All endpoints should be prefixed with the API base URL.
`http://127.0.0.1:8000/api`

## Authentication

All endpoints listed below must be protected and require a valid Bearer Token in the `Authorization` header, consistent with the existing `login` and `register` endpoints.

---

## 📦 Products API

### 1.1 Product Data Model

The `Product` object should have the following structure. Note the relationships with other models like `Category`, `ProductImage`, etc.

```json
{
  "id": 1,
  "name": "string",
  "description": "string",
  "product_number": "string",
  "image_url": "string",
  "price": 150.50,
  "discount": 10,
  "final_price": 135.45,
  "manufacturing_material": "string",
  "manufacturing_country": "string",
  "stock_quantity": 100,
  "is_available": true,
  "category_id": 1,
  "sub_category_id": 2,
  "category": { ... }, // Optional: Include full Category object
  "sub_category": { ... }, // Optional: Include full SubCategory object
  "product_images": [
    {
      "id": 1,
      "image_url": "string",
      "alt_text": "string"
    }
  ],
  // Include other relationships as needed based on the existing Angular interface
  "created_at": "2024-08-23T10:00:00.000000Z",
  "updated_at": "2024-08-23T10:00:00.000000Z"
}
```

### 1.2 Endpoints

#### `GET /products`
- **Description:** Retrieve a paginated list of all products.
- **Query Parameters:**
  - `page` (integer, optional, default: 1): The page number for pagination.
  - `per_page` (integer, optional, default: 10): The number of records per page.
  - `search` (string, optional): A search term to filter products by name or description.
  - `category_id` (integer, optional): Filter products by a specific category ID.
  - `sub_category_id` (integer, optional): Filter products by a specific sub-category ID.
  - `is_available` (boolean, optional): Filter products by availability status.
- **Success Response (200 OK):**
  ```json
  {
    "data": [
      // array of Product objects
    ],
    "links": {
      "first": "url",
      "last": "url",
      "prev": "url",
      "next": "url"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 10,
      "path": "url",
      "per_page": 10,
      "to": 10,
      "total": 100
    }
  }
  ```

#### `POST /products`
- **Description:** Create a new product.
- **Request Body:** A `Product` object (without `id`, `created_at`, `updated_at`).
- **Success Response (201 Created):** The newly created `Product` object.

#### `GET /products/{id}`
- **Description:** Retrieve a single product by its ID.
- **Success Response (200 OK):** The requested `Product` object.

#### `PUT /products/{id}`
- **Description:** Update an existing product.
- **Request Body:** A `Product` object with the fields to be updated.
- **Success Response (200 OK):** The updated `Product` object.

#### `DELETE /products/{id}`
- **Description:** Delete a product.
- **Success Response (204 No Content):** An empty response.

#### `POST /products/{id}/images`
- **Description:** Upload one or more images for a product.
- **Request Body:** `multipart/form-data` with an `images` field containing an array of image files.
- **Success Response (200 OK):** The `Product` object with the updated `product_images` array.

---

## 📂 Categories API

### 2.1 Category Data Model
The `Category` object should have the following structure. Categories can be nested (have a `parent_id`).

```json
{
  "id": 1,
  "name": "string",
  "description": "string",
  "image_url": "string",
  "parent_id": null, // or integer for sub-categories
  "children": [
      // array of child Category objects
  ],
  "created_at": "2024-08-23T10:00:00.000000Z",
  "updated_at": "2024-08-23T10:00:00.000000Z"
}
```

### 2.2 Endpoints

#### `GET /categories`
- **Description:** Retrieve a list of all categories. The response should be a nested tree structure, where top-level categories contain their sub-categories in a `children` array.
- **Success Response (200 OK):**
  ```json
  {
    "data": [
      // array of parent Category objects with their children nested inside
    ]
  }
  ```

#### `POST /categories`
- **Description:** Create a new category.
- **Request Body:** A `Category` object (without `id`, `children`, `created_at`, `updated_at`). `parent_id` can be included to create a sub-category.
- **Success Response (201 Created):** The newly created `Category` object.

#### `GET /categories/{id}`
- **Description:** Retrieve a single category by its ID, including its children.
- **Success Response (200 OK):** The requested `Category` object.

#### `PUT /categories/{id}`
- **Description:** Update an existing category.
- **Request Body:** A `Category` object with the fields to be updated.
- **Success Response (200 OK):** The updated `Category` object.

#### `DELETE /categories/{id}`
- **Description:** Delete a category. If the category has sub-categories, the backend should handle this gracefully (e.g., prevent deletion or re-assign children).
- **Success Response (204 No Content):** An empty response. 