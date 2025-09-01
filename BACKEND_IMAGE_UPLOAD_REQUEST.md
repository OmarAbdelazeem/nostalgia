# Backend Team Request: Product Image Upload Implementation

## 📋 **Issue Summary**

**Date**: September 1, 2025  
**Priority**: High  
**Status**: Pending Backend Clarification  

### **Current Situation**
- ✅ **Product creation works** with JSON data (no images)
- ❌ **Image upload fails** when using FormData approach
- ✅ **Backend supports image upload** (confirmed via Swagger)

---

## 🚨 **Problem Description**

### **Error Details**
When attempting to create products with images using `multipart/form-data`, the backend returns validation errors indicating that all required fields are missing:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "category_id": ["The category id field is required."],
    "description": ["The description field is required."],
    "name": ["The name field is required."],
    "price": ["The price field is required."],
    "product_number": ["The product number field is required."],
    "stock_quantity": ["The stock quantity field is required."]
  }
}
```

### **Frontend Implementation Attempted**
We've tried multiple FormData approaches with different field names:

```typescript
// Approach 1: Standard FormData
formData.append('name', productData.name);
formData.append('description', productData.description);
formData.append('product_number', productData.product_number);
formData.append('price', productData.price.toString());
formData.append('discount', productData.discount.toString());
formData.append('manufacturing_material', productData.manufacturing_material);
formData.append('manufacturing_country', productData.manufacturing_country);
formData.append('stock_quantity', productData.stock_quantity.toString());
formData.append('is_available', productData.is_available ? '1' : '0');
formData.append('category_id', productData.category_id.toString());
formData.append('image', mainImage);

// Approach 2: Alternative field names
formData.append('main_image', mainImage);
formData.append('product_image', mainImage);
```

---

## 🎯 **Request for Backend Team**

### **1. Expected Request Format Clarification**

Please specify the exact format expected by the backend:

#### **Option A: Single FormData Request**
```
POST /api/products
Content-Type: multipart/form-data

FormData Fields:
- name: "Product Name" (string)
- description: "Product Description" (string)
- product_number: "PROD-001" (string)
- price: "299.99" (string/number)
- discount: "10" (string/number)
- manufacturing_material: "Metal" (string)
- manufacturing_country: "Germany" (string)
- stock_quantity: "5" (string/number)
- is_available: "1" (string: "1" or "0")
- category_id: "11" (string/number)
- image: [File object] (main image)
- images[0]: [File object] (additional image 1)
- images[1]: [File object] (additional image 2)
```

#### **Option B: Two-Step Process**
```
Step 1: Create Product (JSON)
POST /api/products
Content-Type: application/json

{
  "name": "Product Name",
  "description": "Product Description",
  "product_number": "PROD-001",
  "price": 299.99,
  "discount": 10,
  "manufacturing_material": "Metal",
  "manufacturing_country": "Germany",
  "stock_quantity": 5,
  "is_available": 1,
  "category_id": 11
}

Step 2: Upload Images (FormData)
POST /api/products/{id}/upload-image
Content-Type: multipart/form-data

FormData:
- image: [File object] (main image)
- images[]: [File objects] (additional images)
```

### **2. Field Name Specifications**

Please confirm the exact field names expected for:

| Field Type | Possible Names | Which One? |
|------------|----------------|------------|
| **Main Image** | `image`, `main_image`, `product_image`, `file` | ? |
| **Additional Images** | `images[]`, `additional_images[]`, `product_images[]` | ? |
| **Boolean Values** | `"1"/"0"` (string) or `1/0` (number) | ? |

### **3. Content-Type Requirements**

- Should the endpoint accept `multipart/form-data`?
- Are there any specific headers required?
- Should we remove the `Content-Type` header to let the browser set it automatically?

### **4. Validation Rules**

- **Image file types**: What formats are supported? (jpg, png, gif, etc.)
- **File size limits**: Maximum file size allowed?
- **Image dimensions**: Any minimum/maximum width/height requirements?
- **Required fields**: Are all product fields required when uploading images?

---

## 🧪 **Test Cases**

### **Test Case 1: Single FormData Request**
Please test this exact request in Swagger/Postman:

```
POST http://127.0.0.1:8000/api/products
Content-Type: multipart/form-data

FormData:
- name: "Test Product with Image"
- description: "Test Description"
- product_number: "TEST-IMG-001"
- price: "99.99"
- discount: "0"
- manufacturing_material: "Test Material"
- manufacturing_country: "Test Country"
- stock_quantity: "10"
- is_available: "1"
- category_id: "11"
- image: [any image file]
```

**Expected Result**: Product created successfully with image uploaded and `image_url` populated in response.

### **Test Case 2: Multiple Images**
```
POST http://127.0.0.1:8000/api/products
Content-Type: multipart/form-data

FormData:
- name: "Test Product with Multiple Images"
- description: "Test Description"
- product_number: "TEST-IMG-002"
- price: "199.99"
- discount: "0"
- manufacturing_material: "Test Material"
- manufacturing_country: "Test Country"
- stock_quantity: "5"
- is_available: "1"
- category_id: "11"
- image: [main image file]
- images[0]: [additional image 1]
- images[1]: [additional image 2]
```

**Expected Result**: Product created with main image and additional images in `product_images` array.

---

## 📝 **Questions for Backend Team**

1. **Which approach does the backend expect?** (Single FormData or Two-step process)
2. **What are the exact field names** for image uploads?
3. **Are there any specific validation rules** for image files?
4. **Should we use a different endpoint** for image uploads?
5. **Is there any middleware or configuration** that might be blocking FormData processing?
6. **Can you provide a working example** of a successful image upload request?

---

## 🔧 **Current Frontend Implementation**

Our current implementation tries multiple approaches:

```typescript
// Smart fallback strategy
if (hasImages) {
  // Try FormData first
  const formData = new FormData();
  // Add all product fields
  // Add images with multiple field name attempts
  // If FormData fails, fall back to JSON
} else {
  // Use JSON for products without images
}
```

---

## 📞 **Next Steps**

1. **Backend team** to provide exact field names and request format
2. **Frontend team** to implement the correct approach
3. **Testing** to ensure image upload works end-to-end
4. **Documentation** to be updated with the correct implementation

---

**Please provide the correct field names and request format so we can fix the frontend implementation accordingly!**

**Contact**: Frontend Development Team  
**Priority**: High - Blocking product image upload functionality 