#!/bin/bash
set -e

echo "🚀 Starting AklatBayon LMS..."

# Run migrations (safe: --force skips confirmation in production)
echo "📦 Running database migrations..."
php artisan migrate --force 2>&1 || echo "⚠️  Migration failed — database may not be ready yet."

# Clear and cache config/routes for performance
php artisan config:cache 2>&1 || true
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true

echo "✅ Application ready. Starting server on port 8080..."

# Start the PHP development server
exec php artisan serve --host=0.0.0.0 --port=8080
