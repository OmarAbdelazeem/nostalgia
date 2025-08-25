# Feature Documentation: Authentication & Authorization

This document provides a detailed overview of the authentication, authorization, and user management features of the Nostalgia REST API.

## 1. Goals

The primary goal was to create a robust, secure, and flexible API to handle all aspects of user identity and access control for the Nostalgia project, serving an Angular single-page application.

- Implement a complete user authentication system (Register, Login, Logout).
- Establish a Role-Based Access Control (RBAC) system with "User", "Admin", and "Super Admin" tiers.
- Build secure API endpoints for administrators to manage users (CRUD).
- Build highly-secure API endpoints for super administrators to dynamically manage roles and permissions.
- Provide comprehensive, interactive API documentation.

## 2. Features Implemented & Progress

All initial goals for this feature set have been **completed**.

### 2.1. User Authentication
- **Endpoints:** `POST /api/register`, `POST /api/login`, `POST /api/logout`.
- **Mechanism:** The API uses stateless token-based authentication via Laravel Sanctum.
- **Response Format:** Both `login` and `register` endpoints return a unified response containing the `access_token` and the full `user` object, including their roles and permissions, to optimize frontend performance.

### 2.2. User & Role Details
- **Endpoint:** `GET /api/user`
- **Functionality:** Returns the full details of the currently authenticated user.

### 2.3. User Management (Admin & Super Admin)
- **Endpoints:** Full CRUD functionality via `GET`, `PUT`, `DELETE` on `/api/users` and `/api/users/{id}`.
- **Security:** Access is restricted to users with the "Admin" or "Super Admin" roles.
- **Functionality:** Allows authorized administrators to list, view, update (name, email, roles), and delete users. Includes a safeguard to prevent users from deleting their own accounts.

### 2.4. Dynamic Role Management (Super Admin Only)
- **Endpoints:** Full CRUD functionality via `GET`, `POST`, `PUT`, `DELETE` on `/api/roles` and `/api/roles/{id}`.
- **Security:** Access is strictly limited to users with the "Super Admin" role.
- **Functionality:** Allows the Super Admin to create, view, update, and delete roles, and to assign any available permissions to them. Includes a safeguard to prevent the deletion of core system roles.

### 2.5. Permission Listing (Super Admin Only)
- **Endpoint:** `GET /api/permissions`
- **Security:** Access is strictly limited to users with the "Super Admin" role.
- **Functionality:** Provides a list of all permissions available in the system, allowing a frontend to build a dynamic UI for role management.

## 3. Key Achievements & Technical Details

- **Technology Stack:** Laravel 11, Sanctum, Spatie Laravel Permission, L5-Swagger.
- **Interactive Documentation:** A complete, interactive Swagger UI is available at `/api/documentation`, allowing for easy testing and verification of all endpoints.
- **Optimized for Frontend:** The authentication flow has been specifically optimized based on frontend team feedback to reduce latency and simplify state management.
- **Robust Security:** The combination of Sanctum and Spatie's Role/Permission middleware provides layered, secure access control for all sensitive data and operations.
- **Laravel 11 Configuration:** Successfully navigated and resolved multiple configuration challenges specific to the new minimal Laravel 11 framework, including route registration, middleware aliasing, and rate limiter definitions.

This feature is now complete, fully tested, and ready for frontend integration. 