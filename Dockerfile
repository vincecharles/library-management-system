FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libsqlite3-dev libicu-dev \
    && docker-php-ext-install pdo pdo_sqlite intl bcmath \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project files first
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm install

# Build frontend assets
RUN npm run build

# Create SQLite database
RUN mkdir -p database && touch database/database.sqlite

# Setup Laravel
RUN cp .env.example .env \
    && php artisan key:generate \
    && php artisan migrate --force

EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
