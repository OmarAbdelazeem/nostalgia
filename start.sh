#!/bin/bash

# Enable error handling
set -e

# Log function
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

log "Starting Laravel application..."

# Check if PORT is set
if [ -z "$PORT" ]; then
    log "ERROR: PORT environment variable is not set"
    exit 1
fi

log "Using port: $PORT"

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    log "ERROR: APP_KEY environment variable is not set"
    exit 1
fi

log "APP_KEY is configured"

# Clear caches first
log "Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
log "Caches cleared"

# Create storage link if it doesn't exist
if [ ! -L "public/storage" ]; then
    log "Creating storage link..."
    php artisan storage:link
    log "Storage link created"
else
    log "Storage link already exists"
fi

# Try to run migrations if database is available
if [ ! -z "$DATABASE_URL" ] || [ ! -z "$DB_HOST" ]; then
    log "Database configuration detected, attempting migrations..."
    
    # Wait a bit for database to be ready
    sleep 5
    
    # Try to run migrations, but don't fail if they don't work
    if php artisan migrate --force 2>/dev/null; then
        log "Migrations completed successfully"
    else
        log "WARNING: Migrations failed, continuing anyway"
    fi
else
    log "No database configuration found, skipping migrations"
fi

# Test if the application can start
log "Testing application startup..."
if php artisan route:list --compact >/dev/null 2>&1; then
    log "Application test successful"
else
    log "WARNING: Application test failed, continuing anyway"
fi

# Start the server
log "Starting Laravel server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT 