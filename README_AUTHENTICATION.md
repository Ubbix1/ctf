# 🚀 CTF Plexaur - Authentication & Leaderboard System

**Complete implementation guide for setting up user authentication, dashboard, and leaderboard on your CTF subdomain.**

---

## 📚 Documentation Files

Read these in order:

### 1. **[QUICK_START.md](QUICK_START.md)** ⚡ (5 minutes)
Start here! Quick overview of what was implemented and fast setup instructions.

### 2. **[DATABASE_SETUP.md](DATABASE_SETUP.md)** 📊 (MUST READ)
Complete database setup guide with SQL queries. **Do this first!**

### 3. **[SETUP_STEPS.md](SETUP_STEPS.md)** 📖 (Visual step-by-step)
Detailed visual guide with screenshots and troubleshooting.

### 4. **[POINTS_INTEGRATION.php](POINTS_INTEGRATION.php)** 🎯 (Optional)
How to add points when users complete CTF challenges.

### 5. **[IMPLEMENTATION_COMPLETE.md](IMPLEMENTATION_COMPLETE.md)** ✅
Summary of everything that was implemented.

### 6. **[ARCHITECTURE.md](ARCHITECTURE.md)** 🏗️
System design, flowcharts, and technical details.

---

## ⚡ Quick Start (2 minutes)

### What's New?

Your CTF now has:
- ✅ **Local Login/Signup** - Stays on `ctf.plexaur.com` (no redirects!)
- ✅ **User Dashboard** - With leaderboard showing top users
- ✅ **Account Settings** - Change password, view profile
- ✅ **Points System** - Ready to integrate with challenges
- ✅ **Beautiful UI** - Matches your dark hacker theme

### New URLs

```
/signup        → Create new account
/login         → User login
/dashboard     → Leaderboard & dashboard
/account       → Account settings
/logout        → Logout
```

### Setup Required

1. **Create database** (see DATABASE_SETUP.md)
2. **Update credentials** in `src/config/db.php`
3. **Test signup/login** - Done!

---

## 📦 What Was Implemented

### New Files Created

```
src/config/
├── db.php              ← Database connection
└── auth.php            ← Authentication functions

src/views/
├── login.php           ← Login page
├── signup.php          ← Signup page
├── dashboard.php       ← Leaderboard & dashboard
├── account.php         ← Account settings
└── logout.php          ← Logout handler

Documentation/
├── DATABASE_SETUP.md        ← Database guide
├── QUICK_START.md           ← Quick start
├── SETUP_STEPS.md           ← Step-by-step guide
├── POINTS_INTEGRATION.php   ← How to add points
├── IMPLEMENTATION_COMPLETE.md
└── ARCHITECTURE.md          ← Technical design
```

### Modified Files

- **`index.php`** - Added 5 new routes
- **`src/layout.php`** - Updated navigation with user menu

---

## 🎯 Key Features

### Feature 1: Login/Signup on Subdomain ✨
Instead of redirecting to `plexaur.com`, login stays on `ctf.plexaur.com`:
- `/signup` - Create account
- `/login` - Login page
- Beautiful, matching UI

### Feature 2: User Dashboard 📊
New page: `/dashboard`
- Welcome message with user's name
- Display user's total points
- Show user's rank
- Global leaderboard (top 50 users)

### Feature 3: Navigation Update 🔄
When logged in, navbar shows username with dropdown:
```
Username ▼
├─ Dashboard
├─ Account
└─ Logout
```

### Feature 4: Account Settings 🛠️
New page: `/account`
- View profile information
- Change password securely
- Beautiful UI

### Feature 5: Leaderboard 🏆
Shows top 50 users:
- Ranked by points
- Shows challenges solved
- Highlights current user
- Medals for top 3 (🥇🥈🥉)

---

## 🗄️ Database Schema

### `users` Table
```
id          → Unique ID
username    → Unique username (3-50 chars)
email       → Unique email
password    → Bcrypt hashed
points      → Total points (default 0)
created_at  → Account creation date
updated_at  → Last update
```

### `user_challenges` Table
```
id              → Unique ID
user_id         → Reference to users table
challenge_id    → Challenge name
completed_at    → When completed
```

---

## 🔐 Security Features

✅ **Password Security**
- Bcrypt hashing (industry standard)
- Automatic salt generation
- Secure password verification

✅ **Input Validation**
- Email format check
- Username length check (3-50)
- Password confirmation

✅ **Database Security**
- Prepared statements (prevents SQL injection)
- Unique constraints
- Foreign key relationships

✅ **Session Security**
- Server-side PHP sessions
- Login required for protected pages
- Secure logout

