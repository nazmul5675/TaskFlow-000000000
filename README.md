# TaskFlow — Production Ready Task Manager

TaskFlow is a sleek, portfolio-quality PHP & MySQL task management application designed for productivity and security. It features a modern, responsive UI built with Tailwind CSS and is fully optimized for deployment on platforms like InfinityFree and Railway.

## 🚀 Key Features

- **Secure Authentication**: User registration and login with Bcrypt password hashing and session fixation protection.
- **Full CRUD Operations**: Create, view, edit, and delete tasks with ease.
- **Smart Filtering**: Search and filter tasks by status, priority, and due date.
- **Security-First Design**:
    - **CSRF Protection**: Comprehensive protection on all state-changing forms using secure tokens.
    - **POST-Only Actions**: Critical actions like status updates and deletions are restricted to POST requests with validation.
    - **Environment-Aware**: Configured to use server environment variables for database credentials, with a local fallback for easy development.
- **Responsive UI**: Beautiful, mobile-friendly design using Tailwind CSS and Inter typography.
- **Interactive UX**: Subtle animations, flash messages, and confirmation dialogs for a premium feel.

## 🛠️ Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: Tailwind CSS v3, Vanilla JavaScript
- **Fonts**: Inter (via Google Fonts)

## 💻 Local Setup (XAMPP)

1. **Clone the repository** to your local environment.
2. **Setup Database**:
   - Open PHPMyAdmin and create a database named `taskflow_db`.
   - Import the provided `database.sql` file.
3. **Configure Environment**:
   - Copy `config/.env.php.example` to `config/.env.php`.
   - Update the database credentials in `config/.env.php` to match your local setup.
4. **Run the App**:
   - Access the application via `http://localhost/TaskFlow`.

## ☁️ Deployment

### InfinityFree / Shared Hosting
1. Upload all project files via FTP to the `htdocs` or `public_html` directory.
2. Create a MySQL database and user via the control panel.
3. Import `database.sql` into your remote database.
4. Correct the credentials in `config/.env.php` on the server.

