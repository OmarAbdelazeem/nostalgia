#!/bin/bash

echo "Testing Laravel application health..."

# Test if Laravel can start
echo "1. Testing Laravel startup..."
php artisan --version

# Test if routes are accessible
echo "2. Testing routes..."
php artisan route:list --compact

# Test if health endpoint works
echo "3. Testing health endpoint..."
curl -s http://localhost:8000/api/health || echo "Health endpoint not accessible (expected if server not running)"

echo "Health check completed!" 