✅ **Output Escaping**
- htmlspecialchars() on all user data
- Prevents XSS attacks

---

## 📱 Responsive Design

All new pages are fully responsive:
- ✅ Desktop: Full navigation
- ✅ Mobile: Hamburger menu
- ✅ Tablet: Adaptive layout
- ✅ Dark theme: Matches existing design

---

## 🛠️ Setup Instructions

### Step 1: Database Setup (Required!)
See: **[DATABASE_SETUP.md](DATABASE_SETUP.md)**

1. Create database `ctf_plexaur` in phpMyAdmin
2. Run SQL queries to create tables
3. Verify tables exist

### Step 2: Update Credentials
Edit: `src/config/db.php`

```php
$db_host = 'localhost';      // Your host
$db_user = 'root';           // Your MySQL user
$db_pass = '';               // Your password
$db_name = 'ctf_plexaur';    // Database name
```

### Step 3: Test Everything!
See: **[SETUP_STEPS.md](SETUP_STEPS.md)** for detailed testing steps

1. Visit `/signup` → Create test account
2. Visit `/login` → Login
3. Visit `/dashboard` → See leaderboard
4. Visit `/account` → Test account settings

---

## 🎯 Next Steps

### Optional: Add Points Integration
To award points when challenges are completed, see:
**[POINTS_INTEGRATION.php](POINTS_INTEGRATION.php)**

Add this code to `src/views/ctf-hub.php` when a correct flag is submitted:
```php
if (is_logged_in()) {
    require_once __DIR__ . '/../config/auth.php';
    record_challenge_completion($_SESSION['user_id'], $challenge_id, 10);
}
```

---

## 🆘 Troubleshooting

### "Can't connect to database"
→ Check credentials in `src/config/db.php`
→ Verify MySQL is running
→ Check database exists in phpMyAdmin

### "Page not found" on signup/login
→ Check routes in `index.php`
→ Verify view files exist in `src/views/`

### "User already exists"
→ This is correct! Each username must be unique
→ Try different username

### "No points after challenge"
→ Add integration code from `POINTS_INTEGRATION.php`
→ Verify user is logged in

See **[SETUP_STEPS.md](SETUP_STEPS.md)** for more troubleshooting.

---

## 🔗 File Quick Reference

| File | Purpose | Edit? |
|------|---------|-------|
| `src/config/db.php` | Database connection | ✏️ YES |
| `src/config/auth.php` | Auth functions | 📖 NO |
| `src/views/login.php` | Login form | 📖 NO |
| `src/views/signup.php` | Signup form | 📖 NO |
| `src/views/dashboard.php` | Leaderboard | 📖 NO |
| `src/views/account.php` | Account settings | 📖 NO |
| `src/layout.php` | Navigation | 📖 NO |
| `index.php` | Router | 📖 NO |

---

## 📊 Database Queries

View users and points:
```sql
SELECT u.username, u.points, COUNT(uc.id) as challenges_solved
FROM users u
LEFT JOIN user_challenges uc ON u.id = uc.user_id
GROUP BY u.id
ORDER BY u.points DESC;
```

Reset points (admin):
```sql
UPDATE users SET points = 0;
DELETE FROM user_challenges;
```

---

## 🎓 Learning Resources

- **Overview**: [QUICK_START.md](QUICK_START.md)
- **Database**: [DATABASE_SETUP.md](DATABASE_SETUP.md)
- **Setup Guide**: [SETUP_STEPS.md](SETUP_STEPS.md)
- **Technical**: [ARCHITECTURE.md](ARCHITECTURE.md)
- **Points System**: [POINTS_INTEGRATION.php](POINTS_INTEGRATION.php)

---

## 📞 What's Next?

1. ✅ Read QUICK_START.md (2 min)
2. ✅ Read DATABASE_SETUP.md and create database (5 min)
3. ✅ Update credentials in src/config/db.php (1 min)
4. ✅ Test signup/login/dashboard (5 min)
5. ✅ Add points integration (optional, 10 min)
6. ✅ Deploy to production!

---

## 🎉 Summary

Your CTF platform now has:
- ✅ **Local authentication** (no external redirects)
- ✅ **Beautiful UI** (matches your dark theme)
- ✅ **User dashboard** with leaderboard
- ✅ **Account management** (change password)
- ✅ **Secure database** with bcrypt passwords
- ✅ **Points system** (ready to integrate)
- ✅ **Mobile responsive** design

**Start with**: [QUICK_START.md](QUICK_START.md) or [DATABASE_SETUP.md](DATABASE_SETUP.md)

Good luck with your CTF! 🚀

---

**Questions?** Check the documentation files - they have detailed explanations!
