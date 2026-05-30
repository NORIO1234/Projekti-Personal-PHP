# Barber Shop Management System

A lightweight appointment management system built with PHP, MySQL, and Bootstrap, featuring user profiles, role-based access control, and admin management tools.

## Features

- **Secure Authentication**
  - User registration and login
  - Password hashing with bcrypt
  - Session management

- **Appointment Management**
  - Create, edit, and delete appointments
  - Search and filter by status
  - View appointment history
  - Real-time status updates (Pending/Completed)

- **User Profiles**
  - View and edit account information
  - Change password
  - View recent appointment history
  - Account creation date tracking

- **Admin Panel (Role-Based Access)**
  - User management and role assignment
  - Search users by name or email
  - Promote/demote users to admin
  - System statistics (total users, appointments)
  - Admin badge in navigation

- **Dashboard**
  - Summary cards: Total, Pending, Completed, Today appointments
  - Quick filters and search
  - User count statistics (admin only)
  - Empty state messaging

- **UI/UX**
  - Responsive Bootstrap 5 design
  - Bootstrap Icons for visual clarity
  - Flash notifications (success/error)
  - Form value preservation on validation errors
  - Top navbar with user greeting
  - Sidebar navigation with role-based menu items

## Project structure

- `index.php` - Landing page
- `login.php` - User login
- `register.php` - User registration
- `dashboard.php` - Appointment dashboard
- `profile.php` - User profile and appointment history
- `appointments/add.php` - Add appointment
- `appointments/edit.php` - Edit appointment
- `appointments/delete.php` - Delete appointment
- `admin/users.php` - User management (admin only)
- `includes/config.php` - Database connection, helpers, and functions
- `includes/header.php` - Shared navigation and header
- `includes/footer.php` - Shared footer
- `includes/auth.php` - Authentication check
- `css/style.css` - Custom styling
- `logout.php` - Logout handler

## Setup

1. Install XAMPP or another local PHP/MySQL environment.
2. Place the project in your web root (`htdocs` for XAMPP).
3. Start Apache and MySQL services.
4. Open `http://localhost/Projekti-Personal-PHP` in your browser.
5. Register a new account and start managing appointments.

## Default Admin Setup

Currently, all new users are created as regular users. To create an admin:

1. Register an account normally.
2. Access your database and run:
   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
   ```
3. Log in and access the "Manage Users" admin panel from the sidebar.

## Key Functions

- `sanitize()` - HTML-escape output
- `setFlash()` - Set session flash messages
- `displayFlash()` - Display flash messages
- `getCurrentUser()` - Get current logged-in user
- `isAdmin()` - Check if user is admin

## Notes

- Tables are auto-created on first run.
- Passwords are hashed with `PASSWORD_DEFAULT` (bcrypt).
- All user inputs are sanitized and validated.
- Prepared statements protect against SQL injection.

## Future Enhancements

- Email notifications for appointments
- SMS reminders
- Google Calendar integration
- Multi-language support
- Dark mode theme
