const Busboy = require('busboy');
const { put } = require('@vercel/blob');

const MAX_BYTES = 2 * 1024 * 1024;
const ALLOWED = {
  'image/png': 'png',
  'image/jpeg': 'jpg',
  'image/webp': 'webp',
};

module.exports = async function handler(req, res) {
  res.setHeader('Content-Type', 'application/json; charset=utf-8');
  res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('Cache-Control', 'no-store');

  if (req.method !== 'POST') {
    res.setHeader('Allow', 'POST');
    return res.status(405).json({ ok: false, error: 'Use POST to upload an image.' });
  }

 if (!process.env.BLOB_READ_WRITE_TOKEN && !process.env.BLOB_STORE_ID) {
    return res.status(500).json({ ok: false, error: 'Vercel Blob is not connected. Create a Blob store in the Vercel project.' });
  }

  let kind = 'logo';
  let fileBuffer = null;
  let fileMime = '';
  let fileSize = 0;
  let tooLarge = false;
  let parseError = null;

  try {
    const bb = Busboy({ headers: req.headers, limits: { files: 1, fileSize: MAX_BYTES, fields: 5 } });
    const chunks = [];

    bb.on('field', (name, value) => {
      if (name === 'kind' && ['header', 'logo', 'qr'].includes(value)) kind = value;
    });
    bb.on('file', (name, stream, info) => {
      if (name !== 'asset') {
        stream.resume();
        return;
      }
      fileMime = info.mimeType || '';
      stream.on('data', chunk => {
        fileSize += chunk.length;
        if (fileSize <= MAX_BYTES) chunks.push(chunk);
      });
      stream.on('limit', () => { tooLarge = true; });
      stream.on('error', err => { parseError = err; });
      stream.on('end', () => { fileBuffer = Buffer.concat(chunks); });
    });

    await new Promise((resolve, reject) => {
      bb.on('error', reject);
      bb.on('finish', resolve);
      req.pipe(bb);
    });

    if (parseError) throw parseError;
    if (tooLarge || fileSize > MAX_BYTES) return res.status(400).json({ ok: false, error: 'Image must be 2 MB or smaller.' });
    if (!fileBuffer || !fileBuffer.length) return res.status(400).json({ ok: false, error: 'No image uploaded.' });
    if (!ALLOWED[fileMime]) return res.status(400).json({ ok: false, error: 'Use PNG, JPG or WEBP image.' });

    const pathname = `veera-solar/${kind}-${Date.now()}.${ALLOWED[fileMime]}`;
    const blob = await put(pathname, fileBuffer, {
      access: 'public',
      contentType: fileMime,
      addRandomSuffix: true,
      storeId: process.env.veerapublicimages_STORE_ID,
    });
    return res.status(200).json({ ok: true, url: blob.url });
  } catch (err) {
    console.error('Veera Solar upload failed:', err);
    return res.status(500).json({ ok: false, error: 'Image upload failed. Check Vercel function logs.' });
  }
};

// Keep the raw multipart stream available to Busboy in Vercel's Node.js runtime.
module.exports.config = { api: { bodyParser: false } };
