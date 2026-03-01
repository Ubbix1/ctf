# 🎯 CTF Plexaur - System Overview & Architecture

## User Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                  CTF PLEXAUR AUTHENTICATION FLOW                 │
└─────────────────────────────────────────────────────────────────┘

                    ┌─────────────────────────┐
                    │   User Visits Site      │
                    │  ctf.plexaur.com        │
                    └────────────┬────────────┘
                                 │
                ┌────────────────┴────────────────┐
                │                                 │
         ┌──────▼──────┐              ┌──────────▼─────┐
         │  Logged In? │              │  Logged In?     │
         │    NO       │              │     YES         │
         └──────┬──────┘              └────────┬────────┘
                │                              │
    ┌───────────┴──────────┐         ┌────────▼────────┐
    │                      │         │  Show Username  │
    │                      │         │  in navbar ▼    │
    │                      │         │                 │
┌──▼────┐         ┌───────▼───┐     │ [Dashboard]     │
│ Login │         │  Sign Up  │     │ [Account]       │
│ Page  │         │   Page    │     │ [Logout]        │
└───┬───┘         └───┬───────┘     └────────┬────────┘
    │                 │                      │
    │      [Email + Password]                │
    │           [Username + Email +          │
    │          Password + Confirm]           │
    │                 │                      │
    └────────┬────────┘                      │
             │                               │
     ┌───────▼────────────────┐              │
     │ Validate Credentials   │              │
     │ Check Database (users) │              │
     │ Bcrypt Password Check  │              │
     └───────┬────────────────┘              │
             │                               │
         [Success]                           │
             │                               │
     ┌───────▼──────────────────┐            │
     │ Set PHP Session          │            │
     │ $_SESSION['user_id']     │            │
     │ $_SESSION['username']    │            │
     │ $_SESSION['email']       │            │
     └───────┬──────────────────┘            │
             │                               │
     ┌───────▼──────────────────┐            │
     │ Redirect to Dashboard    │◄───────────┘
     │ /dashboard               │
     └──────────────────────────┘
```

---

## Database Schema

```
┌──────────────────────────────────────────────────────────────┐
│                    DATABASE: ctf_plexaur                      │
└──────────────────────────────────────────────────────────────┘

┌─────────────────────────────┐
│         USERS TABLE         │
├─────────────────────────────┤
│ id (PK)                     │
│ username (UNIQUE)           │
│ email (UNIQUE)              │
│ password (BCRYPT)           │
│ points                      │
│ created_at                  │
│ updated_at                  │
└────────────┬────────────────┘
             │ (1:Many)
             │
             │ user_id (FK)
             │
┌────────────▼──────────────────────┐
│     USER_CHALLENGES TABLE         │
├───────────────────────────────────┤
│ id (PK)                           │
│ user_id (FK → users.id)           │
│ challenge_id                      │
│ completed_at                      │
└───────────────────────────────────┘

Example Data:
─────────────
users table:
id | username    | email           | password     | points
1  | ProHacker   | pro@email.com   | $2y$10$...  | 80
2  | CyberNinja  | cyber@email.com | $2y$10$...  | 70

user_challenges table:
id | user_id | challenge_id | completed_at
1  | 1       | caesar       | 2024-01-15 10:30:00
2  | 1       | base64       | 2024-01-15 11:15:00
3  | 2       | caesar       | 2024-01-15 10:45:00
```

---

## File & Function Map

```
┌──────────────────────────────────────────────────────────────┐
│                   AUTHENTICATION FLOW                         │
└──────────────────────────────────────────────────────────────┘

User Action          → PHP File           → Function
─────────────────────────────────────────────────────────────
Visit /signup        → signup.php         → register_user()
                     → config/auth.php    

Visit /login         → login.php          → login_user()
                     → config/auth.php    

Visit /dashboard     → dashboard.php      → is_logged_in()
                     → config/auth.php    → get_current_user()
                                         → get_leaderboard()

Visit /account       → account.php        → get_current_user()
Change Password      → config/auth.php    → verify password
                                         → hash new password

Complete Challenge   → ctf-hub.php        → record_challenge_completion()
                     → config/auth.php    → add_user_points()

Visit /logout        → logout.php         → logout_user()
                     → config/auth.php    
```

---

## Session Lifecycle

```
┌────────────────────────────────────────────────────────────┐
│              SESSION MANAGEMENT LIFECYCLE                   │
└────────────────────────────────────────────────────────────┘

1. SIGNUP/LOGIN
   └─ Set $_SESSION['user_id']
   └─ Set $_SESSION['username']
   └─ Set $_SESSION['email']

2. BROWSING (LOGGED IN)
   └─ Each page checks: if (is_logged_in()) { ... }
   └─ Access to /dashboard, /account
   └─ Username shows in navbar

