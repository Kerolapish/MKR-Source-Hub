# MKR Source Hub

An agritech sourcing and contractor management system for **MKR Hartamas Sdn. Bhd.** It provides a central dashboard for tracking hardware inventory, suppliers, contractors, and project tenders.

## Features

- **Operations Dashboard** — key metrics (hardware SKUs, active contractors), recent hardware additions, and a catalog breakdown by category.
- **Hardware Catalog** (`sourcing.php`) — searchable, filterable item registry with create/edit/delete, categorized by Sensor, Microcontroller, Fertigation, Network, and Machinery.
- **Contractor Registry** (`contractors.php`) — manage registered contractors with search, rating filters, and full CRUD.

## Tech Stack

- **PHP** (procedural + lightweight MVC-style controllers)
- **MySQL / MariaDB** (via PDO)
- **Tailwind CSS** (utility styling)
- **Lucide** icons
- Runs on **XAMPP** (Apache + MySQL)

## Project Structure

```
MKR-Source-Hub/
├── assets/                 # CSS, images, logos
├── config/
│   └── database.php        # PDO connection settings
├── controllers/
│   ├── ContractorController.php
│   ├── DashboardController.php
│   └── SourcingController.php
├── includes/
│   ├── header.php
│   └── footer.php
├── sql/
│   └── schema.sql          # Database schema + seed data
├── index.php               # Dashboard
├── contractors.php         # Contractor registry
└── sourcing.php            # Hardware catalog
```

## Setup

1. **Clone into your web root** (e.g. XAMPP `htdocs`):
   ```bash
   git clone https://github.com/Kerolapish/MKR-Source-Hub.git
   ```

2. **Start Apache and MySQL** from the XAMPP Control Panel.

3. **Import the database** — open phpMyAdmin (http://localhost/phpmyadmin) and import `sql/schema.sql`. This creates the `mkr_source_hub` database along with tables and seed data.

4. **Configure the connection** if needed — update credentials in `config/database.php`:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_PORT', '3306');
   define('DB_NAME', 'mkr_source_hub');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

5. **Open the app** at http://localhost/MKR-Source-Hub/

## Database Schema

The schema includes: `users`, `suppliers`, `hardware_items`, `contractors`, `projects`, and `contractor_projects` (a junction table linking contractors to projects). See `sql/schema.sql` for full definitions and seed data.

## License

Internal project for MKR Hartamas Sdn. Bhd.
