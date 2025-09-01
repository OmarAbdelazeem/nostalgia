# Postman Setup Guide for Laravel API

## 🚀 **Complete Postman Configuration**

**Date**: September 1, 2025  
**API Base URL**: `http://localhost:8000/api`  
**Status**: ✅ **Ready for Import**  

---

## 📋 **Step 1: Create Environment Variables**

### **Create New Environment in Postman:**

1. **Click** the "Environments" tab in Postman
2. **Click** "New Environment"
3. **Name**: `Laravel API - Local`
4. **Add Variables**:

| Variable Name | Initial Value | Current Value | Description |
|---------------|---------------|---------------|-------------|
| `base_url` | `http://localhost:8000/api` | `http://localhost:8000/api` | API base URL |
| `access_token` | (leave empty) | (leave empty) | Bearer token after login |
| `user_email` | `admin@example.com` | `admin@example.com` | Default admin email |
| `user_password` | `password` | `password` | Default admin password |

---

## 📁 **Step 2: Import API Collection**

### **Create New Collection:**

1. **Click** "Collections" tab
2. **Click** "New Collection"
3. **Name**: `Laravel API - Nostalgia`
4. **Description**: `Complete API collection for Laravel Nostalgia application`

---

## 🔐 **Step 3: Authentication Setup**

### **Create Login Request:**

1. **Right-click** on collection → "Add Request"
2. **Name**: `Login`
3. **Method**: `POST`
4. **URL**: `{{base_url}}/login`
5. **Headers**:
   ```
   Content-Type: application/json
   Accept: application/json
   ```
6. **Body** (raw JSON):
   ```json
   {
     "email": "{{user_email}}",
     "password": "{{user_password}}"
   }
   ```
7. **Tests** (JavaScript):
   ```javascript
   if (pm.response.code === 200) {
       const response = pm.response.json();
       pm.environment.set("access_token", response.access_token);
       console.log("Token saved:", response.access_token);
   }
   ```

---

## 📚 **Step 4: API Endpoints Setup**

### **Authentication Endpoints:**

#### **1. Register**
- **Method**: `POST`
- **URL**: `{{base_url}}/register`
- **Headers**: `Content-Type: application/json`
- **Body**:
  ```json
  {
    "name": "New Admin User",
    "email": "newadmin@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```

#### **2. Logout**
- **Method**: `POST`
- **URL**: `{{base_url}}/logout`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **3. Get Current User**
- **Method**: `GET`
- **URL**: `{{base_url}}/user`
- **Headers**: `Authorization: Bearer {{access_token}}`

### **Categories Endpoints:**

#### **1. Get All Categories**
- **Method**: `GET`
- **URL**: `{{base_url}}/categories`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **2. Create Category**
- **Method**: `POST`
- **URL**: `{{base_url}}/categories`
- **Headers**: `Authorization: Bearer {{access_token}}`
- **Body** (form-data):
  ```
  name: Test Category
  description: Test Description
  image: [file upload]
  ```

#### **3. Get Category by ID**
- **Method**: `GET`
- **URL**: `{{base_url}}/categories/1`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **4. Update Category**
- **Method**: `PUT`
- **URL**: `{{base_url}}/categories/1`
- **Headers**: `Authorization: Bearer {{access_token}}`
- **Body** (form-data):
  ```
  name: Updated Category
  description: Updated Description
  image: [file upload]
  ```

#### **5. Update Category (Alternative)**
- **Method**: `POST`
- **URL**: `{{base_url}}/categories/1/update`
- **Headers**: `Authorization: Bearer {{access_token}}`
- **Body** (form-data):
  ```
  name: Updated Category
  description: Updated Description
  image: [file upload]
  ```

#### **6. Delete Category**
- **Method**: `DELETE`
- **URL**: `{{base_url}}/categories/1`
- **Headers**: `Authorization: Bearer {{access_token}}`

### **Products Endpoints:**

