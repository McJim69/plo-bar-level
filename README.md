# PLO BSK-Level Database System

An intranet web application designed to manage, organize, and track Sangguniang Kabataan (SK) and Barangay data, including leaders and household members.

## Features

- **Voter Management:** Manage registered voters data.
- **SK Hierarchy Tracking:** Track and assign hierarchies including:
  - SK Chairman
  - SK Kagawad
  - Purok Leaders
  - Household Leaders
  - Household Members
- **Dynamic Reports:** Generate "80% ETO" reports, Exit Poll Counters, and detailed Grid/Form prints.
- **Modern UI:** Features a modern glassmorphism design, responsive layouts, and a built-in Dark Mode.
- **SuperAdmin Panel:** User management, automated backups, and one-click database switching (BSK vs Regular voters).

## Tech Stack

- **Backend:** PHP 8.0+, MySQLi
- **Frontend:** HTML5, CSS3 (Custom Glassmorphism), Bootstrap 5, FontAwesome 6, jQuery
- **UI Components:** Facebox, Fancybox

## Quick Start

1. **Prerequisites:** 
   - A local server environment (XAMPP, WAMP, Laragon, etc.) running PHP 8.0 or higher.
   - MySQL/MariaDB database.

2. **Database Setup:**
   - Import the required SQL dump into your MySQL server.
   - Ensure the database name matches the one specified in the connection string.

3. **Configuration:**
   - Open `connect.php`.
   - Update the database credentials (`$hostname`, `$username`, `$password`, `$database`) to match your local environment.

4. **Run:**
   - Place the project folder (`plo-bsk-level`) in your server's web root (e.g., `htdocs` or `www`).
   - Access the system via `http://localhost/plo-bsk-level`.
   - Login using your assigned credentials.

## License
Proprietary software. Internal use only.
