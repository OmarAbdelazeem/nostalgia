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

# Wait for database to be ready and run migrations
if [ ! -z "$DATABASE_URL" ] || [ ! -z "$DB_HOST" ]; then
    log "Database configuration detected..."
    
    # Wait for database connection
    log "Waiting for database connection..."
    for i in {1..30}; do
        if php artisan tinker --execute="echo 'DB connected';" 2>/dev/null; then
            log "Database connection successful"
            break
        fi
        log "Attempt $i: Database not ready, waiting..."
        sleep 2
    done
    
    # Run migrations
    log "Running database migrations..."
    php artisan migrate --force || {
        log "ERROR: Migrations failed"
        exit 1
    }
    log "Migrations completed successfully"
    
    # Run seeders
    log "Running database seeders..."
    php artisan db:seed --force || {
        log "WARNING: Seeders failed, continuing anyway"
    }
    log "Seeders completed"
else
    log "No database configuration found, skipping database operations"
fi

# Clear caches
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

# Test the application
log "Testing application..."
php artisan route:list --compact

# Start the server
log "Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT 