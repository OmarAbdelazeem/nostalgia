#!/bin/bash

# Laravel API Deployment Script
# This script helps prepare your Laravel API for deployment

echo "🚀 Laravel API Deployment Preparation"
echo "====================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from your Laravel project root."
    exit 1
fi

echo "✅ Laravel project detected"

# Create deployment files if they don't exist
echo "📝 Creating deployment files..."

# Create Procfile if it doesn't exist
if [ ! -f "Procfile" ]; then
    echo "web: vendor/bin/heroku-php-apache2 public/" > Procfile
    echo "✅ Created Procfile"
else
    echo "✅ Procfile already exists"
fi

# Create railway.json if it doesn't exist
if [ ! -f "railway.json" ]; then
    cat > railway.json << EOF
{
    "\$schema": "https://railway.app/railway.schema.json",
    "build": {
        "builder": "NIXPACKS"
    },
    "deploy": {
        "startCommand": "php artisan serve --host=0.0.0.0 --port=\$PORT",
        "healthcheckPath": "/api/health",
        "healthcheckTimeout": 100,
        "restartPolicyType": "ON_FAILURE",
        "restartPolicyMaxRetries": 10
    }
}
EOF
    echo "✅ Created railway.json"
else
    echo "✅ railway.json already exists"
fi

# Create render.yaml if it doesn't exist
if [ ! -f "render.yaml" ]; then
    cat > render.yaml << EOF
services:
  - type: web
    name: laravel-nostalgia-api
    env: php
    plan: free
    buildCommand: composer install --no-dev --optimize-autoloader
    startCommand: php artisan serve --host=0.0.0.0 --port=\$PORT
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: LOG_CHANNEL
        value: stack
      - key: CACHE_DRIVER
        value: file
      - key: SESSION_DRIVER
        value: file
      - key: QUEUE_CONNECTION
        value: sync
    healthCheckPath: /api/health

databases:
  - name: laravel-nostalgia-db
    databaseName: laravel_nostalgia
    user: laravel_user
    plan: free
EOF
    echo "✅ Created render.yaml"
else
    echo "✅ render.yaml already exists"
fi

# Check if health route exists
if grep -q "health" routes/api.php; then
    echo "✅ Health check route exists"
else
    echo "⚠️  Health check route not found. Please add it to routes/api.php"
fi

# Test health endpoint
echo "🧪 Testing health endpoint..."
if curl -s http://localhost:8000/api/health > /dev/null 2>&1; then
    echo "✅ Health endpoint is working"
else
    echo "⚠️  Health endpoint not accessible. Make sure your server is running."
fi

echo ""
echo "🎯 Deployment Platforms:"
echo "======================="
echo "1. Railway (Recommended):"
echo "   - Go to https://railway.app"
echo "   - Sign up with GitHub"
echo "   - Create new project from GitHub repo"
echo "   - Add PostgreSQL database"
echo "   - Set environment variables"
echo ""
echo "2. Render:"
echo "   - Go to https://render.com"
echo "   - Sign up with GitHub"
echo "   - Create new web service"
echo "   - Connect your repository"
echo "   - Add PostgreSQL database"
echo ""
echo "3. Heroku:"
echo "   - Install Heroku CLI"
echo "   - Run: heroku create your-app-name"
echo "   - Run: heroku addons:create heroku-postgresql:mini"
echo "   - Run: git push heroku main"
echo ""
echo "📋 Required Environment Variables:"
echo "=================================="
echo "APP_KEY=base64:your-generated-key"
echo "APP_ENV=production"
echo "APP_DEBUG=false"
echo "APP_URL=https://your-app-name.railway.app"
echo "DB_CONNECTION=postgresql"
echo ""
echo "🔧 Post-Deployment Commands:"
echo "============================"
echo "php artisan migrate --force"
echo "php artisan storage:link"
echo ""
echo "✅ Deployment preparation complete!"
echo "📚 See FREE_DEPLOYMENT_GUIDE.md for detailed instructions" 