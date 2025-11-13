<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/Vite-5.x-646CFF?style=flat&logo=vite" alt="Vite">
  <img src="https://img.shields.io/badge/Boosted%20by%20Orange-Brand-FF7900" alt="Boosted">
</p>

# Orange Real Estate Platform — Jordan

An enterprise-grade Laravel application purpose-built for **Orange Jordan** to manage the full life cycle of every company site, land parcel, building, utility connection, renovation project, and activity log across the Kingdom. From Amman to Aqaba, the platform gives Orange’s real estate, engineering, and operations teams a single source of truth for infrastructure, documentation, and compliance.

> **Latest Release Highlights (Q4 2025)**
>
> - Bulk electricity readings workspace with live service search, solar-aware forms, and batch submission
> - XLS-based water readings template with previous reading reference, “paid” defaults, and smarter error aggregation
> - Automated email reminder engine for rent renewals, water/electricity readings, and unpaid bills
> - Redesigned bulk utilities experience with Boosted modals, alert styling, and Arabic guidance
> - Activity audit helpers and consolidated logging utilities throughout the system

---

## 1. Why Orange Uses This Platform

- 🇯🇴 **Nationwide coverage**: All Orange properties in Jordan organized by region, governorate, directorate, basin, and plot
- 🧠 **Operational intelligence**: Consumption analytics, maintenance history, and billing status in one dashboard
- 🛡️ **Controlled access**: Privilege-based workflows for super admins, administrators, and specialized engineers
- ⚡ **Utilities in focus**: Real-time oversight of water and electricity services, including solar/net metering support
- 🧾 **Documentation-first**: Every permit, plan, photo, certificate, and invoice stored against its asset
- 🔁 **Process automation**: Scheduled reminders ensure rent, readings, and payments never slip

---

## 2. Feature Pillars

### 2.1 Property & Hierarchy Management
- Sites ➜ Lands ➜ Buildings hierarchy with automatic code generation (`1AM00301` style)
- Detailed cadastral data (directorate, basin, neighborhood, plot key) and Google Maps extraction
- Zoning catalogue with Arabic terminology and ad-hoc additions
- High-resolution image galleries, permit repositories, and activity logs per asset

### 2.2 Utilities & Consumption
- Water service registry with meter metadata, activation status, and historical readings
- Electricity services covering standard and solar/net metering operations
- XLS bulk import for water readings (active services only, previous reading column, “No” paid default)
- Interactive bulk electricity tool with live search, solar-only fields, Arabic hints, and batch confirmation
- Consumption recalculation engines (`WaterReadingManager`, `ElectricReadingManager`) keep analytics accurate

### 2.3 Finance & Rent Oversight
- Building contract data (value, frequency, renewals, payment history)
- Automated rent reminders fired 14 days before monthly/quarterly/semi-annual/annual dues
- CSV export tooling for hierarchical reporting (Sites ➜ Lands ➜ Buildings)

### 2.4 Maintenance & Re-Innovation
- Track preventive, corrective, and upgrade projects across any asset level
- Budget vs. actual tracking, contractor assignments, and progress documentation
- Multi-language guidance for engineers logging renovation events on-site

### 2.5 Automation & Notifications
- Daily scheduler (Asia/Amman) delivering:
  - Rent payment reminders to admins + engineers with real-estate privileges only
  - Water readings reminders on the 5th, unpaid bill alerts 5 days before month end (engineers with only water privilege)
  - Electricity readings reminders on the 5th, unpaid bill alerts 5 days before month end (engineers with only electricity privilege)
- Rich HTML mail templates reflecting Orange branding and bilingual cues

### 2.6 User Experience & Accessibility
- Boosted (Orange design system) + Bootstrap 5 interface optimized for tablets in the field
- Arabic/English labels, contextual hints, color-coded badges, and iconography for quick scanning
- High contrast modals with elevated z-index handling for conflict-free interactions

---

## 3. Installation & Environment

