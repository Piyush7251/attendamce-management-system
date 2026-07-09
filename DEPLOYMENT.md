# Deployment Guide - Attendance Management System

This document outlines how to prepare, push, and deploy this project to production.

---

## ⚠️ Important Note: GitHub Pages Limitation
**GitHub Pages only hosts static content** (HTML, CSS, and Client-Side Javascript). It does not support server-side engines like PHP or databases like MySQL. 

If you attempt to deploy this project directly to GitHub Pages:
1.  **It will not execute PHP:** Server-side scripts will fail to run.
2.  **Security Risk:** Visitors can inspect and download the raw `.php` database access and configurations files in plain text, exposing internal credentials.

To deploy this project to production, you must use a hosting service that supports **PHP 8.x** and **MySQL**.

---

## 📦 Option 1: Modern Cloud Deployment (Docker-based)
We have containerized the application with a `Dockerfile` and `docker-compose.yml`. You can host this stack on modern platforms like **Railway**, **Render**, or **Fly.io**.

### Docker Configuration Details
*   **Web server:** Apache running on port `80` (mapped locally to `8000`).
*   **PHP version:** PHP 8.2 with `pdo` and `pdo_mysql` extensions configured.
*   **Database:** MySQL 8.0 container.
*   **Environment variables supported (highly customizable):**
    *   `DB_HOST`: Hostname of the database (configured dynamically).
    *   `DB_USER`: Username for MySQL.
    *   `DB_PASS`: Password for MySQL.
    *   `DB_NAME`: Database schema name.

### Local docker run command:
If you have Docker Desktop installed, you can spin up the entire application locally with a single command:
```bash
docker-compose up --build
```
This runs:
*   MySQL on port `3306`
*   PHP Web server on [http://localhost:8000/attendanceapp/login.php](http://localhost:8000/attendanceapp/login.php)

---

## 🌐 Option 2: Traditional Shared PHP Hosting (InfinityFree, 000webhost, cPanel)
If you prefer traditional shared hosting (many of which are free):

1.  **Create a MySQL Database:** Login to your hosting Control Panel (cPanel), create a new MySQL database, and note down the host, DB name, username, and password.
2.  **Configure Environment Variables (Recommended) or edit files:**
    Ensure your server environment has the keys `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME` set up.
3.  **Upload Files:** Use an FTP client (like FileZilla) to upload the `attendanceapp/` folder into the server's public root directory (typically `public_html/` or `htdocs/`).
4.  **Run DB Migrations:** In your browser, navigate to your domain's table creation endpoint to automatically initialize the tables and the default administrator user:
    `https://yourdomain.com/attendanceapp/database/create_table.php`

---

## 🚀 How to Push this Repository to GitHub

To store this clean repository on GitHub, run the following commands in your workspace root:

1.  **Initialize Git:**
    ```bash
    git init
    ```
2.  **Create `.gitignore`:**
    Create a `.gitignore` file to ensure log and cache files don't get pushed:
    ```
    # Gitignore rules
    attendanceapp/report_*.csv
    .DS_Store
    ```
3.  **Stage and Commit Files:**
    ```bash
    git add .
    git commit -m "Initialize clean Attendance Management System template"
    ```
4.  **Link to GitHub and Push:**
    Create a new repository on [GitHub](https://github.com/new). Do not add a README or Gitignore on GitHub (since we already have them). Copy your repository URL and run:
    ```bash
    git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git
    git branch -M main
    git push -u origin main
    ```
