# Website Read PDF - Document Management System

**Advanced Document Management & OCR Processing System**

Sistem manajemen dokumen berbasis web yang terintegrasi dengan teknologi OCR (Optical Character Recognition) dan AI untuk memproses, menganalisis, dan mengelola dokumen PDF secara otomatis.

---

## Backend Repository

**IMPORTANT**: This project requires a separate Python backend for OCR processing.

**Python OCR Backend Repository**: [https://github.com/rahmatisma/OCR-and-Read-document.git](https://github.com/rahmatisma/OCR-and-Read-document.git)

The backend handles:
- PDF to image conversion
- OCR text extraction using Tesseract
- Intelligent document parsing
- Structured data extraction
- Image processing and optimization

**You must clone and setup the backend repository separately for this application to work.**

---

## Tech Stack

### Backend
- **Laravel**: 12.x
- **PHP**: 8.2+
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Queue**: Redis 7.x (optional)

### Frontend
- **React**: 18.3
- **TypeScript**: 5.7
- **Inertia.js**: 2.2
- **Vite**: 7.0
- **Tailwind CSS**: 4.0
- **Radix UI**: Various components
- **Recharts**: 3.6 (for data visualization)
- **Lucide React**: 0.475 (icons)

### Development Tools
- **Laravel Pint**: Code styling
- **Pest PHP**: Testing framework
- **ESLint**: JavaScript linting
- **Prettier**: Code formatting

---

## System Requirements

### Required Software

1. **PHP** >= 8.2
   - Extensions required:
     - BCMath
     - Ctype
     - Fileinfo
     - JSON
     - Mbstring
     - OpenSSL
     - PDO
     - Tokenizer
     - XML
     - cURL
     - GD or Imagick

2. **Composer** >= 2.0

3. **Node.js** >= 18.x (recommended: 20.x LTS)

4. **NPM** >= 9.x or **Yarn** >= 1.22

5. **Database**: MySQL >= 8.0 or PostgreSQL >= 13

6. **Redis** >= 7.x (optional, for queues and caching)

7. **Git**

8. **Python Backend**: See [OCR-and-Read-document](https://github.com/rahmatisma/OCR-and-Read-document.git) repository

---

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/rahmatisma/Website-Read-PDF.git
cd Website-Read-PDF
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

This will install:
- Laravel Framework 12.x
- Inertia.js Laravel adapter
- Guzzle HTTP client
- Laravel Tinker
- Ziggy (route helper for JavaScript)
- And all development dependencies (Pest, Pint, Sail, etc.)

### Step 3: Install Node.js Dependencies

```bash
npm install
```

This will install:
- React 18.3 and React DOM
- TypeScript 5.7
- Vite 7.0 and Laravel Vite Plugin
- Inertia.js React adapter
- Tailwind CSS 4.0
- Radix UI components
- Recharts for data visualization
- Lucide React for icons
- All development tools (ESLint, Prettier, etc.)

### Step 4: Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 5: Configure Environment Variables

Edit the `.env` file with your configuration:

```env
# Application
APP_NAME="Website Read PDF"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=website_read_pdf
DB_USERNAME=root
DB_PASSWORD=

# For PostgreSQL (alternative)
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=website_read_pdf
# DB_USERNAME=postgres
# DB_PASSWORD=

# Queue Configuration (optional but recommended)
QUEUE_CONNECTION=database
# If using Redis:
# QUEUE_CONNECTION=redis

# Redis Configuration (optional)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
# If using Redis:
# SESSION_DRIVER=redis
# CACHE_STORE=redis

# Python Backend API
PYTHON_API_URL=http://localhost:8000
# Adjust this to match your Python backend URL

# Mail Configuration (optional)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 6: Create Database

Create a new database for the application:

**MySQL:**
```bash
mysql -u root -p
CREATE DATABASE website_read_pdf CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**PostgreSQL:**
```bash
psql -U postgres
CREATE DATABASE website_read_pdf;
\q
```

### Step 7: Run Database Migrations

```bash
php artisan migrate
```

This will create all necessary tables:
- users
- documents/uploads
- spk (Surat Perintah Kerja)
- form_checklist (wireline & wireless)
- form_pm_pop (various types)
- battery_bank_metadata
- battery_measurements
- inspections
- equipment
- locations
- And many more...

### Step 8: Seed Database (Optional)

If you want sample data for testing:

```bash
php artisan db:seed
```

### Step 9: Create Storage Symlink

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

### Step 10: Build Frontend Assets

**For Development:**
```bash
npm run dev
```

**For Production:**
```bash
npm run build
```

### Step 11: Setup Python Backend

**Important**: Follow the installation instructions in the Python backend repository:

```bash
# Clone the Python backend (in a separate directory)
cd ..
git clone https://github.com/rahmatisma/OCR-and-Read-document.git
cd OCR-and-Read-document

# Follow the setup instructions in that repository's README
# Install Python dependencies, Tesseract OCR, etc.
# Start the Python backend service
```

Make sure the Python backend is running before using the document upload features.

---

## Running the Application

### Development Mode

**Option 1: Using Composer Script (Recommended)**

This will start Laravel server, queue worker, and Vite dev server simultaneously:

```bash
composer dev
```

**Option 2: Manual (3 separate terminals)**

Terminal 1 - Laravel Server:
```bash
php artisan serve
```

Terminal 2 - Queue Worker (for background jobs):
```bash
php artisan queue:work
```

Terminal 3 - Vite Dev Server:
```bash
npm run dev
```

**Option 3: Using Laravel Sail (Docker)**

If you prefer Docker:
```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan queue:work
npm run dev
```

### Production Mode

```bash
# Build frontend assets
npm run build

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start queue worker as a daemon (use supervisor)
php artisan queue:work --daemon

# Use a process manager like Supervisor to keep it running
# Configure your web server (Nginx/Apache) to serve the application
```

---

## Accessing the Application

After starting the servers, open your browser:

- **Application URL**: http://localhost:8000
- **Python Backend URL**: http://localhost:8000 (or the port you configured)

Default admin credentials (if using seeder):
- Email: admin@example.com
- Password: password

---

## Additional Configuration

### Queue Configuration

For better performance, use Redis for queues:

1. Install Redis:
```bash
# Ubuntu/Debian
sudo apt-get install redis-server

# macOS
brew install redis
```

2. Update `.env`:
```env
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
```

3. Install PHP Redis extension:
```bash
# Ubuntu/Debian
sudo apt-get install php-redis

# macOS
pecl install redis
```

### File Upload Configuration

If you need to upload large files, update `php.ini`:

```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 512M
```

### Storage Directories

Ensure these directories are writable:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## Useful Commands

### Laravel Artisan

```bash
# Clear all caches
php artisan optimize:clear

# Generate IDE helper (for better autocomplete)
php artisan ide-helper:generate
php artisan ide-helper:models
php artisan ide-helper:meta

# Run tests
php artisan test
# or
composer test

# Code formatting
./vendor/bin/pint

# Check routes
php artisan route:list

# Tinker (Laravel REPL)
php artisan tinker
```

### NPM Scripts

```bash
# Development with hot reload
npm run dev

# Build for production
npm run build

# Build with SSR support
npm run build:ssr

# Code formatting
npm run format

# Check formatting
npm run format:check

# Run linter
npm run lint

# Type checking
npm run types
```

### Composer Scripts

```bash
# Start all services (server + queue + vite)
composer dev

# Start with SSR support
composer dev:ssr

# Run tests
composer test
```

---

## Troubleshooting

### Common Issues

1. **"Vite manifest not found"**
   ```bash
   npm run build
   ```

2. **Database connection error**
   - Check database credentials in `.env`
   - Ensure database server is running
   - Verify database exists

3. **Storage permission errors**
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

4. **Queue not processing**
   ```bash
   php artisan queue:restart
   php artisan queue:work
   ```

5. **Python backend not responding**
   - Check if Python backend is running
   - Verify `PYTHON_API_URL` in `.env`
   - Check Python backend logs

6. **Class not found errors**
   ```bash
   composer dump-autoload
   php artisan optimize:clear
   ```

---

## Project Structure

```
Website-Read-PDF/
├── app/
│   ├── Http/Controllers/     # Request handlers
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic
│   └── Console/Commands/     # Artisan commands
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── js/                   # React/TypeScript files
│   │   ├── components/       # React components
│   │   ├── pages/            # Inertia pages
│   │   ├── layouts/          # Layout components
│   │   └── types/            # TypeScript types
│   └── css/                  # Stylesheets
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
├── storage/
│   └── app/public/
│       ├── uploads/          # Uploaded files
│       ├── checklists/       # Checklist PDFs
│       ├── pmpop/            # PM POP PDFs
│       └── output/           # Processed outputs
├── public/                   # Public assets
├── .env                      # Environment config
├── composer.json             # PHP dependencies
├── package.json              # Node dependencies
└── vite.config.ts           # Vite configuration
```

---

## Related Repositories

**Backend OCR Service**: [OCR-and-Read-document](https://github.com/rahmatisma/OCR-and-Read-document.git)

---

## Support

For issues or questions:
- Open an issue on [GitHub Issues](https://github.com/rahmatisma/Website-Read-PDF/issues)
- For backend-related issues: [Backend Issues](https://github.com/rahmatisma/OCR-and-Read-document/issues)

---

## Author

**Rahmat Isma**
- GitHub: [@rahmatisma](https://github.com/rahmatisma)

---

## License

This project is licensed under the MIT License.