```bash
git clone https://github.com/coding-academy-ojo/RealEstate_Management_Tool.git
cd RealEstate_Management_Tool

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed   # optional seeders for demo data
npm run build                # or npm run dev during development

php artisan serve
# http://localhost:8000
```

Key stack requirements: PHP 8.2+, Composer 2, Node 18+, NPM 9+, SQLite/MySQL, and standard PHP extensions (OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo).

For schedules and mail:

```env
QUEUE_CONNECTION=database
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=orange-realestate@example.com
MAIL_PASSWORD=app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=realestate@orange.jo
MAIL_FROM_NAME="Orange Jordan Real Estate"
```

Windows task scheduler / Linux cron must run `php artisan schedule:run` every minute to dispatch reminders.

---

## 4. Daily Operations

| Workflow | Highlights |
| --- | --- |
| **Site intake** | Google Maps URL parsing ➜ coordinates extracted automatically; zoning tags selected or created inline |
| **Land/Building updates** | Auto-generated codes, document uploads (ownership, permits, plans), image galleries |
| **Water readings** | Download template ➜ fill with previous reading reference ➜ upload ➜ aggregated error reporting |
| **Electricity bulk** | Search by registration/meter/subscriber ➜ solar fields toggle ➜ add to batch ➜ Boosted confirmation modal |
| **Maintenance logging** | Modal workflows for disconnections, renovations, deactivations, and reactivations |
| **Email reminders** | Rent (14 days ahead), water readings (5th), water bills (month end -5), electricity equivalents |

---

## 5. System Map

```
Sites
├── Lands
│   ├── WaterServices ─┬─ WaterReadings
│   └── ElectricityServices ─┬─ ElectricReadings
├── Buildings
│   ├── WaterServices ─┴─ WaterReadings
│   └── ElectricityServices ─┴─ ElectricReadings
├── ReInnovations (polymorphic)
├── Documents & Galleries (polymorphic)
└── Activity Logs

Users
├── Super Admins (all privileges)
├── Admins (configurable privileges)
└── Engineers (water-only, electricity-only, or multi-privilege)
```

---

## 6. Testing & Quality

```bash
php artisan test            # feature + unit suites
php artisan test --filter ElectricityReadingBulkTest
php artisan lint            # if laravel pint/friendsofphp/php-cs-fixer configured
```

Automated calculations for consumption are covered by dedicated service tests. Bulk upload flows include validation coverage for required columns, date formats, and numeric ranges.

---

## 7. Roadmap Snapshot

- [x] Water XLS template with active-service filter and previous reading column
- [x] Electricity bulk entry page with solar awareness and Boosted modals
- [x] Email automation suite (rent, water, electricity)
- [x] Activity logging helpers and consolidated error messaging
- [ ] Hierarchy CSV export (Sites ➜ Lands ➜ Buildings) in progress
- [ ] Dashboard KPIs and Power BI connectors
- [ ] Mobile-first offline capture for remote governorates

---

## 8. About Orange Real Estate — Jordan

Orange Real Estate manages the telecom giant’s physical footprint across Jordan’s twelve governorates. This platform was engineered in Jordan 🇯🇴 for Orange Jordan 🧡 to deliver:

- Transparency over thousands of assets, ducts, towers, and operational sites
- Accurate utility billing and consumption oversight, including renewable energy tracking
- Fast coordination between headquarters in Amman and engineers in the field
- Compliance-ready documentation for regulators and municipal partners

**Developed with passion for Orange Jordan — boosting real estate operations nationwide.**

---

## 9. Support & Contribution

- Issues: [GitHub Issues](https://github.com/coding-academy-ojo/RealEstate_Management_Tool/issues)
- Enhancements: Submit PRs following PSR-12, with tests and English/Arabic copy updates where applicable
- Security: security@orange.jo

---

## 10. License

Released under the MIT License. See [LICENSE](LICENSE) for details.

---

<p align="center">Made in Jordan for Orange Jordan — Real Estate Operations, Simplified.</p>
