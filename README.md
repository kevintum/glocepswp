# GLOCEPS WordPress Website

A premium WordPress theme and website for the **Global Centre for Policy and Strategy (GLOCEPS)** - a leading think tank focused on policy influence and strategy formulation, advancing peace, security, and development in Eastern Africa.

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Project Structure](#project-structure)
- [Custom Post Types](#custom-post-types)
- [Theme Features](#theme-features)
- [Deployment](#deployment)
- [Development](#development)
- [Contributing](#contributing)

## 🎯 Overview

GLOCEPS is a comprehensive WordPress website designed to showcase research, publications, events, and policy insights. The site features:

- **Custom Content Management**: Multiple custom post types for different content types
- **E-Commerce Integration**: WooCommerce for selling premium publications
- **Flexible Page Builder**: ACF Flexible Content blocks for dynamic page creation
- **Advanced Search**: Enhanced search functionality across all content types
- **Responsive Design**: Mobile-first, fully responsive design
- **Performance Optimized**: Optimized for speed and SEO

## ✨ Features

### Content Management
- **8 Custom Post Types**: Publications, Events, Team Members, Videos, Podcasts, Galleries, Articles, and Speeches
- **Research Pillars**: Taxonomy-based organization of research areas
- **ACF Flexible Content**: Drag-and-drop page builder with reusable content blocks
- **Media Management**: Custom image sizes and optimized media handling

### E-Commerce
- **WooCommerce Integration**: Full e-commerce functionality for publication sales
- **Payment Gateways**: 
  - Pesapal (M-Pesa integration for Kenyan market)
  - PayPal (International payments)
- **Custom Cart & Checkout**: Customized shopping experience
- **Order Management**: Custom order receipt and resend functionality

### User Experience
- **Advanced Navigation**: Multi-level dropdown menus with hover delays
- **Search Enhancement**: Search across all post types with filtering
- **Pagination**: Custom pagination for all archive pages
- **Responsive Design**: Mobile-first approach with breakpoint optimization
- **Accessibility**: WCAG-compliant markup and semantic HTML

### Performance & SEO
- **Optimized Assets**: Minified CSS/JS, optimized images
- **SEO-Friendly**: Proper meta tags, structured data, clean URLs
- **Caching Ready**: Compatible with WordPress caching plugins
- **Font Optimization**: Self-hosted Effra font with fallbacks

## 🛠 Technology Stack

### Core
- **WordPress**: 6.4+ (Tested up to 6.9)
- **PHP**: 8.0+ (Recommended: 8.3+)
- **MySQL**: 5.5.5+ (Recommended: 8.0+ or MariaDB 10.6+)

### Required Plugins
- **Advanced Custom Fields Pro** (6.7.0+): Custom fields and flexible content
- **WooCommerce** (10.4.2+): E-commerce functionality
- **Contact Form 7**: Contact form handling
- **WP Mail SMTP**: Email delivery configuration

### Payment Gateways
- **Pesapal WooCommerce Plugin**: M-Pesa and Pesapal payments
- **WooCommerce PayPal**: PayPal integration

### Optional Plugins
- **Duplicator**: Site migration and backups
- **WP Mail SMTP**: Email delivery (recommended)

### Theme Technologies
- **CSS**: Custom CSS with CSS Variables for theming
- **JavaScript**: Vanilla JS (no jQuery dependency for core functionality)
- **Fonts**: 
  - Effra (Self-hosted, primary font)
  - DM Sans (Google Fonts, fallback)
  - Fraunces (Google Fonts, decorative)

## 📦 Requirements

### Server Requirements
- PHP 8.0 or higher (8.3+ recommended)
- MySQL 5.5.5+ or MariaDB 10.6+
- Apache with mod_rewrite or Nginx
- HTTPS support (recommended)
- PHP extensions: `mysqli`, `gd`, `mbstring`, `xml`, `curl`, `zip`

### WordPress Requirements
- WordPress 6.4 or higher
- ACF Pro plugin (required)
- WooCommerce plugin (required for e-commerce features)

## 🚀 Installation

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/kevintum/glocepswp.git
   cd glocepswp
   ```

2. **Set up WordPress**
   - If using Local by Flywheel, create a new site and point it to the `app/public` directory
   - Or set up a local LAMP/MAMP/WAMP stack

3. **Configure `wp-config.php`**
   - Copy `wp-config-sample.php` to `wp-config.php`
   - Update database credentials for your local environment
   - The theme includes environment detection (local/staging/production)

4. **Install WordPress**
   - Navigate to your local site URL
   - Follow WordPress installation wizard
   - Note: `wp-config.php` is excluded from Git for security

5. **Install Required Plugins**
   - ACF Pro (must be installed manually - not available on WordPress.org)
   - WooCommerce (install from WordPress admin)
   - Contact Form 7 (install from WordPress admin)
   - WP Mail SMTP (install from WordPress admin)
   - Pesapal WooCommerce Plugin (install from WordPress admin or upload)

6. **Activate Theme**
   - Go to **Appearance** → **Themes**
   - Activate **GLOCEPS Theme**

7. **Run Theme Setup** (if applicable)
   - The theme includes auto-setup functionality via `mu-plugins/gloceps-setup.php`
   - Essential pages and ACF blocks will be created automatically

8. **Import Demo Data** (optional)
   - Demo data functions are available in `inc/demo-data.php`
   - Can be triggered programmatically if needed

### Database Setup

The theme expects the following database structure:
- Standard WordPress tables
- ACF field groups and options
- WooCommerce tables (if WooCommerce is active)
- Custom post type tables (created automatically)

## 📁 Project Structure

```
app/public/
├── wp-content/
│   ├── themes/
│   │   └── gloceps-theme/          # Main theme directory
│   │       ├── assets/
│   │       │   ├── css/            # Stylesheets
│   │       │   ├── js/             # JavaScript files
│   │       │   ├── fonts/          # Self-hosted fonts (Effra)
│   │       │   └── images/         # Theme images
│   │       ├── inc/                # PHP includes
│   │       │   ├── acf-fields.php  # ACF field definitions
│   │       │   ├── template-functions.php
│   │       │   ├── template-tags.php
│   │       │   ├── nav-walkers.php
│   │       │   ├── woocommerce-functions.php
│   │       │   └── newsletter-subscriptions.php
│   │       ├── template-parts/     # Reusable template parts
│   │       │   ├── blocks/         # ACF flexible content blocks
│   │       │   └── components/     # Card components, etc.
│   │       ├── woocommerce/        # WooCommerce template overrides
│   │       ├── archive-*.php       # Archive templates
│   │       ├── single-*.php        # Single post templates
│   │       ├── page-*.php          # Custom page templates
│   │       ├── functions.php       # Main theme functions
│   │       └── style.css           # Theme header
│   ├── plugins/                    # WordPress plugins (included in Git)
│   └── mu-plugins/                 # Must-use plugins
│       └── gloceps-setup.php       # Auto-setup functionality
├── wp-admin/                       # WordPress admin (core)
├── wp-includes/                    # WordPress core files
├── .gitignore                      # Git ignore rules
├── wp-config.php                   # WordPress config (NOT in Git)
└── README.md                       # This file
```

## 📝 Custom Post Types

The theme registers the following custom post types:

### 1. **Publications** (`publication`)
- Archive: `/publications/`
- Single: `/publications/{slug}/`
- Fields: Publication date, author, file, price, excerpt
- WooCommerce integration for sales

### 2. **Events** (`event`)
- Archive: `/events/`
- Single: `/events/{slug}/`
- Fields: Event date, location, registration link, featured status

### 3. **Team Members** (`team_member`)
- Archive: `/team/`
- Single: `/team/{slug}/`
- Fields: Position, bio, social links, expertise areas

### 4. **Videos** (`video`)
- Archive: `/videos/`
- Single: Redirects to archive (not directly accessible)
- Fields: Video embed URL, duration, featured status

### 5. **Podcasts** (`podcast`)
- Archive: `/podcasts/`
- Single: Redirects to archive (not directly accessible)
- Fields: Source type (embed/external/file), episode number, duration
- Modal popup for embedded content

### 6. **Articles** (`article`)
- Archive: `/articles/`
- Single: `/articles/{slug}/`
- Fields: Publication date, author, featured image, excerpt
- Related articles functionality

### 7. **Galleries** (`gallery`)
- Archive: `/galleries/`
- Single: `/galleries/{slug}/`
- Fields: Gallery images, description, featured status

### 8. **Speeches** (`speech`)
- Archive: `/speeches/`
- Single: Redirects to archive (not directly accessible)
- Fields: Speech date, downloadable file, description

## 🎨 Theme Features

### ACF Flexible Content Blocks

The theme includes numerous reusable content blocks:

- **Hero Sections**: Split hero, full-width hero, video hero
- **Content Blocks**: Content with image, two-column content, call-to-action
- **Grids**: Team grid, publication grid, event grid, card grid
- **Sections**: FAQ section, testimonial section, stats section
- **Special**: Research pillars, page headers, contact forms

### Navigation System

- **Primary Navigation**: Multi-level dropdown menus
- **Mobile Navigation**: Responsive mobile menu
- **Footer Navigation**: Multiple footer menu areas
- **Breadcrumbs**: Automatic breadcrumb generation
- **Hover Delays**: Improved UX with hover delays on dropdowns

### Search & Filtering

- **Enhanced Search**: Searches across all post types
- **Archive Filtering**: Filter by taxonomy, date, etc.
- **Pagination**: Custom pagination for all archives
- **404 Handling**: Custom 404 page with search

### WooCommerce Integration

- **Custom Templates**: Overridden WooCommerce templates
- **Custom Cart Page**: Custom cart experience
- **Order Receipt**: Custom order completion page
- **Resend Publications**: Functionality to resend purchased publications
- **Payment Gateways**: Pesapal and PayPal integration

### Theme Settings (ACF Options Pages)

Accessible via **Theme Settings** in WordPress admin:

- **General Settings**: Site-wide settings, contact info, social media
- **Archive Options**: Settings for each post type archive
- **Text Truncation**: Control title/description truncation
- **Font Settings**: Effra font activation
- **WooCommerce Settings**: E-commerce specific settings

## 🚢 Deployment

### Environment Configuration

The theme supports three environments:
- **Local**: `gloceps.local` or `localhost`
- **Staging**: Cloudways staging URL
- **Production**: `gloceps.org`

Environment detection is handled automatically via `wp-config.php`.

### Deployment to Staging (Cloudways)

1. **Push to GitHub**
   ```bash
   git add .
   git commit -m "Your commit message"
   git push origin main
   ```

2. **Pull on Cloudways**
   - SSH into Cloudways server
   - Navigate to WordPress root
   - Pull latest changes: `git pull origin main`

3. **Update `wp-config.php`** (if needed)
   - `wp-config.php` is excluded from Git
   - Ensure staging credentials are set correctly
   - See `DEPLOYMENT.md` for detailed instructions

4. **Sync Database** (if needed)
   - Export from local
   - Import to staging
   - Run search-replace for URLs

### Production Deployment

See `DEPLOYMENT.md` for detailed production deployment instructions.

### Important Notes

- **Never commit `wp-config.php`** - It contains sensitive credentials
- **Media files** (`wp-content/uploads/`) are excluded from Git
- **Plugin build files** are included in Git for easier deployment
- Always backup before deploying

## 💻 Development

### Local Development

1. **Set up Local Environment**
   - Use Local by Flywheel or similar
   - Point to `app/public` directory
   - Configure database credentials

2. **Enable Debug Mode** (in `wp-config.php`)
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

3. **Watch for Changes**
   - CSS/JS changes require browser refresh
   - PHP changes are immediate
   - Clear cache if using caching plugins

### Code Standards

- **PHP**: Follow WordPress Coding Standards
- **CSS**: Use BEM methodology where applicable
- **JavaScript**: ES6+ syntax, no jQuery for new code
- **File Naming**: kebab-case for files, snake_case for functions

### Customization

#### Adding a New Content Block

1. Define fields in `inc/acf-fields.php`
2. Create template in `template-parts/blocks/block-{name}.php`
3. Register in ACF field group

#### Adding a New Custom Post Type

1. Register in `functions.php` → `gloceps_register_post_types()`
2. Add ACF fields in `inc/acf-fields.php`
3. Create archive template: `archive-{post-type}.php`
4. Create single template: `single-{post-type}.php` (if needed)
5. Add pagination support in `gloceps_fix_cpt_archive_pagination()`

#### Modifying Styles

- Main stylesheet: `assets/css/styles.css`
- Font overrides: `assets/css/effra-overrides.css`
- WooCommerce styles: `assets/css/woocommerce.css`

### Git Workflow

1. **Create Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make Changes & Commit**
   ```bash
   git add .
   git commit -m "Description of changes"
   ```

3. **Push & Create Pull Request**
   ```bash
   git push origin feature/your-feature-name
   ```

4. **Merge to Main**
   - After review, merge to `main`
   - Deploy to staging for testing

## 🔧 Troubleshooting

### Common Issues

#### Pagination Not Working
- Ensure rewrite rules are flushed: **Settings** → **Permalinks** → **Save Changes**
- Check that `gloceps_fix_cpt_archive_pagination()` includes your post type

#### ACF Fields Not Showing
- Ensure ACF Pro is installed and activated
- Check field group location rules
- Verify field group is assigned to correct post type/page template

#### WooCommerce Issues
- Ensure WooCommerce is installed and activated
- Check payment gateway configuration
- Verify WooCommerce pages are set: **WooCommerce** → **Settings** → **Advanced**

#### Navigation Dropdowns Not Working
- Clear browser cache
- Check JavaScript console for errors
- Verify `main.js` is enqueued correctly

#### Styling Issues
- Clear browser cache
- Check if Effra font is activated in theme settings
- Verify CSS file is loading: Check Network tab in DevTools

### Debug Mode

Enable WordPress debug mode in `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Logs will be written to `wp-content/debug.log`.

## 📚 Additional Documentation

- **Deployment Guide**: See `DEPLOYMENT.md` for detailed deployment instructions
- **WordPress Codex**: https://codex.wordpress.org/
- **ACF Documentation**: https://www.advancedcustomfields.com/resources/
- **WooCommerce Documentation**: https://woocommerce.com/documentation/

## 👥 Contributing

This is a private project for GLOCEPS. For contributions:

1. Create a feature branch
2. Make your changes
3. Test thoroughly
4. Submit for review
5. Deploy to staging for client approval

## 📄 License

This theme is proprietary software developed for GLOCEPS. All rights reserved.

WordPress is licensed under GPL v2 or later.

## 📞 Support

For issues or questions:
- **Developer**: Kevin Tum (https://www.kevintum.com/)
- **Client**: GLOCEPS (https://gloceps.org)

## 🗺 Roadmap

### Completed
- ✅ Custom post types implementation
- ✅ WooCommerce integration
- ✅ ACF Flexible Content blocks
- ✅ Responsive design
- ✅ Payment gateway integration
- ✅ Archive pagination fixes
- ✅ Navigation enhancements
- ✅ Search functionality

### Future Enhancements
- [ ] Multi-language support (WPML/Polylang)
- [ ] Advanced analytics integration
- [ ] Newsletter subscription system enhancement
- [ ] Member portal (if needed)
- [ ] API endpoints for external integrations

---

**Last Updated**: December 2024  
**Version**: 1.0.0  
**WordPress Version**: 6.4+  
**PHP Version**: 8.0+
