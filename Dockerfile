FROM php:8.3-cli

# ── Install ALL system deps + PHP extensions in one layer ──
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libicu-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring intl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# ── Node.js 22 ──
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# ── Composer ──
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# ── Copy composer files first (layer caching) ──
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# ── Copy package files for npm (layer caching) ──
COPY package.json package-lock.json* ./
RUN npm ci --production=false

# ── Copy everything else ──
COPY . .

# ── Rebuild autoload with all project files ──
RUN composer dump-autoload --optimize

# ── Build frontend assets ──
RUN npm run build

# ── Create storage directories Laravel needs ──
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ── Copy .env.example so artisan commands can bootstrap ──
# (Real env vars come from Render's Environment settings)
RUN cp .env.example .env \
    && php artisan key:generate

# ── DO NOT run migrations here — no DB at build time ──
# Migrations run at container start via the entrypoint script

EXPOSE 8080

# ── Entrypoint: run migrations then start the server ──
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh
ENTRYPOINT ["/docker-entrypoint.sh"]
