# Website Read PDF - Document Management System

**Advanced Document Management & OCR Processing System**

[Features](#features) • [Installation](#installation) • [Usage](#usage) • [Tech Stack](#tech-stack) • [Backend Repository](#backend-repository)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [System Architecture](#system-architecture)
- [Backend Repository](#backend-repository)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 Overview

**Website Read PDF** adalah sistem manajemen dokumen berbasis web yang terintegrasi dengan teknologi OCR (Optical Character Recognition) dan AI untuk memproses, menganalisis, dan mengelola dokumen PDF secara otomatis. Sistem ini dirancang khusus untuk menangani berbagai jenis dokumen teknis seperti SPK (Surat Perintah Kerja), Form Checklist, dan Form PM POP.

### Key Highlights

- **Automatic Document Processing**: Upload PDF dan sistem akan otomatis mengekstrak data menggunakan OCR
- **Intelligent Document Classification**: AI-powered document type detection
- **Smart Search & Filter**: Pencarian cerdas dengan multiple filter options
- **Interactive Data Visualization**: Charts dan graphs untuk data battery monitoring
- **Real-time Status Updates**: Live polling untuk tracking progress processing
- **Responsive UI**: Modern interface built with React, TypeScript, dan Tailwind CSS

---

## ✨ Features

### 📄 Document Management

- **Multi-Type Document Support**
  - SPK (Surat Perintah Kerja)
  - Form Checklist (Wireline & Wireless)
  - Form PM POP (Preventive Maintenance)
  
- **Intelligent Processing Pipeline**
  - Automatic OCR extraction
  - Document type detection
  - Structured data parsing
  - Image extraction from PDFs

### 🔍 Smart Search System

- Advanced filtering by:
  - Document type
  - Upload date range
  - Status (completed, processing, failed)
  - Location
  - Customer name
- Full-text search capability
- Export results to CSV/Excel

### 📊 Data Visualization

- **Battery Monitoring Dashboard**
  - Voltage charts per cell
  - State of Health (SoH) visualization
  - Battery bank comparison
  - Health status indicators
  
- **Form PM POP Analytics**
  - Equipment inventory tracking
  - Performance metrics
  - Maintenance history

### 🔐 User Management

- Role-based access control (Admin/User)
- User verification system
- Activity logging
- Secure authentication with Laravel Sanctum

### 🤖 AI Integration

- Chatbot for document querying
- Semantic search using embeddings
- Natural language processing for data extraction

---

## 🛠️ Tech Stack

### Frontend

| Technology | Version | Purpose |
|------------|---------|---------|
| React | 18.x | UI Framework |
| TypeScript | 5.x | Type Safety |
| Inertia.js | 1.x | SPA without API |
| Tailwind CSS | 3.x | Styling |
| Shadcn/ui | Latest | Component Library |
| Recharts | 2.x | Data Visualization |
| Axios | 1.x | HTTP Client |

### Backend

| Technology | Version | Purpose |
|------------|---------|---------|
| Laravel | 11.x | Backend Framework |
| PHP | 8.2+ | Server Language |
| MySQL | 8.0+ | Database |
| Redis | 7.x | Caching & Queues |

### Additional Tools

- **Vite**: Frontend build tool
- **Composer**: PHP dependency manager
- **NPM**: Node package manager
- **Laravel Queue**: Background job processing

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                         Frontend                            │
│  React + TypeScript + Inertia.js + Tailwind CSS            │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        │ HTTP/HTTPS
                        │
┌───────────────────────▼─────────────────────────────────────┐
│                    Laravel Backend                          │
│  Routes → Controllers → Services → Models → Database        │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        │ HTTP API
                        │
┌───────────────────────▼─────────────────────────────────────┐
│                 Python OCR Backend                          │
│  FastAPI + PyMuPDF + Tesseract OCR + OpenAI                │
│  Repository: OCR-and-Read-document                         │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔗 Backend Repository

**IMPORTANT**: This project requires the Python OCR backend to function properly.

### OCR & Document Processing Backend

🔗 **Repository**: [OCR-and-Read-document](https://github.com/rahmatisma/OCR-and-Read-document.git)

The backend handles:
- PDF to image conversion
- OCR text extraction using Tesseract
- Intelligent document parsing
- Structure data extraction
- Image processing and optimization

**You must clone and run the backend repository separately for this application to work.**

### Quick Backend Setup

```bash
# Clone the backend repository
git clone https://github.com/rahmatisma/OCR-and-Read-document.git

# Follow the installation instructions in that repository
cd OCR-and-Read-document
# ... (refer to backend README for detailed setup)
```

---

## 📦 Installation

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- NPM or Yarn
- MySQL >= 8.0
- Redis (optional, for queues)
- Python Backend (see [Backend Repository](#backend-repository))

### Step 1: Clone Repository

```bash
git clone https://github.com/rahmatisma/Website-Read-PDF.git
cd Website-Read-PDF
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
npm install
# or
yarn install
```

### Step 4: Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 5: Configure Environment

Edit `.env` file:

```env
APP_NAME="Website Read PDF"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Python Backend URL
PYTHON_API_URL=http://localhost:8000

# Queue Configuration (optional)
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Step 6: Database Migration

```bash
# Run migrations
php artisan migrate

# (Optional) Seed database with sample data
php artisan db:seed
```

### Step 7: Storage Link

```bash
php artisan storage:link
```

### Step 8: Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### Step 9: Start Development Server

```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Vite Dev Server (if using npm run dev)
npm run dev

# Terminal 3: Queue Worker (optional)
php artisan queue:work
```

### Step 10: Setup Python Backend

Follow the instructions in the [OCR-and-Read-document](https://github.com/rahmatisma/OCR-and-Read-document.git) repository to start the Python backend service.

---

## ⚙️ Configuration

### File Storage

Configure storage paths in `config/filesystems.php`:

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

### Queue Configuration

For background processing, configure queues:

```bash
# Start queue worker
php artisan queue:work

# Or use supervisor for production
```

### Python Backend Integration

Update the Python API endpoint in your `.env`:

```env
PYTHON_API_URL=http://localhost:8000
```

---

## 🚀 Usage

### Uploading Documents

1. Navigate to the Documents page
2. Select document type (SPK, Checklist, or PM POP)
3. Upload your PDF file
4. Wait for automatic processing
5. View extracted data

### Searching Documents

1. Go to Search page
2. Use filters:
   - Document Type
   - Date Range
   - Status
   - Location
3. Enter search keywords
4. Export results if needed

### Viewing Battery Data

1. Upload Form PM POP Battery document
2. Wait for processing completion
3. View interactive charts:
   - Voltage per cell
   - State of Health (SoH)
   - Battery bank comparison
   - Health status summary

### User Management (Admin Only)

1. Navigate to Users page
2. Add/Edit/Delete users
3. Assign roles and permissions
4. Toggle admin verification

---

## 📁 Project Structure

```
Website-Read-PDF/
├── app/
│   ├── Console/
│   │   └── Commands/          # Artisan commands
│   ├── Http/
│   │   ├── Controllers/       # Request handlers
│   │   ├── Middleware/        # HTTP middleware
│   │   └── Requests/          # Form requests
│   ├── Models/                # Eloquent models
│   ├── Services/              # Business logic
│   └── Providers/             # Service providers
├── config/                    # Configuration files
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── public/                    # Public assets
├── resources/
│   ├── css/                   # Stylesheets
│   ├── js/
│   │   ├── components/        # React components
│   │   ├── layouts/           # Layout components
│   │   ├── pages/             # Page components
│   │   └── types/             # TypeScript types
│   └── views/                 # Blade templates
├── routes/
│   ├── web.php               # Web routes
│   ├── api.php               # API routes
│   └── auth.php              # Auth routes
├── storage/
│   └── app/
│       └── public/
│           ├── checklists/   # Uploaded checklists
│           ├── pmpop/        # Uploaded PM POP forms
│           ├── uploads/      # General uploads
│           └── output/       # Processed outputs
├── tests/                    # Test files
├── .env.example             # Environment template
├── composer.json            # PHP dependencies
├── package.json             # Node dependencies
├── tsconfig.json            # TypeScript config
└── vite.config.ts           # Vite configuration
```

---

## 📚 API Documentation

### Document Endpoints

#### Upload Document

```http
POST /documents/spk
POST /documents/checklist
POST /documents/pmpop
```

**Request:**
```json
{
  "file": "binary",
  "document_type": "string"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Document uploaded successfully",
  "data": {
    "id_upload": 1,
    "file_name": "example.pdf",
    "status": "uploaded"
  }
}
```

#### Get Document Status

```http
GET /api/documents/{id}/status
```

**Response:**
```json
{
  "status": "completed",
  "updated_at": "2026-02-02T10:30:00Z"
}
```

### Battery Data Endpoints

#### Get Battery Chart Data

```http
GET /api/battery/chart-data-by-upload/{uploadId}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "voltage_chart": [...],
    "soh_chart": [...],
    "bank_summary": [...],
    "metadata": {
      "total_banks": 2,
      "total_cells": 40
    }
  }
}
```

### Search Endpoints

#### Search Documents

```http
GET /search/api/search
```

**Query Parameters:**
- `query`: Search keywords
- `type`: Document type filter
- `status`: Status filter
- `date_from`: Start date
- `date_to`: End date

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 for PHP code
- Use ESLint and Prettier for TypeScript/React
- Write meaningful commit messages
- Add tests for new features

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👥 Author

**Rahmat Isma**

- GitHub: [@rahmatisma](https://github.com/rahmatisma)
- Repository: [Website-Read-PDF](https://github.com/rahmatisma/Website-Read-PDF)
- Backend Repository: [OCR-and-Read-document](https://github.com/rahmatisma/OCR-and-Read-document)

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [React](https://reactjs.org) - JavaScript Library
- [Inertia.js](https://inertiajs.com) - Modern Monolith Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS
- [Shadcn/ui](https://ui.shadcn.com) - Re-usable Components
- [Tesseract OCR](https://github.com/tesseract-ocr/tesseract) - OCR Engine

---

## 📞 Support

If you encounter any issues or have questions:

1. Check the [Issues](https://github.com/rahmatisma/Website-Read-PDF/issues) page
2. Create a new issue with detailed description
3. For backend-related issues, check the [OCR Backend Issues](https://github.com/rahmatisma/OCR-and-Read-document/issues)

---

## 🔄 Related Repositories

- **Backend OCR Service**: [OCR-and-Read-document](https://github.com/rahmatisma/OCR-and-Read-document.git)

---

<div align="center">

**Built with ❤️ using Laravel, React, and TypeScript**

⭐ Star this repository if you find it helpful!

</div>
