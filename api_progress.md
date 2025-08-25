# API Development Progress

This document tracks the goals, progress, and challenges during the development of the RESTful API.

## Goals

- **Completed:** Create a complete RESTful API for user authentication and authorization.
- **Completed:** Use Laravel 11, Sanctum, and `spatie/laravel-permission`.
- **Completed:** The API will serve an existing Angular single-page application (SPA).
- **Completed:** Implement endpoints for registration, login, logout, and fetching user data.
- **Completed:** Include role-based access control (RBAC) with "User", "Admin", and "Super Admin" roles.
- **Completed:** Seed the database with initial roles, permissions, and sample users.
- **Completed:** Implemented a user management endpoint (`/api/users`) to list all users.
- **Completed:** Protected the user management endpoint using role-based access control (`Admin` & `Super Admin` only).
- **Completed:** Implemented full CRUD (Show, Update, Delete) functionality for the user management endpoints.

## Handoff Notes for Frontend Integration

- The API is now complete and ready for frontend integration.
- It uses stateless token-based authentication (Bearer Token).
- CORS is configured to accept requests from `http://localhost:4200`.
- See below for interactive documentation and test credentials.

## Progress

- **Completed:** User authentication endpoints (register, login, logout, get user) are fully functional.
- **Completed:** Integrated interactive API documentation with Swagger UI.
- **Completed:** Configured Sanctum for stateless token-based authentication.
- **Completed:** Seeded the database with roles, permissions, and sample users.
- **Completed:** Installed and configured all necessary dependencies (`Sanctum`, `spatie/laravel-permission`, `l5-swagger`).
- Initial setup complete.

## Challenges

- **Resolved:** Encountered a `BindingResolutionException` (Target class [role] does not exist) when using role-based middleware.
  - **Solution:** Manually aliased the `role` middleware from the Spatie package in `bootstrap/app.php`, as this is now required in Laravel 11.
- **Resolved:** Encountered a `NotFoundHttpException` because API routes were not loaded by default in Laravel 11.
  - **Solution:** Explicitly registered the API routes file in `bootstrap/app.php`.
- **Resolved:** Ran into a `CSRF token mismatch` error when testing from Swagger UI.
  - **Solution:** Switched to stateless authentication by removing the `EnsureFrontendRequestsAreStateful` middleware, as stateful CSRF protection is not needed for token-based API testing and will be configured later for the SPA frontend.
- **Resolved:** Faced a `MissingRateLimiterException` because the `api` rate limiter was not defined in the minimal Laravel 11 installation.
  - **Solution:** Defined the default rate limiter in the `AppServiceProvider`.