# SolarDesk — Veera Solar Proposal & Quotation Builder

A PHP 8.1+ web application prepared from the supplied written brief. It uses PDO SQLite and vanilla JavaScript; no Composer or npm packages are required.

## Run locally

Requirements:

- PHP 8.1 or newer
- PHP extensions `pdo_sqlite`, `sqlite3`, and `mbstring`
- A web server with PHP enabled (or PHP's development server for local testing)

From this directory:

```sh
php -S 0.0.0.0:8000
```

Open `http://localhost:8000`, create a company account, and set the company details under **Company Settings**. The app creates `data/solar-proposals.sqlite` on first API request. Keep the `data/` directory writable by the PHP process and persistent between restarts. Back it up with the rest of the deployment data.

For production, use HTTPS, a persistent writable volume, routine database backups, and a PHP-capable host. Do not expose `data/` as a public file directory; the included `.htaccess` is intended for Apache, and other servers need an equivalent deny rule.

## Included

- Company signup/sign-in, hashed passwords, session cookies, CSRF checks, and company-scoped database queries.
- Separate company branding and proposal content.
- Customer, material/service, costing, proposal, and quotation-history flows.
- Costing-to-proposal conversion with internal purchase cost kept in private proposal fields. Customer print pages are composed from an allowlist of public price fields only.
- Pricing bases: per piece, watt, kW, metre, square foot, and lump sum. A kW is converted to exactly 1,000 W.
- Discount, GST, additional charges, subsidy, annual-generation, savings, and simple payback estimates.
- A4-style multi-section proposal preview and the browser's Print / Save as PDF workflow.
- Built-in starting templates for grid-tied, hybrid, and commercial rooftop offers.
- Company logo, contact details, GSTIN, colors, header/footer lines, and GST defaults.

## Scope notes

- The referenced `Pise_Sir_3.3kw.pdf` was not attached in this conversation. The initial blue/orange layout is based on the written brief; exact visual matching needs the PDF.
- PDF output uses the browser's print-to-PDF engine; a separate server-side PDF library is not included.
- Subsidy is entered by the user. The app does not determine current scheme eligibility or calculate a legally authoritative subsidy.
- Each new account creates a separate company workspace. This starter does not yet include invitations, multiple staff roles, password-reset email, or company switching.
- Built-in proposal templates are included; custom template authoring and template version history are not.
- Set up and test persistent storage, HTTPS, backups, and any hosting-specific PHP configuration before deployment.

## Data protection notes

All business tables include a company ID and are queried through the authenticated session's company ID. Passwords are stored with PHP's `password_hash()`. Browser PDF pages display item names/specifications, customer rates, amounts, taxes, subsidy and totals; they do not render stored purchase rates or margin. Test any deployment-specific customization against this rule before issuing live documents.
