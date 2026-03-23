================================================================================
    EVENT MANAGEMENT SYSTEM - SETUP INSTRUCTIONS FOR XAMPP
================================================================================

This is a full-stack Event Management System built with PHP, MySQL (PDO), 
HTML, CSS, and JavaScript. The application features user authentication, 
event management, registration system, and an admin dashboard.

================================================================================
QUICK SETUP INSTRUCTIONS
================================================================================

1. INSTALL XAMPP
   - Download and install XAMPP from https://www.apachefriends.org/
   - Make sure Apache and MySQL services are running

2. COPY PROJECT FILES
   - Copy the 'event_manager' folder to: C:\xampp\htdocs\
   - Full path should be: C:\xampp\htdocs\event_manager

3. CREATE DATABASE
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Click on "Import" tab
   - Click "Choose File" and select: create_db.sql (in event_manager folder)
   - Click "Go" to import
   - This will create the database 'event_manager' with sample data

4. UPDATE DATABASE CONFIGURATION
   - If your MySQL password is not empty, edit: config.php
   - Change DB_PASS from '' to your MySQL password
   - Default settings work for standard XAMPP installation

5. SET PERMISSIONS (Important for file uploads)
   - The 'uploads' folder must be writable
   - Right-click on: C:\xampp\htdocs\event_manager\uploads
   - Go to Properties > Security > Edit
   - Make sure 'Users' have 'Modify' permissions

6. ACCESS THE APPLICATION
   - Open your browser and go to: http://localhost/event_manager
   - You should see the home page with sample events

================================================================================
DEFAULT LOGIN CREDENTIALS
================================================================================

The first user registered automatically becomes an admin.
For initial testing, use the sample admin account:

Email: admin@eventmanager.com
Password: admin123

Alternatively, register a new account - the first user will be admin.

================================================================================
FEATURES
================================================================================

PUBLIC PAGES:
- Home: Browse upcoming events with search and pagination
- Event Details: View event information and register
- Contact: Contact form for inquiries

AUTHENTICATION:
- User registration with validation
- Secure login with password hashing
- Session management
- Logout functionality

ADMIN DASHBOARD:
- Create new events
- Edit existing events
- Delete events
- Upload event images
- View all event registrations
- Filter registrations by event

EVENT REGISTRATION:
- Users can register for events
- Registration saves: name, email, phone
- Email validation prevents duplicate registrations
- Capacity tracking (if enabled)

================================================================================
FILE STRUCTURE
================================================================================

event_manager/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── app.js              # JavaScript functionality
├── dashboard/
│   ├── index.php               # Dashboard home
│   ├── create_event.php        # Create new event
│   ├── edit_event.php          # Edit existing event
│   ├── events.php              # List all events
│   └── registrations.php       # View registrations
├── includes/
│   ├── header.php              # Site header with navigation
│   └── footer.php              # Site footer
├── uploads/                    # Event images storage
├── auth.php                    # Authentication helpers
├── config.php                  # Configuration
├── contact.php                 # Contact page
├── create_db.sql               # Database schema and sample data
├── db_connect.php              # Database connection
├── event_details.php           # Event details page
├── index.php                   # Home page
├── login.php                   # User login
├── logout.php                  # Logout
├── profile.php                 # User profile
├── register.php                # User registration
└── README.txt                  # This file

================================================================================
SECURITY FEATURES
================================================================================

✓ Password hashing using password_hash()
✓ PDO prepared statements (SQL injection prevention)
✓ CSRF token protection on forms
✓ XSS prevention with htmlspecialchars()
✓ Session security settings
✓ File upload validation
✓ Server-side and client-side validation
✓ Secure file permissions

================================================================================
DEPLOYING TO SHARED HOSTING
================================================================================

1. UPLOAD FILES
   - Upload all files to your hosting root directory (usually public_html or www)
   - Or create a subdirectory like public_html/event_manager

2. CREATE DATABASE
   - Log into your hosting control panel (cPanel, etc.)
   - Create a new MySQL database and user
   - Note the database name, username, and password

3. UPDATE CONFIG.PHP
   - Edit config.php and update:
     - DB_HOST: your hosting database host (usually 'localhost')
     - DB_NAME: your database name
     - DB_USER: your database username
     - DB_PASS: your database password

4. IMPORT DATABASE
   - Use phpMyAdmin on your hosting to import create_db.sql
   - Or use command line: mysql -u username -p database < create_db.sql

5. SET PERMISSIONS
   - Make sure 'uploads' folder has write permissions (chmod 755 or 777)
   - Contact hosting support if needed

6. UPDATE URL
   - Edit config.php and update SITE_URL to your domain
   - Example: https://yourdomain.com/event_manager

7. TEST THE APPLICATION
   - Access your domain
   - Register a new account
   - Test all features

================================================================================
TROUBLESHOOTING
================================================================================

PROBLEM: Database connection error
SOLUTION: Check config.php settings, ensure MySQL is running

PROBLEM: Images not uploading
SOLUTION: Check uploads folder permissions, increase PHP upload limits

PROBLEM: Pages show errors
SOLUTION: Check error_reporting in config.php, verify all files uploaded

PROBLEM: CSRF token errors
SOLUTION: Clear browser cache, ensure session is started

PROBLEM: Registration not working
SOLUTION: Check database connection, verify table structure

PROBLEM: Mobile menu not working
SOLUTION: Clear browser cache, check JavaScript console for errors

================================================================================
TEST CHECKLIST
================================================================================

Before going live, test these features:

□ User registration
□ User login/logout
□ Browse events on home page
□ Search for events
□ View event details
□ Register for an event
□ Admin dashboard access
□ Create new event
□ Upload event image
□ Edit existing event
□ Delete event
□ View registrations
□ Filter registrations by event
□ Contact form
□ Responsive design on mobile devices
□ Pagination on event list

================================================================================
ADDITIONAL NOTES
================================================================================

- The first user registered becomes the admin (is_admin = 1)
- Images are stored in the 'uploads' folder
- Maximum file size: 5MB
- Allowed image formats: JPG, PNG, GIF
- Events are sorted by date (upcoming first)
- Past events are not shown on home page
- All passwords are securely hashed using PHP password_hash()

================================================================================
SUPPORT
================================================================================

For issues or questions:
- Check the troubleshooting section above
- Verify all files are in correct locations
- Check browser console for JavaScript errors
- Review PHP error logs
- Ensure PHP version is 7.0 or higher

================================================================================
LICENSE
================================================================================

This project is provided as-is for educational purposes.

================================================================================
END OF README
================================================================================

