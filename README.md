# CTF Plexaur Website

A lightweight, PHP-based Capture The Flag (CTF) web platform containing interactive tools and challenge-based exercises focused on web security, cryptography, and networking.

This document explains **how to run the project**, **required PHP extensions**, **project structure**, and **common pitfalls**, since some PHP classes used are **not enabled by default**.

---

## 1. Project Overview

CTF Plexaur is designed as:

* A **router-based PHP app** (no framework)
* Educational CTF challenges + security tools
* Minimal dependencies (pure PHP)

---

## 2. Requirements

### Minimum

* PHP **8.0+** (7.4 may work, but not recommended)
* Web server:

  * PHP built-in server **OR**
  * XAMPP / Apache

### Required PHP Extensions (IMPORTANT)

Some tools will **break silently** if these are disabled.

Make sure the following extensions are enabled in `php.ini`:

```ini
extension=openssl
extension=mbstring
extension=gd
extension=exif
extension=fileinfo
```

#### Why they are needed

| Extension | Used For                                      |
| --------- | --------------------------------------------- |
| openssl   | Cryptography challenges (MD5, crypto tools)   |
| mbstring  | Safe string handling (Base64, Caesar cipher)  |
| gd        | Image processing (invert tool, steganography) |
| exif      | Metadata extraction challenge                 |
| fileinfo  | File type detection (uploads / pcap)          |

After enabling extensions:

```bash
sudo systemctl restart apache2
# or
sudo systemctl restart php-fpm
```

---

## 3. Running the Project Locally

### Option 1: PHP Built-in Server (Recommended)

From the project root:

```bash
php -S localhost:8000
```

Open:

```
http://localhost:8000
```

### Option 2: XAMPP

1. Move project to:

   ```
   htdocs/ctf-plexaur
   ```
2. Start **Apache** from XAMPP
3. Open:

   ```
   http://localhost/ctf-plexaur
   ```

---

## 4. Project Structure Explained

```txt
/ (Root)
├── index.php                # Main router (entry point)
├── assets/                  # Static files (mostly unused, CDN preferred)
└── src/
    ├── layout.php           # Global layout (header/footer wrapper)
    ├── views/               # Page-level views
    │   ├── home.php         # Landing page
    │   └── ctf-hub.php      # Central challenge hub
    └── tools/               # Functional tools & challenges
```

---

## 5. Router Logic (index.php)

* Acts as a **front controller**
* Loads views dynamically

Typical flow:

```
index.php
  └── determines route
      └── loads src/layout.php
           └── injects selected view/tool
```

---

## 6. Tools Directory Breakdown

### `/tools` (Utilities)

| File       | Description                     |
| ---------- | ------------------------------- |
| base64.php | Encode/decode Base64 strings    |
| steg.php   | Image steganography (LSB-based) |
| invert.php | Image color inversion using GD  |
| pcap.php   | Basic PCAP file inspection      |

### `/tools/crypto`

| File       | Description                         |
| ---------- | ----------------------------------- |
| caesar.php | Caesar cipher encryption/decryption |

### `/tools/ctf` (Challenges)

| File         | Challenge Type              |
| ------------ | --------------------------- |
| base64.php   | Encoded flag challenge      |
| metadata.php | EXIF metadata extraction    |
| password.php | Weak password logic flaw    |
| redirect.php | Open redirect vulnerability |
| ports.php    | Network port reasoning      |
| xss.php      | Reflected XSS               |
| md5.php      | Broken hash comparison      |

---

## 7. Common Issues & Fixes

### Images not processing

✔ Ensure `gd` extension is enabled

```bash
php -m | grep gd
```

---

### Metadata challenge not working

✔ Enable `exif`

```bash
php -m | grep exif
```

---

### Blank page / white screen

✔ Enable error reporting (dev only)

Add at top of `index.php`:

```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

**CTF Plexaur** — Learn by breaking 
