#!/bin/bash

echo "===================================="
echo "Face Recognition Attendance System"
echo "Laravel Installation Script"
echo "===================================="
echo ""

echo "[1/7] Installing Composer dependencies..."
composer install
if [ $? -ne 0 ]; then
    echo "ERROR: Composer install failed!"
    exit 1
fi
echo ""

echo "[2/7] Installing NPM dependencies..."
npm install
if [ $? -ne 0 ]; then
    echo "ERROR: NPM install failed!"
    exit 1
fi
echo ""

echo "[3/7] Creating .env file..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo ".env file created"
else
    echo ".env file already exists, skipping..."
fi
echo ""

echo "[4/7] Generating application key..."
php artisan key:generate
echo ""

echo "[5/7] Running database migrations..."
php artisan migrate
if [ $? -ne 0 ]; then
    echo "WARNING: Migration failed. Please check your database configuration in .env"
    echo "You can run 'php artisan migrate' manually later."
fi
echo ""

echo "[6/7] Building assets..."
npm run build
if [ $? -ne 0 ]; then
    echo "ERROR: Asset build failed!"
    exit 1
fi
echo ""

echo "[7/7] Creating storage link..."
php artisan storage:link
echo ""

echo "===================================="
echo "Installation Complete!"
echo "===================================="
echo ""
echo "Next steps:"
echo "1. Configure your database in .env file"
echo "2. Run: php artisan migrate (if not already done)"
echo "3. Start Python API on port 8001"
echo "4. Start Laravel: php artisan serve"
echo "5. Visit: http://localhost:8000"
echo ""
echo "Default Admin Login:"
echo "  Email: admin@example.com"
echo "  Password: password"
echo ""
echo "IMPORTANT: Change the admin password after first login!"
echo ""
