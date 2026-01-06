# DesertSafariGo — Dynamic PHP Site (Root Domain)

This package is built to be installed directly in **public_html** so your site works at:

**https://desertsafarigo.com/**  
(No subfolders.)

---

## 1) Upload to Hostinger (cPanel) — ROOT

1. Go to **cPanel → File Manager**
2. Open **public_html/**
3. Upload `desertsafarigo_root.zip`
4. Extract it **inside public_html** (so `index.php` sits directly inside `public_html/`)

After extraction, your structure should look like:

- public_html/index.php
- public_html/admin/
- public_html/install/
- public_html/assets/
- public_html/includes/
- public_html/pages/
- public_html/partials/

---

## 2) Create MySQL Database (cPanel)

1. Go to **MySQL Databases**
2. Create:
   - a database (example: `cpaneluser_desertsafarigo`)
   - a database user (example: `cpaneluser_dsg`)
   - assign the user to the database with **ALL PRIVILEGES**

Keep these 4 values:
- DB Host: usually `localhost`
- DB Name
- DB User
- DB Password

---

## 3) Run the Installer (creates tables + admin login)

Open in browser:

**https://desertsafarigo.com/install/**

Fill:
- DB Host / Name / User / Password
- Admin email + admin password

Click **Install Now**.

When you see “Installed successfully”:
1. Login at: **https://desertsafarigo.com/admin/login.php**
2. **Delete the `/install` folder** from File Manager (security).

---

## 4) Edit Everything from Admin

Admin URL: **https://desertsafarigo.com/admin/**

You can manage:
- Leads (from homepage form)
- Packages
- Highlights (carousel)
- Why Us items (parallax section)
- Testimonials
- Popups (recently booked toast)
- Blog (SEO posts)
- Settings (texts, CTA labels, backgrounds, contacts, webhook)

---

## 5) Leads to Google Sheets (Optional)

This site stores leads in MySQL **by default**.

If you also want leads pushed to Google Sheets:
- Create an Apps Script Web App that appends a row to your Sheet
- Paste its Webhook URL into:
  **Admin → Settings → Lead Webhook URL (optional)**

The webhook receives JSON like:
```json
{
  "full_name":"…",
  "phone":"…",
  "email":"…",
  "package_name":"…",
  "trip_date":"…",
  "persons":"…",
  "contact_pref":"…",
  "message":"…",
  "created_at":"YYYY-MM-DD HH:MM:SS",
  "source":"desertsafarigo.com"
}
```

---

## Troubleshooting

- **Homepage shows 500 error:** your hosting PHP version should be **PHP 8.1+**
- **Installer fails to connect DB:** check your exact DB name/user (cPanel prefixes matter)
- **Changes not visible:** clear browser cache or disable any caching plugin / server cache

---

## Security Notes

- Always delete `/install` after setup.
- The package includes `/includes/.htaccess` denying direct access to `config.local.php`.

