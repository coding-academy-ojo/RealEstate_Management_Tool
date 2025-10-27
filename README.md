# 🏢 Orange Jordan - Real Estate Management System

An enterprise-grade Laravel-based property management system designed specifically for **Orange Jordan** to track and manage all company sites, lands, buildings, and infrastructure across Jordan. This comprehensive platform centralizes property information, utilities (water & electricity), maintenance records, renovations, and provides role-based access for administrators and field engineers.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=flat&logo=tailwind-css)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=flat&logo=bootstrap)
![License](https://img.shields.io/badge/License-MIT-green.svg)

---

## 🎯 Purpose

This system was developed for **Orange Jordan** telecommunications company to provide:
- Centralized tracking of all company-owned sites, lands, and buildings nationwide
- Real-time monitoring of utilities (water & electricity services)
- Complete maintenance and renovation (Re-Innovation) history
- Role-based access control for administrators and field engineers
- Comprehensive documentation and reporting capabilities

---

## 📋 Table of Contents

- [Features](#-features)
- [System Architecture](#-system-architecture)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Database Structure](#-database-structure)
- [Usage](#-usage)
- [Screenshots](#-screenshots)
- [API Endpoints](#-api-endpoints)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### 🏗️ Core Property Management
- **Multi-Site Management**: Track all Orange Jordan sites across 12 governorates
- **Dynamic Land Registration**: Unlimited land parcels per site with complete location data
- **Building Inventory**: Monitor all buildings with specifications, floor counts, and types
- **Hierarchical Structure**: Sites → Lands → Buildings with automatic code generation
- **Zoning Status Management**: Multi-select zoning classifications with custom status creation

### 💧⚡ Utilities Management
- **Water Services Tracking**: Monitor water connections, meter readings, and consumption
  - Service provider information
  - Meter numbers and locations
  - Monthly consumption tracking
  - Service dates and status
- **Electricity Services Tracking**: Complete electrical infrastructure monitoring
  - Meter readings and account numbers
  - Power consumption analytics
  - Service provider details
  - Connection dates and billing information

### 🔧 Maintenance & Renovations (Re-Innovation)
- **Complete Tracking System**: Monitor all maintenance and renovation activities
  - Scheduled and emergency maintenance
  - Renovation projects and upgrades
  - Work order management
  - Polymorphic relations (Sites/Lands/Buildings)
- **Project Management**:
  - Project timelines and budgets
  - Actual cost vs. budget tracking
  - Status monitoring (planned, in_progress, completed)
  - Before/after documentation
- **Resource Management**:
  - Contractor/vendor information
  - Assigned engineer tracking
  - Material and labor costs
  - Completion certificates

### 👥 Role-Based Access Control
- **Super Admin**: Full system access
  - User management
  - System configuration
  - All CRUD operations
  - Reports and analytics
  - Audit logs
- **Engineers**: Field-level access
  - Site inspections
  - Re-Innovation (maintenance/renovation) reporting
  - Utility readings
  - Limited editing capabilities
  - Mobile-friendly interface

### 🗺️ Geographical Features
- **Jordan Regional Division**: Organized by 4 regions (Capital, North, Middle, South)
- **Governorate Support**: All 12 governorates with Arabic/English names
- **Google Maps Integration**: Automatic coordinate extraction from Maps URLs
- **Location Hierarchy**: Directorate → Village → Basin → Neighborhood → Plot
- **GPS Coordinates**: Latitude/Longitude tracking for precise location mapping

### 📄 Document Management
- **File Uploads**: Ownership documents, site plans, zoning plans (PDF/JPG)
- **Image Gallery**: Up to 20 photos per property with primary image selection
- **Document Organization**: Organized by property type with easy retrieval
- **Re-Innovation Documentation**: Attach photos and documents to renovation/maintenance projects

### 🔍 Advanced Search & Filtering
- **Multi-Criteria Search**: Filter by region, governorate, zoning status
- **Dynamic Filtering**: Real-time search with instant results
- **Sorting Options**: Sort by code, name, area, building/land count
- **Pagination**: Efficient data browsing with customizable page sizes
- **Engineer-Specific Views**: Filter data by assigned engineer

### 🎨 User Interface
- **Responsive Design**: Mobile-first approach for field engineers using tablets/phones
- **Arabic/English Support**: Bilingual labels throughout the application
- **Interactive Components**: Collapsible cards, dynamic forms, modal dialogs
- **Orange Branding**: Orange corporate theme (#FF7900) with modern gradients
- **Dashboard Analytics**: Real-time statistics and KPIs

### 🔢 Auto-Code Generation
- **Site Codes**: `[Region][Governorate][Serial]` (e.g., `1AM001`)
- **Building Codes**: `[SiteCode][Sequence]` (e.g., `1AM00101`)
- **Smart Incrementing**: Automatic serial number assignment per governorate
- **Unique Identifiers**: System-wide unique tracking codes

---

## 🏗️ System Architecture

### Entity Relationship
```
Sites (الموقع)
  ├── Lands (الأراضي) [Many]
  │     ├── Directorate (المديرية)
  │     ├── Basin (الحوض)
  │     ├── Plot Number (رقم القطعة)
  │     ├── Area (المساحة)
  │     ├── Water Services (خدمات المياه) [Many]
  │     ├── Electricity Services (خدمات الكهرباء) [Many]
  │     ├── Re-Innovations (التجديد) [Polymorphic]
  │     └── Images (الصور) [Polymorphic]
  ├── Buildings (المباني) [Many]
  │     ├── Sequence Number
  │     ├── Floor Count
  │     ├── Building Type
  │     ├── Water Services (خدمات المياه) [Many]
  │     ├── Electricity Services (خدمات الكهرباء) [Many]
  │     ├── Re-Innovations (التجديد) [Polymorphic]
  │     └── Images (الصور) [Polymorphic]
  ├── Zoning Statuses (التنظيم) [Many-to-Many]
  ├── Re-Innovations (التجديد) [Polymorphic]
  └── Images (الصور) [Polymorphic]

Users (المستخدمين)
  ├── Super Admins (Full Access)
  └── Engineers (Field Access)
```

### Regional Structure
- **Region 1 (Capital)**: Amman (AM)
- **Region 2 (North)**: Irbid (IR), Mafraq (MF), Ajloun (AJ), Jerash (JA)
- **Region 3 (Middle)**: Balqa (BA), Zarqa (ZA), Madaba (MA)
- **Region 4 (South)**: Aqaba (AQ), Karak (KA), Tafileh (TF), Ma'an (MN)

---

## 🛠️ Requirements

### Server Requirements
- **PHP**: 8.2 or higher
- **Composer**: 2.x
- **Node.js**: 18.x or higher
- **NPM**: 9.x or higher
- **Database**: SQLite (default) or MySQL/PostgreSQL

### PHP Extensions
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo

---

## 📦 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/coding-academy-ojo/RealEstate_Management_Tool.git
cd RealEstate_Management_Tool
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install NPM Dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup
```bash
# Create SQLite database (default)
touch database/database.sqlite

# Or configure MySQL in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=realstate_db
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed
```

### 6. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server
```bash
# Laravel development server
php artisan serve

# Access at: http://localhost:8000
```

---

## 🗄️ Database Structure

### Core Tables

#### `users`
- `id` - Primary key
- `name` - User full name
- `email` - Email address (unique)
- `password` - Hashed password
- `role` - User role (super_admin, engineer)
- `is_active` - Account status
- `last_login` - Last login timestamp
- `timestamps`, `deleted_at`

#### `sites`
- `id` - Primary key
- `code` - Auto-generated (e.g., 1AM001)
- `cluster_no` - Cluster number
- `region` - Region number (1-4)
- `governorate` - Governorate code (AM, IR, etc.)
- `name` - Site name
- `area_m2` - Total area in square meters
- `zoning_status` - Legacy zoning field
- `notes` - Additional notes
- `serial_no` - Auto-incrementing serial per governorate
- `assigned_engineer_id` - Foreign key to users
- `timestamps`, `deleted_at`

#### `lands`
- `id` - Primary key
- `site_id` - Foreign key to sites
- `governorate` - Governorate code
- `directorate` - المديرية
- `directorate_number` - رقم المديرية
- `village` - القرية
- `village_number` - رقم القرية
- `basin` - الحوض
- `basin_number` - رقم الحوض
- `neighborhood` - الحي
- `neighborhood_number` - رقم الحي
- `plot_number` - رقم القطعة
- `plot_key` - مفتاح القطعة
- `area_m2` - المساحة
- `map_location` - Google Maps URL
- `latitude`, `longitude` - GPS coordinates
- `ownership_doc`, `site_plan`, `zoning_plan` - Document paths
- `timestamps`, `deleted_at`

#### `buildings`
- `id` - Primary key
- `site_id` - Foreign key to sites
- `code` - Auto-generated building code
- `sequence` - Building sequence number
- `floors` - Number of floors
- `building_type` - Type of building
- `timestamps`, `deleted_at`

#### `water_services`
- `id` - Primary key
- `serviceable_id` - Polymorphic ID (land/building)
- `serviceable_type` - Polymorphic type
- `provider` - Water service provider
- `meter_number` - Water meter number
- `account_number` - Account/subscription number
- `connection_date` - Service connection date
- `monthly_consumption` - Average monthly consumption (m³)
- `meter_location` - Physical meter location
- `status` - Service status (active/inactive)
- `notes` - Additional notes
- `timestamps`, `deleted_at`

#### `electricity_services`
- `id` - Primary key
- `serviceable_id` - Polymorphic ID (land/building)
- `serviceable_type` - Polymorphic type
- `provider` - Electricity provider
- `meter_number` - Electric meter number
- `account_number` - Account/subscription number
- `connection_date` - Service connection date
- `monthly_consumption` - Average monthly consumption (kWh)
- `capacity_kw` - Installed capacity
- `voltage` - Supply voltage
- `status` - Service status (active/inactive)
- `notes` - Additional notes
- `timestamps`, `deleted_at`

#### `re_innovations` (Renovations/Maintenance)
- `id` - Primary key
- `innovatable_id` - Polymorphic ID (site/land/building)
- `innovatable_type` - Polymorphic type
- `title` - Renovation/maintenance title
- `description` - Detailed description
- `type` - Type (maintenance, renovation, upgrade)
- `status` - Status (planned, in_progress, completed)
- `start_date` - Project start date
- `end_date` - Project completion date
- `budget` - Allocated budget
- `actual_cost` - Actual spending
- `contractor` - Contractor/vendor name
- `assigned_engineer_id` - Responsible engineer
- `timestamps`, `deleted_at`

#### `zoning_statuses`
- `id` - Primary key
- `name_ar` - Arabic name
- `is_active` - Active status
- `timestamps`, `deleted_at`

#### Pivot & Polymorphic Tables
- `site_zoning_status` - Many-to-many relationship
- `images` - Polymorphic for sites/lands/buildings (up to 20 per entity)
- `activity_logs` - Audit trail for all user actions

---

## 🎯 Usage

### Creating a New Site with Lands

1. **Navigate to Sites** → Click "Create New Site"
2. **Fill Site Information**:
   - Site Name (اسم الموقع)
   - Select Governorate (المحافظة) - auto-sets region
   - Enter Site Area (مساحة الموقع)
   - Select Zoning Statuses (التنظيم)
   - Add Notes (ملاحظات) - optional

3. **Add Lands** (Right Panel):
   - Click "+ Add Land" button
   - Fill required fields:
     - Directorate (المديرية) + Number
     - Basin (الحوض) + Number
     - Plot Number (رقم القطعة) + Key
     - Area (المساحة) - auto-updates site total
   - Optional fields:
     - Village, Neighborhood
     - Google Maps URL (auto-extracts coordinates)
     - Latitude/Longitude
   - Collapse/Expand cards to save space
   - Remove unwanted land cards

4. **Submit** → Site + All Lands created together

### Managing Zoning Statuses

- **Search**: Type to filter zoning options
- **Multi-Select**: Check multiple zoning statuses
- **Add New**: Click "+ Add New" to create custom status
- **Selected Items**: View selected items at the top with badges

### Automatic Features

✅ **Auto-Calculated**:
- Site total area from sum of land areas
- Region from governorate selection
- Serial numbers per governorate
- Site/Building code generation

✅ **Auto-Extracted**:
- GPS coordinates from Google Maps URLs
- Cluster numbers (next available)

---

## 📸 Screenshots

### Sites Dashboard
Multi-column view with filtering, search, and sorting capabilities.

### Create Site with Lands
Two-column layout: Site form (left) + Dynamic land cards (right).

### Zoning Status Selector
Interactive multi-select with search, badges, and custom status creation.

---

## 🔌 API Endpoints

### Public Endpoints

#### Get Next Cluster Number
```http
GET /sites/next-cluster/{governorate}

Response:
{
  "next_cluster": 5
}
```

#### Create Zoning Status
```http
POST /zoning-statuses
Content-Type: application/json

{
  "name_ar": "سكن تجاري"
}

Response:
{
  "id": 15,
  "name_ar": "سكن تجاري",
  "is_active": true
}
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter SiteTest

# With coverage
php artisan test --coverage
```

---

## 🚀 Deployment

### Production Checklist

```bash
# 1. Set environment to production
APP_ENV=production
APP_DEBUG=false

# 2. Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Build production assets
npm run build

# 4. Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 5. Enable maintenance mode during deployment
php artisan down
# ... deploy ...
php artisan up
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards
- Follow PSR-12 coding standards
- Write descriptive commit messages
- Add tests for new features
- Update documentation as needed

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👥 Authors

- **Coding Academy OJO** - [GitHub](https://github.com/coding-academy-ojo)

---

## 🙏 Acknowledgments

- Laravel Framework - [laravel.com](https://laravel.com)
- Bootstrap 5 - [getbootstrap.com](https://getbootstrap.com)
- TailwindCSS - [tailwindcss.com](https://tailwindcss.com)
- Bootstrap Icons - [icons.getbootstrap.com](https://icons.getbootstrap.com)

---

## 📞 Support

For support, email your-email@example.com or open an issue on GitHub.

---

## 🗺️ Roadmap

### Phase 1 - Core Features ✅
- [x] Multi-site management
- [x] Land registration system
- [x] Building inventory
- [x] Zoning status management
- [x] Auto-code generation
- [x] Google Maps integration

### Phase 2 - Utilities & Services ✅
- [x] Water services tracking
- [x] Electricity services monitoring
- [x] Maintenance & renovation (Re-Innovation) system

### Phase 3 - User Management (In Progress)
- [x] User authentication (Laravel Breeze)
- [ ] Role-based access control (Super Admin/Engineer)
- [ ] Engineer assignment to sites
- [ ] Activity logging and audit trails
- [ ] User permissions and capabilities

### Phase 4 - Reporting & Analytics (Planned)
- [ ] Dashboard with KPIs
- [ ] Utility consumption reports
- [ ] Re-Innovation (maintenance/renovation) cost analysis
- [ ] Site inventory reports
- [ ] Export to Excel/PDF
- [ ] Custom report builder

### Phase 5 - Advanced Features (Future)
- [ ] Email/SMS notifications
- [ ] Mobile application for engineers
- [ ] Integration with Orange systems
- [ ] Automated billing for utilities
- [ ] Predictive maintenance alerts (AI-powered)
- [ ] REST API for third-party integrations
- [ ] Automated backups and disaster recovery

---

## 🏢 About Orange Jordan

Orange Jordan is a leading telecommunications provider in Jordan, operating a nationwide network infrastructure. This Real Estate Management System helps Orange Jordan efficiently manage its extensive portfolio of sites, lands, and buildings across all 12 governorates.

**Key Benefits:**
- 📊 Centralized asset tracking
- 💰 Cost reduction through efficient utility monitoring
- 🔧 Proactive maintenance & renovation management
- 📱 Mobile access for field engineers
- 📈 Data-driven decision making
- 🔒 Secure role-based access

---

**Developed for Orange Jordan 🧡 | Made in Jordan 🇯🇴**
