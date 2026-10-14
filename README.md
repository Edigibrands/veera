# Veera Solar on Vercel

This project uses the community PHP runtime for the existing PHP page, a Node.js function for image uploads, and Vercel Blob for durable image storage.

## Deploy

1. Copy the contents of this folder into the root of the `Edigibrands/veera` GitHub repository. The repository root must contain `vercel.json`, `package.json`, and the `api/` folder.
2. In Vercel, open the `veera` project and go to **Storage** → **Create Database** → **Blob**. Create a **Public** Blob store and connect it to this project. Ensure the `BLOB_READ_WRITE_TOKEN` environment variable is available for Production and Preview.
3. Commit and push the files to the `main` branch, then wait for a successful deployment.
4. Open the production domain. The app should render HTML rather than downloading `index.php`.

## Important storage behavior

- Header/logo/QR images are uploaded to Vercel Blob, not the serverless function's temporary filesystem.
- Saved quotation, invoice, and company text settings are still stored in the browser's `localStorage`. They persist in that browser/profile, not across devices or different browsers. A shared multi-user app needs a database and authentication; this package does not silently pretend localStorage is shared storage.
- `api/calculate` is a JSON calculation endpoint. The current browser UI performs its calculations client-side and does not require this endpoint for normal operation.
- PHP is provided through the community `vercel-php` runtime, not a native first-party PHP runtime. If the runtime fails during build, inspect the Vercel build logs before changing application code.

## Test

- `/` should display the app.
- `GET /api/upload` should return HTTP 405 JSON (not download a file).
- Upload a small PNG/JPG/WEBP logo in Settings and click **Apply Header & Footer**. The response should save a `*.blob.vercel-storage.com` URL in the browser's brand settings.