#### **1. Get All Products**
- **Method**: `GET`
- **URL**: `{{base_url}}/products`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **2. Get Products with Filters**
- **Method**: `GET`
- **URL**: `{{base_url}}/products?search=test&category_id=1&min_price=10&max_price=100&is_available=1`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **3. Create Product**
- **Method**: `POST`
- **URL**: `{{base_url}}/products`
- **Headers**: `Authorization: Bearer {{access_token}}`
- **Body** (raw JSON):
  ```json
  {
    "name": "Test Product",
    "description": "Test Description",
    "product_number": "TEST-001",
    "price": 99.99,
    "discount": 0,
    "manufacturing_material": "Test Material",
    "manufacturing_country": "Test Country",
    "stock_quantity": 10,
    "is_available": 1,
    "category_id": 1
  }
  ```

#### **4. Get Product by ID**
- **Method**: `GET`
- **URL**: `{{base_url}}/products/1`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **5. Update Product**
- **Method**: `PUT`
- **URL**: `{{base_url}}/products/1`
- **Headers**: `Authorization: Bearer {{access_token}}`
- **Body** (raw JSON):
  ```json
  {
    "name": "Updated Product",
    "description": "Updated Description",
    "price": 149.99,
    "stock_quantity": 20,
    "is_available": 1
  }
  ```

#### **6. Delete Product**
- **Method**: `DELETE`
- **URL**: `{{base_url}}/products/1`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **7. Upload Product Images**
- **Method**: `POST`
- **URL**: `{{base_url}}/products/1/upload-image`
- **Headers**: `Authorization: Bearer {{access_token}}`
- **Body** (form-data):
  ```
  image: [main image file]
  images[]: [additional image file 1]
  images[]: [additional image file 2]
  ```

### **Product Images Endpoints:**

#### **1. Get Product Images**
- **Method**: `GET`
- **URL**: `{{base_url}}/products/1/images`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **2. Add Product Image**
- **Method**: `POST`
- **URL**: `{{base_url}}/products/1/images`
- **Headers**: `Authorization: Bearer {{access_token}}`
- **Body** (form-data):
  ```
  image: [image file]
  alt_text: Image description
  ```

#### **3. Delete Product Image**
- **Method**: `DELETE`
- **URL**: `{{base_url}}/products/1/images/1`
- **Headers**: `Authorization: Bearer {{access_token}}`

### **User Management Endpoints:**

#### **1. Get All Users**
- **Method**: `GET`
- **URL**: `{{base_url}}/users`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **2. Get User by ID**
- **Method**: `GET`
- **URL**: `{{base_url}}/users/1`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **3. Update User**
- **Method**: `PUT`
- **URL**: `{{base_url}}/users/1`
- **Headers**: `Authorization: Bearer {{access_token}}`
- **Body** (raw JSON):
  ```json
  {
    "name": "Updated User Name",
    "email": "updated@example.com"
  }
  ```

#### **4. Delete User**
- **Method**: `DELETE`
- **URL**: `{{base_url}}/users/1`
- **Headers**: `Authorization: Bearer {{access_token}}`

### **Roles & Permissions Endpoints:**

#### **1. Get All Roles**
- **Method**: `GET`
- **URL**: `{{base_url}}/roles`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### **2. Get All Permissions**
- **Method**: `GET`
- **URL**: `{{base_url}}/permissions`
- **Headers**: `Authorization: Bearer {{access_token}}`

---

## 🧪 **Step 5: Testing Scripts**

### **Login Test Script:**
```javascript
// Add this to the Login request Tests tab
if (pm.response.code === 200) {
    const response = pm.response.json();
    pm.environment.set("access_token", response.access_token);
    pm.environment.set("user_id", response.user.id);
    console.log("✅ Login successful");
    console.log("Token saved:", response.access_token);
    console.log("User ID:", response.user.id);
    console.log("User roles:", response.user.roles.map(r => r.name));
} else {
    console.log("❌ Login failed:", pm.response.text());
}
```