3. CHALLENGE COMPLETION (OPTIONAL)
   └─ Check: if (is_logged_in()) {
   └─ Add points to user
   └─ Record challenge completion

4. LOGOUT
   └─ Destroy session: session_destroy()
   └─ Clear all $_SESSION data
   └─ Redirect to home

5. EXPIRE
   └─ Session expires (default 24 mins)
   └─ User redirected to login
```

---

## Navigation Structure

```
┌────────────────────────────────────────────────────────────┐
│                   NAVIGATION TREE                           │
└────────────────────────────────────────────────────────────┘

                    ┌─────────────────┐
                    │   Home Page     │
                    │   (/)           │
                    └────────┬────────┘
                             │
        ┌────────────────────┼────────────────────┐
        │                    │                    │
        │                    │                    │
    ┌───▼────┐    ┌─────────▼──────┐    ┌───────▼────┐
    │ LOGIN/ │    │ CTF CHALLENGES │    │  TOOLS     │
    │ SIGNUP │    │  (/ctf)        │    │  Dropdown  │
    │        │    │                │    │            │
    │        │    │ [Challenges]   │    ├─ Base64    │
    └───┬────┘    └────────────────┘    ├─ Steg      │
        │                               ├─ Invert    │
        │                               └─ PCAP      │
        │
    ┌───▼──────────────────────┐
    │ LOGGED IN ONLY           │
    ├──────────────────────────┤
    │ ├─ Dashboard (/dashboard)│
    │ │  └─ Leaderboard        │
    │ │  └─ Points             │
    │ │  └─ Rank               │
    │ │                        │
    │ ├─ Account (/account)    │
    │ │  └─ Profile            │
    │ │  └─ Change Password    │
    │ │                        │
    │ └─ Logout (/logout)      │
    └──────────────────────────┘
```

---

## Leaderboard Algorithm

```
┌────────────────────────────────────────────────────────────┐
│           LEADERBOARD RANKING ALGORITHM                     │
└────────────────────────────────────────────────────────────┘

1. Get all users from database
2. For each user, count completed challenges
3. Sort by: 
   - PRIMARY: points DESC (highest first)
   - SECONDARY: username ASC (alphabetical tiebreaker)
4. Assign rank (1, 2, 3, ...)
5. Highlight current user
6. Show medals for top 3:
   - 1st: 🥇 Gold
   - 2nd: 🥈 Silver
   - 3rd: 🥉 Bronze

SQL Query:
──────────
SELECT u.id, u.username, u.points, COUNT(uc.id) as challenges_solved
FROM users u
LEFT JOIN user_challenges uc ON u.id = uc.user_id
GROUP BY u.id
ORDER BY u.points DESC, u.username ASC
LIMIT 50;
```

---

## Security Layers

```
┌────────────────────────────────────────────────────────────┐
│                  SECURITY ARCHITECTURE                      │
└────────────────────────────────────────────────────────────┘

LAYER 1: INPUT VALIDATION
├─ Email format validation (filter_var)
├─ Username length check (3-50 chars)
├─ Password length check (min 6 chars)
└─ Password confirmation match

LAYER 2: PASSWORD SECURITY
├─ Bcrypt hashing (PASSWORD_BCRYPT)
├─ Salt generation automatic
└─ Verification with password_verify()

LAYER 3: DATABASE SECURITY
├─ Prepared statements (PDO)
├─ No string interpolation
├─ SQL injection prevention
└─ Foreign key constraints

LAYER 4: SESSION SECURITY
├─ Server-side session storage
├─ Secure PHP session handling
├─ Session expiration (24 mins default)
└─ HTTPS recommended

LAYER 5: OUTPUT ESCAPING
├─ htmlspecialchars() on all user output
├─ XSS prevention
└─ Safe display of user data

LAYER 6: DATABASE CONSTRAINTS
├─ UNIQUE on username & email
├─ Primary/Foreign keys
└─ Type validation
```

---

## Data Flow: Challenge Completion → Points

```
┌────────────────────────────────────────────────────────────┐
│         CHALLENGE COMPLETION & POINTS FLOW                  │
└────────────────────────────────────────────────────────────┘

1. User submits flag
   └─ POST /ctf
   └─ Challenge ID + Flag

2. Server validates flag
   └─ Check against master flags array
   └─ If correct → proceed
   └─ If wrong → return error

3. If correct & user logged in
   └─ Check: is_logged_in()
   └─ Get user_id from $_SESSION

4. Award points
   └─ record_challenge_completion()
   │
   ├─ Check if already completed
   ├─ Insert into user_challenges
   └─ Call add_user_points()

5. Update user points
   └─ UPDATE users SET points = points + 10
   └─ WHERE id = user_id

6. Return success to client
   └─ JavaScript updates UI
   └─ Shows +10 points message

7. Leaderboard updates
   └─ Next time user visits /dashboard
   └─ Recalculated rankings
   └─ User's rank may change
```

---

## Key Decisions & Why

| Decision | Why? |
|----------|------|
| Bcrypt for passwords | Industry standard, automatic salting, slow (prevents brute force) |
| Server-side sessions | More secure than client-side cookies |
| PDO prepared statements | Prevents SQL injection attacks |
| Separate config file | Easy to update credentials without touching code |
| Points in database | Persistent, queryable, leaderboard-friendly |
| Unique username/email | Prevents duplicate accounts, easy user lookup |
| Server-side validation | Can't be bypassed by client-side manipulation |
| htmlspecialchars() output | Prevents XSS attacks |

---

## Performance Considerations

```
┌────────────────────────────────────────────────────────────┐
│              PERFORMANCE OPTIMIZATIONS                      │
└────────────────────────────────────────────────────────────┘

1. Database Indexes
   └─ username (searched on login)
   └─ email (searched on signup)
   └─ user_id (searched on leaderboard)

2. Query Optimization
   └─ Leaderboard: LIMIT 50 (not all users)
   └─ JOIN instead of multiple queries
   └─ GROUP BY for aggregation

3. Caching (Optional)
   └─ Cache leaderboard for 1 hour
   └─ Cache user points
   └─ Reduce database hits

4. Pagination (Future)
   └─ Leaderboard: Show 20 per page
   └─ Load more: OFFSET + LIMIT
   └─ Reduce HTML size
```

---

## Testing Scenarios

```
┌────────────────────────────────────────────────────────────┐
│              TEST SCENARIOS TO VERIFY                       │
└────────────────────────────────────────────────────────────┘

✓ Signup Test
  └─ Create new account
  └─ Check duplicate username/email
  └─ Verify data in database

✓ Login Test
  └─ Correct credentials → logged in
  └─ Wrong password → error
  └─ Non-existent email → error

✓ Dashboard Test
  └─ Redirect if not logged in
  └─ Show correct leaderboard
  └─ Highlight current user

✓ Points Test
  └─ Complete challenge → points added
  └─ Leaderboard updates
  └─ Rank changes

✓ Security Test
  └─ Try SQL injection in username → blocked
  └─ Try XSS in profile → escaped
  └─ Try session hijacking → failed

✓ Mobile Test
  └─ Responsive design works
  └─ Touch-friendly buttons
  └─ Mobile menu functions
```

---

## Deployment Checklist

```
┌────────────────────────────────────────────────────────────┐
│            DEPLOYMENT TO PRODUCTION                         │
└────────────────────────────────────────────────────────────┘

Database:
☐ Database created on production server
☐ All tables created (users, user_challenges)
☐ Indexes created
☐ Backup of database

Server Configuration:
☐ Update credentials in src/config/db.php
☐ Set appropriate permissions (644 for files)
☐ Enable HTTPS (SSL certificate)
☐ Configure session.save_path

Security:
☐ Set error_reporting to not display errors
☐ Use .env file for sensitive data (better practice)
☐ Regular database backups
☐ Monitor for suspicious activity

Testing:
☐ Full signup/login/logout flow
☐ Complete 1+ challenge, verify points
☐ Check leaderboard rankings
☐ Test on mobile devices
☐ Test all links work

Monitoring:
☐ Set up error logging
☐ Monitor database size
☐ Watch for failed logins (brute force attempts)
☐ Regular performance checks
```

---

## Future Enhancement Ideas

```
┌────────────────────────────────────────────────────────────┐
│           POSSIBLE FUTURE FEATURES                          │
└────────────────────────────────────────────────────────────┘

User Features:
├─ Email verification on signup
├─ Forgot password email reset
├─ User profile pictures (avatar)
├─ User bio/description
├─ Team mode (group challenges)
└─ Follow other users

Gamification:
├─ Achievement badges
├─ Difficulty tiers (bronze/silver/gold)
├─ Time-based bonuses (solve faster = more points)
├─ Streak counter (consecutive days)
└─ Level system

Leaderboard:
├─ Weekly rankings
├─ Monthly rankings
├─ Filter by difficulty
├─ Search for users
└─ User comparison

Admin:
├─ Admin dashboard
├─ Manually award points
├─ Create/edit challenges
├─ View user analytics
└─ Ban malicious users
```

---

## Summary

Your CTF Plexaur platform now has a **complete, secure, and scalable** authentication and leaderboard system!

**Key Features:**
- ✅ Local login/signup (no external redirects)
- ✅ User dashboard with leaderboard
- ✅ Points & ranking system
- ✅ Secure database integration
- ✅ Responsive design
- ✅ Account management

**Next Step:** Add the points integration code to your CTF challenges!
