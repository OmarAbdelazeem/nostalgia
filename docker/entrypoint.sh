#!/usr/bin/env bash
set -euxo pipefail

# Ensure SSH host keys exist (idempotent)
echo "Generating SSH host keys..."
ssh-keygen -A

# Start sshd with logs to stderr; listen on 2222
echo "Starting SSH daemon on port 2222..."
/usr/sbin/sshd -D -e -p 2222 &

# Small wait, then print diagnostics (will appear in Container Logs)
sleep 2
echo "=== SSH Diagnostics ==="
( command -v ss >/dev/null && ss -lntp || netstat -tlnp ) || true
pgrep -fal sshd || true
echo "=== End SSH Diagnostics ==="

# Run Laravel optimizations (don't fail startup if they fail)
echo "Running Laravel optimizations..."
php artisan config:cache || true
php artisan route:cache || true
php artisan migrate --force || true

echo "Starting Apache..."
# Hand off to Apache (or whatever "$@" is)
exec "$@"