### **Category Creation Test Script:**
```javascript
// Add this to Create Category request Tests tab
if (pm.response.code === 201) {
    const response = pm.response.json();
    pm.environment.set("category_id", response.id);
    console.log("✅ Category created successfully");
    console.log("Category ID:", response.id);
    console.log("Category name:", response.name);
} else {
    console.log("❌ Category creation failed:", pm.response.text());
}
```

### **Product Creation Test Script:**
```javascript
// Add this to Create Product request Tests tab
if (pm.response.code === 201) {
    const response = pm.response.json();
    pm.environment.set("product_id", response.id);
    console.log("✅ Product created successfully");
    console.log("Product ID:", response.id);
    console.log("Product name:", response.name);
} else {
    console.log("❌ Product creation failed:", pm.response.text());
}
```

---

## 📊 **Step 6: Collection Variables**

### **Add Collection Variables:**
1. **Right-click** on collection → "Edit"
2. **Go to** "Variables" tab
3. **Add Variables**:

| Variable Name | Initial Value | Current Value |
|---------------|---------------|---------------|
| `category_id` | (leave empty) | (leave empty) |
| `product_id` | (leave empty) | (leave empty) |
| `user_id` | (leave empty) | (leave empty) |

---

## 🔄 **Step 7: Request Order**

### **Recommended Testing Order:**

1. **Login** → Get access token
2. **Register** → Create new admin user
3. **Get Categories** → View existing categories
4. **Create Category** → Create new category
5. **Get Products** → View existing products
6. **Create Product** → Create new product
7. **Upload Product Images** → Add images to product
8. **Test other endpoints** → Update, delete, etc.

---

## 🎯 **Step 8: Common Headers**

### **Standard Headers for All Requests:**
```
Accept: application/json
Content-Type: application/json
Authorization: Bearer {{access_token}}
```

### **For File Uploads:**
```
Accept: application/json
Authorization: Bearer {{access_token}}
```
*(Don't set Content-Type for file uploads - Postman sets it automatically)*

---

## 🚨 **Step 9: Error Handling**

### **Common Error Responses:**

#### **401 Unauthorized:**
```json
{
  "message": "Unauthorized"
}
```
**Solution**: Run Login request first to get valid token

#### **422 Validation Error:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Error message"]
  }
}
```
**Solution**: Check request body and validation rules

#### **404 Not Found:**
```json
{
  "message": "Resource not found"
}
```
**Solution**: Check if ID exists in database

---

## 📁 **Step 10: File Upload Testing**

### **For Category Images:**
1. **Select** "form-data" in Body tab
2. **Add** key: `image`
3. **Select** "File" type
4. **Choose** image file

### **For Product Images:**
1. **Select** "form-data" in Body tab
2. **Add** key: `image` (for main image)
3. **Add** key: `images[]` (for additional images)
4. **Select** "File" type for each
5. **Choose** image files

---

## 🎉 **Step 11: Quick Start Checklist**

### **✅ Setup Complete When:**
- [ ] Environment created with variables
- [ ] Collection created with all endpoints
- [ ] Login request working and saving token
- [ ] Can create categories and products
- [ ] Can upload images
- [ ] All CRUD operations working

### **✅ Testing Complete When:**
- [ ] Authentication flow works
- [ ] Category management works
- [ ] Product management works
- [ ] Image upload works
- [ ] Error handling works
- [ ] All endpoints return expected responses

---

## 📞 **Support**

### **If You Encounter Issues:**

1. **Check Laravel logs**: `tail -f storage/logs/laravel.log`
2. **Verify server is running**: `php artisan serve`
3. **Check database**: Ensure migrations are run
4. **Verify environment**: Check `.env` file configuration

### **Common Issues:**
- **CORS errors**: Check `config/cors.php`
- **Token issues**: Re-run Login request
- **File upload errors**: Check file size and type
- **Validation errors**: Check request body format

---

**Status**: ✅ **READY FOR POSTMAN IMPORT**  
**API Base URL**: `http://localhost:8000/api`  
**Authentication**: Bearer Token (Sanctum) 