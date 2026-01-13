# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Budget management module
- Recurring transactions
- Multi-user/team support
- Data import from CSV/Excel
- Mobile app (React Native)
- Bank account integration
- Advanced reporting with PDF export
- Multi-language support

---

## [1.0.0] - 2026-01-08

### 🎉 Initial Release

First stable release of Expense Manager - a modern, open-source personal finance management application.

### Added

#### 💰 Income Management
- Record and track all income sources
- Categorize income with custom categories
- Attach receipts and invoices (PDF, images)
- Search and filter by date, category, reference
- Export income data to Excel

#### 💸 Expense Management
- Track expenses with detailed information
- Hierarchical expense categories (parent/child)
- Multiple payment methods (Cash, Card, Bank)
- File attachments for receipts
- Advanced filtering and search
- Export expense data to Excel

#### 📊 Dashboard & Reports
- Financial overview dashboard
- Income vs Expense summary cards
- Monthly/yearly statistics
- Category-wise breakdown
- Visual charts and graphs (ApexCharts)
- Balance tracking

#### 👤 User Management
- Secure authentication system
- User registration with email
- User profile management
- Custom avatar upload with image resizing
- Custom banner upload
- Password reset functionality
- Remember me option
- Session management
- Last login tracking
- Registration IP logging

#### ⚙️ Settings & Customization
- Multi-currency support (50+ currencies)
- Customizable currency formatting
- Date and time format preferences
- Company/business branding
- Timezone configuration
- Database backup/export

#### 🎨 Modern UI/UX
- Clean, responsive Bootstrap 5 design
- Dark/Light theme toggle
- Mobile-friendly interface
- AJAX-powered interactions (PJAX)
- Toast notifications (NEM Toast System)
- Modal-based forms
- Bootstrap Icons

#### 🛠 Developer Features
- Database seeder for demo data (`php yii seed/demo`)
- Comprehensive database migrations
- PSR-4 autoloading
- Modular widget architecture
- Multi-language support (i18n ready)
- Clean URL routing

### Technical Stack
- **Framework**: Yii2 (v2.0.53)
- **PHP**: 8.1+ required
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: Bootstrap 5.3
- **Icons**: Bootstrap Icons
- **Charts**: ApexCharts

### Security
- CSRF protection on all forms
- Password hashing (bcrypt)
- SQL injection prevention (prepared statements)
- XSS protection (output encoding)
- Secure session handling
- Cookie validation

---

## Version History

| Version | Date | Description |
|---------|------|-------------|
| 1.0.0 | 2026-01-08 | Initial stable release |

---

## Upgrade Guide

### Fresh Installation

1. Clone or download the repository
2. Run `composer install`
3. Configure `config/db.php`
4. Run `php yii migrate`
5. (Optional) Run `php yii seed/demo` for demo data

---

[Unreleased]: https://github.com/mohsin-rafique/expense-manager/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/mohsin-rafique/expense-manager/releases/tag/v1.0.0
