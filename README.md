# Tuition Class Management System

**[🟢 Live Demo](https://tuitionclass.free.nf/auth/login.php)**

A modern, responsive, and easy-to-use Tuition Class Management System built with PHP, MySQL, HTML, CSS, and Vanilla JavaScript. It features a premium SaaS dashboard aesthetic, suitable for managing students, classes, attendance, and payments.

## Features

- **Authentication**: Secure admin login system using password hashes and session management.
- **SaaS Dashboard**: A visually appealing dashboard with summary cards for total students, total classes, monthly revenue, attendance analytics, and a dynamic Chart.js income graph.
- **Student Management**: Full CRUD operations for managing student profiles with a clean list, search, and pagination.
- **Class Management**: Easily add and organize tuition classes, keeping track of active enrollments.
- **Attendance Tracking**: Rapid attendance marking UI, allowing you to quickly set students to Present or Absent with visual indicators, plus an attendance history summary.
- **Fee Management**: Process student payments associated with a specific month/year and track historical income.
- **Reports & Analytics**: Downloadable CSV reports of both student data and payment history directly from the reports tab.
- **Premium UI**: Soft color palette (white, blue, light purple), nice drop shadows, Google Fonts (Inter), smooth hover transitions, and Lucide icons.

## Technology Stack

- **Frontend**: HTML5, Vanilla Custom CSS (using root variables and responsive grids), Vanilla JavaScript.
- **Backend**: Native PHP (using PDO with prepared statements for utmost security).
- **Database**: MySQL relational schema.
- **External Libraries**: [Chart.js](https://www.chartjs.org/) for analytics, [Lucide](https://lucide.dev/) for modern icons.

## Installation & Setup

1. **Clone or Download the Repository**.
2. **Move to Server Directory**: Place the project folder into your local web server root (e.g., `htdocs` for XAMPP, `www` for WAMP).
3. **Database Configuration**:
   - Open phpMyAdmin.
   - Run the provided `database.sql` script to create the `tuition_management` database and required tables.
   - If your local database uses a different user or password than `root` (with no password), update the connection parameters in `/config/db.php`.
4. **Access the System**:
   - Navigate to `http://localhost/your_folder_name/` in your browser. The root `index.php` will automatically re-route you to the login screen.

## Default Credentials

A default admin account is seeded upon database creation:
- **Username**: `admin`
- **Password**: `password123`

*(It is highly recommended to change or add new admin accounts for production environments.)*

## Folder Structure Summary

- `/assets`: Contains custom CSS (`style.css`), JavaScript (`main.js`), and image assets.
- `/config`: Configuration files, such as `db.php` for database connection.
- `/auth`: Contains login, logout, and authentication logic.
- `/includes`: Reusable components like `header.php` and `footer.php`.
- `/dashboard`: Main dashboard views and logic.
- `/students`: Student management module.
- `/classes`: Class tracking module.
- `/attendance`: Daily attendance recording module.
- `/payments`: Financial management and payment recordings.
- `/reports`: Export logic for student data and financials.

## License

This project is open source and available for personal portfolio and educational use.
