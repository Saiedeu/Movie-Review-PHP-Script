msr-review-website/
├── 📁 config/
│   ├── 📄 config.php          # Database & site configuration
│   ├── 📄 database.php        # Database connection class
│   └── 📄 constants.php       # Site constants
├── 📁 includes/
│   ├── 📄 functions.php       # Helper functions
│   ├── 📄 auth.php           # Authentication functions
│   ├── 📄 seo.php            # SEO functions
│   ├── 📄 header.php         # Site header
│   └── 📄 footer.php         # Site footer
├── 📁 admin/
│   ├── 📄 index.php          # Admin dashboard
│   ├── 📄 login.php          # Admin login
│   ├── 📄 logout.php         # Admin logout
│   ├── 📁 includes/
│   │   ├── 📄 header.php     # Admin header
│   │   └── 📄 footer.php     # Admin footer
│   ├── 📁 reviews/
│   │   ├── 📄 index.php      # All reviews management
│   │   ├── 📄 add.php        # Add new review
│   │   ├── 📄 edit.php       # Edit review
│   │   ├── 📄 delete.php     # Delete review
│   │   └── 📄 pending.php    # User submitted reviews
│   ├── 📁 categories/
│   │   ├── 📄 index.php      # Categories management
│   │   ├── 📄 add.php        # Add category
│   │   ├── 📄 edit.php       # Edit category
│   │   └── 📄 delete.php     # Delete category
│   ├── 📁 users/
│   │   ├── 📄 index.php      # User management
│   │   ├── 📄 add.php        # Add user
│   │   ├── 📄 edit.php       # Edit user
│   │   └── 📄 delete.php     # Delete user
│   └── 📁 settings/
│       ├── 📄 site.php       # Site settings
│       └── 📄 seo.php        # SEO settings
├── 📁 assets/
│   ├── 📁 css/
│   │   ├── 📄 style.css      # Custom styles
│   │   └── 📄 admin.css      # Admin styles
│   ├── 📁 js/
│   │   ├── 📄 script.js      # Main JavaScript
│   │   └── 📄 admin.js       # Admin JavaScript
│   ├── 📁 images/
│   │   ├── 📄 logo.png       # Default logo
│   │   ├── 📄 favicon.ico    # Default favicon
│   │   └── 📄 placeholder.jpg # Placeholder image
│   └── 📁 uploads/
│       ├── 📁 logo/          # Site logos
│       ├── 📁 seo/           # SEO images, favicons
│       └── 📁 reviews/       # Review feature images
├── 📁 api/
│   ├── 📄 submit-review.php  # Handle review submissions
│   └── 📄 contact.php        # Handle contact form
├── 📄 index.php              # Homepage
├── 📄 reviews.php            # All reviews page
├── 📄 review.php             # Single review page
├── 📄 category.php           # Category page
├── 📄 categories.php         # All categories page
├── 📄 submit-review.php      # Submit review form
├── 📄 about.php              # About page
├── 📄 contact.php            # Contact page
├── 📄 search.php             # Search results
├── 📄 404.php                # 404 error page
├── 📄 .htaccess              # URL rewriting
└── 📁 sql/
    └── 📄 msr_database.sql   # Database schema