<?php
// Server-side upload endpoint: keep image files on disk instead of localStorage (avoids browser quota errors).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['brand_upload'])) {
    header('Content-Type: application/json; charset=utf-8');
    $requestedKind = $_POST['kind'] ?? 'logo';
    $kind = in_array($requestedKind, ['header', 'logo', 'qr'], true) ? $requestedKind : 'logo';
    if (!isset($_FILES['asset']) || $_FILES['asset']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400); echo json_encode(['ok'=>false,'error'=>'No image uploaded or upload failed.']); exit;
    }
    $file = $_FILES['asset'];
    if ($file['size'] > 2 * 1024 * 1024) {
        http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Image must be 2 MB or smaller.']); exit;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $exts = ['image/png'=>'png','image/jpeg'=>'jpg','image/webp'=>'webp'];
    if (!isset($exts[$mime])) {
        http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Use PNG, JPG or WEBP image.']); exit;
    }
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Could not create uploads folder.']); exit;
    }
    $filename = 'solar-' . $kind . '.' . $exts[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . DIRECTORY_SEPARATOR . $filename)) {
        http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Could not save uploaded image. Check folder permissions.']); exit;
    }
    echo json_encode(['ok'=>true,'url'=>'uploads/' . $filename]); exit;
}
$brand = 'VEERA SOLAR';
$location = 'VELAPUR';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Veera Solar | Proposal & Quotation</title>
<style>
:root{--blue:#0872b9;--deep:#07518a;--orange:#f59a00;--green:#65b900;--ink:#203044;--muted:#687789;--line:#dce5ec;--pale:#f3f8fb}
*{box-sizing:border-box}body{margin:0;background:#edf2f6;color:var(--ink);font:15px/1.45 Arial,Helvetica,sans-serif}.app{max-width:1200px;margin:24px auto;padding:0 16px}.toolbar{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;margin-bottom:14px}.btn{border:0;border-radius:8px;padding:11px 16px;font-weight:700;cursor:pointer;background:var(--blue);color:white}.btn.secondary{background:#fff;color:var(--deep);border:1px solid var(--line)}.sheet{background:#fff;max-width:900px;margin:auto;box-shadow:0 8px 30px #16324a14;border-radius:12px;overflow:hidden}.brandbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;padding:18px 34px;border-bottom:4px solid var(--blue);gap:20px}.brandbar.has-banner{display:block;padding:0;border-bottom:4px solid var(--blue)}.brandbar.has-banner #headerBanner{display:block;width:100%;max-width:100%;height:auto;max-height:none;object-fit:contain;margin:0}.brandbar.has-banner .brandcontact,.brandbar.has-banner #brandIdentity{display:none!important}.brand{display:flex;align-items:center;gap:14px}.brand img{width:86px;height:75px;object-fit:contain}.brand h1{font-size:23px;letter-spacing:1px;margin:0;color:var(--blue)}.brand p{margin:2px 0 0;color:var(--orange);font-weight:800;letter-spacing:5px}.brandcontact{text-align:right;color:var(--muted);font-size:12px}.content{padding:30px 34px 36px}.eyebrow{color:var(--orange);font-weight:800;text-transform:uppercase;letter-spacing:1.7px;font-size:12px}.title{color:var(--deep);font-size:32px;line-height:1.1;margin:7px 0 10px}.intro{color:var(--muted);margin:0 0 24px}.panel{border:1px solid var(--line);border-radius:10px;padding:18px;margin:18px 0;background:#fff}.panel h2{font-size:17px;color:var(--deep);margin:0 0 14px}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px}.field label{display:block;font-size:12px;font-weight:700;color:#536579;margin-bottom:5px}.field input,.field select,.field textarea{width:100%;border:1px solid #ccd8e2;border-radius:7px;padding:10px;font:inherit;background:#fff;color:var(--ink)}.field textarea{min-height:68px;resize:vertical}.full{grid-column:1/-1}.tablewrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:590px}th{background:var(--orange);color:#fff;text-align:left;font-size:12px;padding:10px}td{padding:8px;border-bottom:1px solid var(--line)}td input{width:100%;min-width:75px;border:1px solid #d3dee7;border-radius:5px;padding:8px;font:inherit}td .desc{min-width:180px}.remove{background:#fff0ee;color:#b43a2d;border:0;border-radius:5px;padding:7px;cursor:pointer}.addrow{margin-top:12px;background:#eef7fc;color:var(--deep);border:1px solid #cce5f5;border-radius:7px;padding:9px 12px;font-weight:700;cursor:pointer}.totals{margin-left:auto;max-width:390px;margin-top:18px}.totalrow{display:flex;justify-content:space-between;gap:20px;padding:8px 0;border-bottom:1px solid var(--line)}.totalrow strong{font-variant-numeric:tabular-nums}.totalrow.grand{font-size:19px;color:var(--deep);border-bottom:0;border-top:2px solid var(--blue);margin-top:5px;padding-top:13px}.note{font-size:12px;color:var(--muted)}.footer{border-top:4px solid var(--blue);padding:15px 34px;display:flex;justify-content:space-between;gap:12px;color:#526579;font-size:11px}.footer strong{color:var(--deep)}.notice{padding:11px 13px;background:#fff8e8;border-left:4px solid var(--orange);font-size:12px;color:#72501a;border-radius:4px}.status{margin-top:10px;color:var(--green);font-weight:700;min-height:20px}
.preview-modal{display:none;position:fixed;inset:0;background:#0b1b2dcc;z-index:1000;padding:18px;overflow:auto}.preview-modal.open{display:block}.preview-toolbar{max-width:900px;margin:0 auto 12px;background:#fff;border-radius:9px;padding:10px;display:flex;justify-content:space-between;align-items:center;gap:8px;position:sticky;top:0}.preview-paper{max-width:900px;margin:0 auto;background:#fff;box-shadow:0 12px 40px #0003}.preview-paper .no-print{display:none!important}

@media(max-width:680px){.grid{grid-template-columns:1fr 1fr}.brandbar{padding:14px 18px}.brand img{width:65px;height:60px}.brand h1{font-size:17px}.brand p{letter-spacing:3px;font-size:11px}.brandcontact{display:none}.content{padding:22px 16px}.title{font-size:27px}.footer{padding:14px 16px;flex-direction:column}}
@media print{@page{size:A4;margin:8mm}body{background:#fff}.app{max-width:none;margin:0;padding:0}.toolbar,.no-print{display:none!important}.preview-modal{display:none!important}.sheet{max-width:none;box-shadow:none;border-radius:0;overflow:visible}.brandbar{padding:8px 10px;gap:10px}.brandbar.has-banner{padding:0;display:block}.brandbar.has-banner #headerBanner{display:block!important;width:100%!important;max-width:100%!important;height:auto!important;max-height:none!important;object-fit:contain!important}.brandbar.has-banner .brandcontact,.brandbar.has-banner #brandIdentity{display:none!important}.brand img{width:58px;height:52px}.brand h1{font-size:18px}.content{padding:12px 10px}.title{font-size:25px}.panel{font-size:12px}.grid{gap:8px}.panel{break-inside:avoid;padding:12px;margin:12px 0}.footer{padding:10px 14px;break-inside:avoid}input,select,textarea{border:0!important;padding:2px!important;appearance:none;background:transparent!important}td input{border:0!important;padding:2px!important}th{-webkit-print-color-adjust:exact;print-color-adjust:exact}.totalrow.grand{break-inside:avoid}}

/* App workspace and tab navigation */
body{background:#eef2f7}
.app-shell{min-height:100vh;display:flex}
.sidebar{width:230px;flex:0 0 230px;background:#0d1b2b;color:#d9e5f2;min-height:100vh;display:flex;flex-direction:column;padding:18px 12px;position:sticky;top:0;height:100vh}
.side-brand{display:flex;align-items:center;gap:10px;padding:2px 8px 26px;border-bottom:1px solid #25364b}
.side-brand img{width:34px;height:34px;object-fit:contain;background:#fff;border-radius:7px}
.side-brand strong{display:block;color:#fff;font-size:14px}.side-brand small{display:block;color:#8da7c3;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;margin-top:3px}
.side-nav{display:grid;gap:6px;margin-top:22px}
.nav-item{border:0;background:transparent;color:#aebfd1;text-align:left;padding:12px 12px;border-radius:7px;display:flex;gap:12px;align-items:center;cursor:pointer;font:inherit;font-size:13px}
.nav-item:hover,.nav-item.active{background:#243447;color:#fff}.nav-item.active{box-shadow:inset 3px 0 #f59a00}
.side-foot{margin-top:auto;color:#69829e;font-size:10px;line-height:1.6;padding:12px 8px}
.workspace{min-width:0;flex:1}
.app-topbar{min-height:76px;background:#fff;border-bottom:1px solid #dce5ec;padding:14px 26px;display:flex;align-items:center;justify-content:space-between;gap:20px}
.topbar-title{font-weight:800;color:#123d65;font-size:18px}.topbar-subtitle{color:#687789;font-size:12px;margin-top:3px}.topbar-actions{display:flex;gap:8px;flex-wrap:wrap}
.app{max-width:none;margin:0;padding:22px 26px}
.page-heading{font-size:25px;color:#172f48;margin:2px 0 5px}.page-subtitle{color:#687789;margin:0 0 20px;font-size:13px}
.dashboard-cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin:16px 0}
.metric-card,.dashboard-panel{background:#fff;border:1px solid #dce5ec;border-radius:11px;padding:19px;box-shadow:0 3px 10px #18334a08}
.metric-card span{display:block;color:#687789;font-size:12px;margin-bottom:10px}.metric-card strong{font-size:22px;color:#123d65}
.dashboard-panel{margin-top:18px}.dashboard-panel h2{font-size:17px;color:#123d65;margin:0 0 8px}.dashboard-panel p{color:#687789;font-size:13px}
#brandSettings{max-width:none!important;margin:0!important}
.sheet{max-width:1040px}
#costingPanel .note{color:#687789}
.payment-total{display:flex;justify-content:space-between;align-items:center;border-top:2px solid var(--blue);margin-top:14px;padding:13px 0 2px;font-size:16px;font-weight:800;color:var(--deep)}\n.payment-match-status{margin:8px 0 0;padding:9px 11px;border-radius:6px;font-size:13px;font-weight:700;border:1px solid transparent}\n.payment-match-status.match{color:#16713b;background:#eaf8ef;border-color:#a8dfb8}\n.payment-match-status.mismatch{color:#b42318;background:#fff0ef;border-color:#f4b6b2}
.internal-only,.item-code,[data-internal="true"]{display:none!important}
@media(max-width:900px){.sidebar{width:190px;flex-basis:190px}.app{padding:16px}.app-topbar{padding:12px 16px}.dashboard-cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:650px){.app-shell{display:block}.sidebar{width:auto;min-height:0;height:auto;position:relative;padding:10px}.side-brand{padding-bottom:10px}.side-nav{display:flex;overflow:auto;margin-top:10px}.nav-item{white-space:nowrap;padding:9px}.side-foot{display:none}.app-topbar{align-items:flex-start;flex-direction:column}.app{padding:12px}.dashboard-cards{grid-template-columns:1fr 1fr}.topbar-actions .btn{padding:9px}}
@media print{.sidebar,.app-topbar{display:none!important}.app-shell,.workspace{display:block}.app{padding:0}.tab-page{display:none!important}}

.invoice-form-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
.invoice-form-page input,.invoice-form-page textarea,.invoice-form-page select{pointer-events:auto;user-select:text;cursor:text;background:#fff!important;color:var(--ink)!important;border:1px solid #ccd8e2!important}.invoice-form-page select{cursor:pointer}.invoice-form-page input:focus,.invoice-form-page textarea:focus,.invoice-form-page select:focus{outline:2px solid #87c7ef;outline-offset:1px}
.invoice-sheet{display:none;background:#fff;max-width:1040px;margin:0 auto;box-shadow:0 8px 30px #16324a14;border-radius:10px;overflow:hidden}
.invoice-content{padding:22px 26px}.invoice-title{font-size:25px;color:var(--deep);margin:8px 0 14px;text-align:right}
.invoice-meta,.invoice-parties{width:100%;border-collapse:collapse;min-width:0;margin:12px 0}
.invoice-meta th,.invoice-meta td,.invoice-parties th,.invoice-parties td{border:1px solid #cbd5df;padding:8px;font-size:11px;vertical-align:top}
.invoice-meta th,.invoice-parties th{background:#eef5f9;color:#25435d}
.invoice-table{width:100%;border-collapse:collapse;min-width:0;margin-top:14px}.invoice-table th{background:#7fc4ef;color:#fff;padding:9px 6px;font-size:10px}.invoice-table td{border:1px solid #cbd5df;padding:7px 5px;font-size:11px}.invoice-table input{width:100%;min-width:0;border:0;padding:2px;font:inherit;background:transparent}
.invoice-bottom{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px}.invoice-box{border:1px solid #cbd5df;padding:12px;font-size:11px;min-height:90px}.invoice-box h3{margin:0 0 9px;color:var(--deep);font-size:12px}.invoice-summary{margin-left:auto;width:min(380px,100%);font-size:11px}.invoice-summary div{display:flex;justify-content:space-between;border-bottom:1px solid #dce5ec;padding:6px}.invoice-summary .invoice-grand{background:#7fc4ef;color:#fff;font-weight:800;font-size:14px}.invoice-qr{max-width:110px;max-height:110px;object-fit:contain;display:block;margin:8px 0}.invoice-actions{display:flex;gap:8px;flex-wrap:wrap;margin:0 0 14px}.invoice-sheet .brandbar{padding:0;display:block}.invoice-sheet .brandbar #invoiceHeaderBanner{display:block;width:100%;max-width:100%;height:auto;object-fit:contain}
body.invoice-mode .sheet{display:none!important}
@media(max-width:680px){.invoice-form-grid{grid-template-columns:1fr 1fr}.invoice-content{padding:12px}.invoice-bottom{grid-template-columns:1fr}}
@media print{body.invoice-mode .sheet{display:none!important}body.invoice-mode .invoice-sheet{display:block!important;max-width:none;box-shadow:none;border-radius:0}body.invoice-mode .invoice-content{padding:8px}body:not(.invoice-mode) .invoice-sheet{display:none!important}.invoice-form-page{display:none!important}.invoice-sheet .brandbar.has-banner{display:block;padding:0}.invoice-sheet .brandbar.has-banner #invoiceHeaderBanner{width:100%!important;max-width:100%!important;height:auto!important;max-height:none!important}}
.gst-toggle-field label{display:flex;align-items:center;gap:8px;font-weight:700}.gst-toggle-field input[type=checkbox]{width:auto!important;min-width:0;cursor:pointer}.gst-toggle-field small{display:block;color:var(--muted);font-size:11px;margin-top:5px}
.bank-qr-layout{display:flex;align-items:center;justify-content:space-between;gap:12px}.bank-qr-layout #outBankDetails{min-width:0;overflow-wrap:anywhere}.invoice-qr{flex:0 0 auto;width:88px;height:88px;max-width:88px;max-height:88px;object-fit:contain;margin:0}
body.invoice-hide-gst .invoice-sheet .gst-detail{display:none!important}
@media print{
 @page{size:A4;margin:5mm}
 body.invoice-mode .invoice-content{padding:3mm 4mm!important}
 .invoice-sheet .brandbar.has-banner #invoiceHeaderBanner{height:32mm!important;max-height:32mm!important;object-fit:contain!important}
 .invoice-sheet .brandbar:not(.has-banner){padding:2mm 3mm!important}
 .invoice-meta,.invoice-parties{margin:5px 0!important}
 .invoice-meta th,.invoice-meta td,.invoice-parties th,.invoice-parties td{padding:4px!important;font-size:9px!important;line-height:1.2!important}
 .invoice-table{margin-top:5px!important}
 .invoice-table th{padding:5px 4px!important;font-size:8px!important}
 .invoice-table td{padding:4px 3px!important;font-size:9px!important;line-height:1.15!important}
 .invoice-summary{font-size:9px!important}
 .invoice-summary div{padding:3px 5px!important}
 .invoice-summary .invoice-grand{font-size:11px!important}
 .invoice-box{padding:6px!important;font-size:9px!important;min-height:0!important}
 .invoice-box h3{margin-bottom:4px!important;font-size:10px!important}
 .invoice-bottom{gap:8px!important;margin-top:6px!important}
 .invoice-qr{width:22mm!important;height:22mm!important;max-width:22mm!important;max-height:22mm!important}
 .invoice-content > .invoice-box{margin-top:5px!important}
 .invoice-sheet .footer{padding:5px 8px!important;font-size:8px!important;margin-top:5px!important}
 .invoice-sheet .invoice-parties p{margin:4px 0!important;line-height:1.25!important}
 .invoice-sheet .invoice-title{font-size:18px!important;margin:3px 0 5px!important}
 .invoice-sheet .invoice-bottom,.invoice-sheet .invoice-box,.invoice-sheet .invoice-table tr{break-inside:avoid;page-break-inside:avoid}
}



/* Invoice print refinements: larger readable text, full-width header, saved-invoice tab. */
.invoice-sheet .brandbar.has-banner #invoiceHeaderBanner{display:block;width:100%;max-width:100%;height:auto;object-fit:fill;margin:0}
@media print{
 @page{size:A4;margin:5mm}
 .invoice-sheet .brandbar.has-banner{display:block!important;width:100%!important;padding:0!important;margin:0!important}
 .invoice-sheet .brandbar.has-banner #invoiceHeaderBanner{display:block!important;width:100%!important;max-width:100%!important;height:32mm!important;max-height:32mm!important;object-fit:fill!important;margin:0!important}
 .invoice-meta th,.invoice-meta td,.invoice-parties th,.invoice-parties td{font-size:11px!important;line-height:1.25!important;padding:4px!important}
 .invoice-table th{font-size:10px!important;padding:5px 4px!important}
 .invoice-table td{font-size:11px!important;line-height:1.2!important;padding:4px 3px!important}
 .invoice-summary{font-size:11px!important}
 .invoice-summary .invoice-grand{font-size:13px!important}
 .invoice-box{font-size:11px!important;padding:6px!important}
 .invoice-box h3{font-size:12px!important}
 .invoice-bottom{gap:8px!important;margin-top:6px!important}
}
</style></head>
<body>
<div class="app-shell">
<aside class="sidebar no-print">
  <div class="side-brand"><img id="appLogo" src="veera-solar-logo.png" alt="Veera Solar logo"><div><strong id="appBrandName">Veera Solar</strong><small>Solar Business Suite</small></div></div>
  <nav class="side-nav" aria-label="Main navigation">
    <button type="button" class="nav-item active" data-tab="dashboard">▦ <span>Dashboard</span></button>
    <button type="button" class="nav-item" data-tab="costing">⌁ <span>Solar Costing</span></button>
    <button type="button" class="nav-item" data-tab="quotation">▤ <span>Quotation</span></button>
    <button type="button" class="nav-item" data-tab="savedquotations">▤ <span>Saved Quotations</span></button>
    <button type="button" class="nav-item" data-tab="taxinvoice">▧ <span>Tax Invoice</span></button>
    <button type="button" class="nav-item" data-tab="savedinvoices">▧ <span>Saved Tax Invoices</span></button>
    <button type="button" class="nav-item" data-tab="materials">▦ <span>Material Master</span></button>
    <button type="button" class="nav-item" data-tab="settings">⚙ <span>Settings</span></button>
  </nav>
  <div class="side-foot">Reusable costing & quotation<br>Client-ready proposal builder</div>
</aside>
<div class="workspace">
<header class="app-topbar no-print">
  <div><div class="topbar-title" id="topbarCompany">Veera Solar</div><div class="topbar-subtitle" id="topbarContact">Velapur, Maharashtra · Add business contact details</div></div>
  <div class="topbar-actions"><button class="btn secondary" type="button" id="topPreviewBtn">Preview quotation</button><button class="btn" type="button" id="topPrintBtn">Print / Save PDF</button></div>
</header>
<div class="app">
<section class="tab-page no-print" id="dashboardPage">
  <h1 class="page-heading">Dashboard</h1>
  <p class="page-subtitle">Overview of your current solar quotation.</p>
  <div class="dashboard-cards">
    <div class="metric-card"><span>System Capacity</span><strong id="dashCapacity">3.3 kW</strong></div>
    <div class="metric-card"><span>Material Subtotal</span><strong id="dashSubtotal">₹0.00</strong></div>
    <div class="metric-card"><span>Total Quotation Value</span><strong id="dashTotal">₹0.00</strong></div>
    <div class="metric-card"><span>Payment Milestones</span><strong id="dashPaymentStatus">100%</strong></div>
  </div>
  <div class="dashboard-panel"><h2>Quick actions</h2><p>Manage your solar estimate, review client details, configure company branding, and preview the customer quotation.</p><div class="toolbar" style="justify-content:flex-start"><button class="btn" type="button" data-goto="costing">Edit costing</button><button class="btn secondary" type="button" data-goto="quotation">Open quotation</button><button class="btn secondary" type="button" data-goto="settings">Company settings</button></div></div>
</section>
<section class="tab-page no-print" id="savedQuotationsPage" style="display:none">
  <h1 class="page-heading">Saved Quotations</h1>
  <p class="page-subtitle">Every quotation you save appears here. Open one to edit it, or import it into Tax Invoice.</p>
  <div class="dashboard-panel">
    <div class="toolbar" style="justify-content:space-between;align-items:center"><h2 style="margin:0">Quotation Records</h2><button class="btn" type="button" data-goto="quotation">Create / edit quotation</button></div>
    <div id="savedQuotationList" style="margin-top:14px"></div>
    <p class="note" style="margin-top:12px">Records are saved in this browser on this device. They are not yet shared between different browsers or computers.</p>
  </div>
</section>
<section class="tab-page no-print" id="materialsPage" style="display:none">
  <h1 class="page-heading">Material Master</h1>
  <p class="page-subtitle">Maintain the reusable materials and rates used in the current quotation.</p>
  <div class="dashboard-panel"><h2>Current quotation materials</h2><p class="note">Edit material descriptions, quantities, units and rates in Solar Costing. Changes recalculate automatically.</p><div id="materialMasterList"></div><button type="button" class="btn" data-goto="costing">Manage material rates</button></div>
</section>
<section class="panel no-print" id="brandSettings" style="max-width:900px;margin:0 auto 16px">
<h2>Header & Footer Settings</h2>
<p class="note">Upload a logo or full-width header, then enter the company name and contact details. Images are saved in the project folder, not browser storage.</p>
<div class="grid">
<div class="field"><label>Company / Business Name</label><input id="brandName" value="VEERA SOLAR"></div>
<div class="field"><label>Location / Tagline</label><input id="brandLocation" value="VELAPUR"></div>
<div class="field"><label>Business Mobile Number</label><input id="brandMobile" placeholder="+91 ..."></div>
<div class="field"><label>Business Email</label><input id="brandEmail" type="email" placeholder="business@example.com"></div>
<div class="field"><label>Footer Address</label><input id="brandAddress" placeholder="Velapur, Maharashtra"></div>
<div class="field"><label>Authorized Person (Prepared By)</label><input id="brandAuthorized" placeholder="Authorized person's name"></div>
<div class="field"><label>Business GSTIN (optional)</label><input id="brandGSTIN" placeholder="GSTIN"></div>
<div class="field"><label>Tax Invoice QR Code Upload</label><input id="qrUpload" type="file" accept="image/png,image/jpeg,image/webp"><img id="qrThumb" alt="QR code preview" style="display:block;max-width:90px;max-height:90px;object-fit:contain;margin-top:6px"></div>
<div class="field"><label>Header Image (optional)</label><input id="headerUpload" type="file" accept="image/png,image/jpeg,image/webp"><img id="headerThumb" alt="Header preview" style="display:block;max-width:220px;max-height:65px;object-fit:contain;margin-top:6px"></div>
<div class="field"><label>Company Logo</label><input id="logoUpload" type="file" accept="image/png,image/jpeg,image/webp"><img id="logoThumb" alt="Logo preview" style="display:block;max-width:100px;max-height:65px;object-fit:contain;margin-top:6px"></div>
</div>
<div class="toolbar" style="justify-content:flex-start;margin:12px 0 0"><button class="btn" type="button" id="applyBrand">Apply Header & Footer</button><button class="btn secondary" type="button" id="resetBrand">Reset brand fields</button><span class="status" id="brandStatus" aria-live="polite"></span></div>
</section>
<div class="toolbar no-print" id="quotationToolbar"><button class="btn secondary" type="button" id="resetBtn">Reset sample</button><button class="btn secondary" type="button" id="saveBtn">Save quotation</button><span id="quoteSaveStatus" class="status" aria-live="polite"></span><button class="btn secondary" type="button" id="previewBtn">Preview quotation</button><button class="btn" type="button" onclick="window.print()">Print / Save PDF</button></div>
<div class="sheet">
<header class="brandbar" id="quotationHeader"><img id="headerBanner" alt="Quotation header" style="display:none;width:100%;max-width:100%;height:auto;max-height:none;object-fit:contain;flex:0 0 100%"><div class="brand" id="brandIdentity"><img id="quotationLogo" src="veera-solar-logo.png" alt="Company logo"><div><h1 id="quotationBrandName"><?= htmlspecialchars($brand) ?></h1><p id="quotationBrandLocation"><?= htmlspecialchars($location) ?></p></div></div><div class="brandcontact"><strong>Solar Proposal & Quotation</strong><br>Residential • Commercial • On-grid Solar<br><span id="headerPhone">Contact: Add business phone</span><br><span id="headerEmail"></span></div></header>
<main class="content">
<div class="eyebrow">Customized solar solution</div><h2 class="title">Solar Proposal & Commercial Quotation</h2><p class="intro">A clear, itemized estimate prepared for your site and system requirements.</p>
<section class="panel quotation-section" data-section="client"><h2>01 / Client & Project Details</h2><div class="grid">
<div class="field"><label>Quotation Number</label><input id="quoteNo" value="VS-2026-001"></div><div class="field"><label>Quotation Date</label><input id="quoteDate" type="date"></div><div class="field"><label>Valid Until</label><input id="validUntil" type="date"></div>
<div class="field"><label>Client Name</label><input id="clientName" value="Pise Sir" placeholder="Client full name"></div><div class="field"><label>Mobile Number</label><input id="clientPhone" placeholder="Client contact number"></div><div class="field"><label>System Capacity (kW)</label><input id="capacity" type="number" min="0.1" step="0.1" value="3.3"></div>
<div class="field full"><label>Installation Address / Site Location</label><textarea id="siteAddress" placeholder="Full installation address">Velapur</textarea></div>
<div class="field"><label>System Type</label><select id="systemType"><option>On-grid</option><option>Off-grid</option><option>Hybrid</option></select></div><div class="field"><label>Panel Wattage (W)</label><input id="panelWatt" type="number" min="1" value="550"></div><div class="field"><label>Estimated Panel Count</label><input id="panelCount" readonly value="6"></div>
</div></section>
<section class="panel quotation-section" id="costingPanel" data-section="costing"><h2>02 / Material & Service Price Breakdown</h2><p class="note">Edit descriptions, quantities and unit rates. Amounts recalculate automatically. Quantities and rates below are sample values—replace them with approved supplier prices.</p><div class="tablewrap"><table id="itemsTable"><thead><tr><th style="width:42%">Material / Service</th><th>Qty</th><th>Unit</th><th>Rate (₹)</th><th>Amount (₹)</th><th class="no-print"> </th></tr></thead><tbody id="itemsBody"></tbody></table></div><button type="button" class="addrow no-print" id="addRow">+ Add material / service</button>
<div class="grid" style="margin-top:18px"><div class="field"><label>GST (%)</label><input id="gstRate" type="number" min="0" max="100" step="0.01" value="8.9"></div><div class="field"><label>Discount (₹)</label><input id="discount" type="number" min="0" step="1" value="0"></div><div class="field"><label>Subsidy (₹, if applicable)</label><input id="subsidy" type="number" min="0" step="1" value="0"></div></div>
<div class="totals"><div class="totalrow"><span>Subtotal</span><strong id="subtotal">₹0.00</strong></div><div class="totalrow"><span>GST</span><strong id="gstAmount">₹0.00</strong></div><div class="totalrow"><span>Discount</span><strong id="discountAmount">− ₹0.00</strong></div><div class="totalrow"><span>Total Project Cost</span><strong id="grossTotal">₹0.00</strong></div><div class="totalrow"><span>Less Subsidy (indicative)</span><strong id="subsidyAmount">− ₹0.00</strong></div><div class="totalrow grand"><span>Effective Cost</span><strong id="netTotal">₹0.00</strong></div></div>
</section>
<section class="panel quotation-section" data-section="payment"><h2>03 / Payment Schedule</h2><div class="grid"><div class="field"><label>Advance (%)</label><input id="advancePct" type="number" min="0" max="100" value="20"></div><div class="field"><label>Installation Stage (%)</label><input id="installPct" type="number" min="0" max="100" value="70"></div><div class="field"><label>Commissioning (%)</label><input id="commissionPct" type="number" min="0" max="100" value="10"></div></div><div class="grid" style="margin-top:12px"><div class="field"><label>Advance Amount</label><input id="advanceAmt" readonly></div><div class="field"><label>Installation Amount</label><input id="installAmt" readonly></div><div class="field"><label>Commissioning Amount</label><input id="commissionAmt" readonly></div></div>
<div class="payment-total"><span>Total Quotation Value</span><strong id="paymentTotal">₹0.00</strong></div>
<p id="paymentWarning" class="payment-match-status mismatch" role="status" aria-live="polite">Price must be equal to total price.</p></section>
<section class="panel quotation-section" data-section="terms"><h2>04 / Scope, Warranty & Terms</h2><div class="grid"><div class="field full"><label>Scope of Work</label><textarea id="scope">Site assessment, supply of listed equipment, installation, testing and commissioning as agreed in the final work order.</textarea></div><div class="field"><label>Solar Panel Warranty</label><input id="panelWarranty" value="As per manufacturer warranty"></div><div class="field"><label>Inverter Warranty</label><input id="inverterWarranty" value="As per manufacturer warranty"></div><div class="field"><label>Quotation Validity / Delivery</label><input id="delivery" value="Validity and schedule to be confirmed"></div><div class="field full"><label>Terms & Conditions / Exclusions</label><textarea id="terms">Final pricing is subject to site survey and written confirmation. Any civil work, meter charges, statutory fees or additional work not listed above will be quoted separately. Subsidy is subject to current government rules, eligibility and approval; it is not guaranteed by this quotation. GST rate and tax treatment must be verified before issue.</textarea></div></div></section>
<section class="panel quotation-section" data-section="prepared"><h2>05 / Prepared By</h2><div class="grid"><div class="field"><label>Authorized Person</label><input id="preparedBy" placeholder="Name"></div><div class="field"><label>Business Phone</label><input id="businessPhone" placeholder="Business contact number"></div><div class="field"><label>Email</label><input id="businessEmail" type="email" placeholder="Business email"></div><div class="field full"><label>Business Address / GSTIN (optional)</label><input id="businessAddress" placeholder="Address and GSTIN, if applicable"></div></div><div class="status no-print" id="apiStatus" aria-live="polite"></div></section>
</main><footer class="footer"><div><strong id="footerBrandName">VEERA SOLAR VELAPUR</strong><br><span id="footerTagline">Solar energy solutions tailored to your site.</span></div><div id="footerContact">Contact details can be configured above.</div><div>Quotation • <span id="footerQuoteNo">VS-2026-001</span></div></footer>
</div>
<section class="tab-page no-print" id="savedInvoicesPage" style="display:none">
  <h1 class="page-heading">Saved Tax Invoices</h1>
  <p class="page-subtitle">Invoices saved from this browser appear here. Open an invoice to edit it, or delete a saved record.</p>
  <div class="dashboard-panel">
    <div class="toolbar" style="justify-content:space-between;align-items:center"><h2 style="margin:0">Invoice Records</h2><button class="btn" type="button" data-goto="taxinvoice">Create / edit tax invoice</button></div>
    <div id="savedInvoiceList" style="margin-top:14px"></div>
    <p class="note" style="margin-top:12px">Invoices are stored in this browser on this device. They are not automatically shared with other browsers or computers.</p>
  </div>
</section>
<section class="tab-page no-print invoice-form-page" id="taxInvoicePage" style="display:none">
  <h1 class="page-heading">Tax Invoice</h1><p class="page-subtitle">Create an invoice using company branding and details from Settings. Invoice lines can be imported from the current quotation.</p>
  <div class="dashboard-panel">
    <div class="invoice-form-grid" style="margin-bottom:12px"><div class="field"><label for="invoiceFromQuotation">From Quotation</label><select id="invoiceFromQuotation"><option value="">Choose a saved quotation…</option><option value="__current__">Current quotation on screen</option></select></div><div class="field" style="grid-column:span 2;align-self:end"><p class="note" style="margin:0 0 8px">Choose a saved quotation to automatically fill invoice customer details, project details, items, rates and GST. You can edit the invoice fields afterward.</p></div></div>
    <div class="invoice-actions"><button type="button" class="btn secondary" id="importQuoteInvoice">Import selected quotation</button><button type="button" class="btn secondary" id="saveInvoiceBtn">Save invoice</button><button type="button" class="btn secondary" id="previewInvoiceBtn">Preview invoice</button><button type="button" class="btn" id="printInvoiceBtn">Print / Save PDF</button></div>
    <div class="invoice-form-grid">
      <div class="field"><label>Invoice No.</label><input id="invNo" value="INV-0001"></div><div class="field"><label>Invoice Date</label><input id="invDate" type="date"></div><div class="field"><label>Delivery Challan</label><input id="invChallan" placeholder="Optional"></div>
      <div class="field"><label>Building / Project</label><input id="invBuilding" placeholder="Building / project"></div><div class="field"><label>W/O No.</label><input id="invWo" placeholder="Work order number"></div><div class="field"><label>Nature & Description of Service</label><input id="invNature" value="Supply and installation of solar power system"></div>
    </div>
    <div class="invoice-form-grid" style="margin-top:12px">
      <div class="field"><label>Receiver (Billed To) Name</label><input id="billName" placeholder="Customer name"></div><div class="field"><label>Receiver Phone</label><input id="billPhone" placeholder="Customer phone"></div><div class="field"><label>Receiver GSTIN</label><input id="billGSTIN" placeholder="Customer GSTIN"></div>
      <div class="field"><label>Receiver Address</label><textarea id="billAddress" placeholder="Billing address"></textarea></div><div class="field"><label>Consignee (Shipped To) Name</label><input id="shipName" placeholder="Consignee name"></div><div class="field"><label>Consignee GSTIN</label><input id="shipGSTIN" placeholder="Consignee GSTIN"></div>
      <div class="field"><label>Consignee Address</label><textarea id="shipAddress" placeholder="Shipping address"></textarea></div><div class="field"><label>GST Rate (%)</label><input id="invGstRate" type="number" min="0" max="100" step="0.01" value="18"></div><div class="field"><label>Round Off (₹)</label><input id="invRoundOff" type="number" step="0.01" value="0"></div><div class="field gst-toggle-field"><label><input id="showGstDetails" type="checkbox" checked> Show GST details on invoice</label><small>Turn off to make the printed invoice more compact.</small></div>
    </div>
    <h2 style="margin:20px 0 8px;color:var(--deep)">Invoice items</h2><div class="tablewrap"><table class="invoice-table" id="invEditTable"><thead><tr><th>SN</th><th>Particulars</th><th>HSN/SAC</th><th>Qty</th><th>Unit</th><th>Rate (₹)</th><th>Taxable Amount (₹)</th><th class="no-print"></th></tr></thead><tbody id="invItemsBody"></tbody></table></div>
    <button class="addrow" type="button" id="addInvoiceRow">+ Add invoice item</button>
    <div class="invoice-form-grid" style="margin-top:14px"><div class="field"><label>Bank Name</label><input id="invBank" placeholder="Bank name"></div><div class="field"><label>Account Number</label><input id="invAccount" placeholder="Account number"></div><div class="field"><label>IFSC</label><input id="invIFSC" placeholder="IFSC code"></div><div class="field"><label>Branch</label><input id="invBranch" placeholder="Branch"></div><div class="field"><label>Authorized Signatory</label><input id="invSignatory" placeholder="Auto-filled from Settings"></div></div>
    <p class="note">Verify tax rates, HSN/SAC, bank information and invoice values before issuing a tax invoice.</p><div id="invoiceStatus" class="status" aria-live="polite"></div>
  </div>
</section>
</div>
<div class="invoice-sheet" id="invoiceSheet">
  <header class="brandbar" id="invoiceHeader"><img id="invoiceHeaderBanner" alt="Company quotation header" style="display:none;width:100%;max-width:100%;height:auto;object-fit:contain"><div class="brand" id="invoiceBrandIdentity"><img id="invoiceLogo" src="veera-solar-logo.png" alt="Company logo"><div><h1 id="invoiceBrandName">VEERA SOLAR</h1><p id="invoiceBrandLocation">VELAPUR</p></div></div><div class="brandcontact" id="invoiceBrandContact"></div></header>
  <div class="invoice-content">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px"><div><strong id="invoiceCompanyTitle">VEERA SOLAR</strong><div id="invoiceCompanyAddress"></div><div id="invoiceCompanyGSTIN" class="gst-detail"></div></div><h2 class="invoice-title">TAX INVOICE</h2></div>
    <table class="invoice-meta"><tbody><tr><th>Name</th><td id="outBillName"></td><th>Building</th><td id="outBuilding"></td></tr><tr><th>Invoice No.</th><td id="outInvNo"></td><th>W/O No.</th><td id="outWo"></td></tr><tr><th>Date</th><td id="outInvDate"></td><th>Nature & Description of Service</th><td id="outNature"></td></tr></tbody></table>
    <table class="invoice-parties"><tbody><tr><td style="width:50%"><strong>Details of Receiver (Billed To)</strong><p><b>Name:</b> <span id="outBillName2"></span><br><b>Address:</b> <span id="outBillAddress"></span><br><b>Phone:</b> <span id="outBillPhone"></span><br><span class="gst-detail"><b>GSTIN:</b> <span id="outBillGSTIN"></span></span></p></td><td style="width:50%"><strong>Details of Consignee (Shipped To)</strong><p><b>Name:</b> <span id="outShipName"></span><br><b>Address:</b> <span id="outShipAddress"></span><br><span class="gst-detail"><b>GSTIN:</b> <span id="outShipGSTIN"></span></span></p></td></tr></tbody></table>
    <table class="invoice-table"><thead><tr><th>SN</th><th>PARTICULARS</th><th class="gst-detail">HSN/SAC</th><th>QTY</th><th>UNIT</th><th>RATE</th><th>TAXABLE AMOUNT</th></tr></thead><tbody id="outInvItems"></tbody></table>
    <div class="invoice-summary"><div><span>Taxable Amount</span><strong id="outTaxable"></strong></div><div class="gst-detail"><span>CGST</span><strong id="outCGST"></strong></div><div class="gst-detail"><span>SGST</span><strong id="outSGST"></strong></div><div class="gst-detail"><span>IGST (if applicable)</span><strong id="outIGST">—</strong></div><div><span>Round Off</span><strong id="outRoundOff"></strong></div><div class="invoice-grand"><span>TOTAL INVOICE AMOUNT</span><strong id="outInvGrand"></strong></div></div>
    <div class="invoice-box" style="margin-top:12px"><strong>Amount in Words</strong><p id="outAmountWords"></p></div>
    <div class="invoice-bottom"><div class="invoice-box"><h3>BANK DETAILS</h3><div class="bank-qr-layout"><div id="outBankDetails"></div><img id="outInvoiceQR" class="invoice-qr" alt="Payment QR code" style="display:none"></div></div><div class="invoice-box"><h3 id="outForCompany">For VEERA SOLAR</h3><p><strong id="outSignatory"></strong></p><p>Authorized Signatory</p><p>Signature: __________________</p><p>Date: __________________</p></div></div>
    <footer class="footer"><div><strong id="invoiceFooterBrand">VEERA SOLAR</strong><br><span id="invoiceFooterAddress"></span></div><div id="invoiceFooterContact"></div><div>Invoice • <span id="invoiceFooterNo"></span></div></footer>
  </div>
</div>

<div class="preview-modal" id="previewModal"><div class="preview-toolbar"><strong>Quotation Preview</strong><div><button class="btn" type="button" id="previewPrint">Print / Save PDF</button> <button class="btn secondary" type="button" id="closePreview">Close</button></div></div><div class="preview-paper" id="previewPaper"></div></div>
<script>
const quoteSaveKey='veeraSolarSavedQuotationV1',savedQuotesKey='veeraSolarSavedQuotationsV1',invoiceSaveKey='veeraSolarSavedInvoiceV1';
function readSavedQuotations(){try{const v=JSON.parse(localStorage.getItem(savedQuotesKey)||'[]');return Array.isArray(v)?v:[];}catch(e){return [];}}
function quotationSnapshot(){calculate();const d={};document.querySelectorAll('.sheet input,.sheet textarea,.sheet select').forEach(e=>{if(e.id)d[e.id]=e.value;});d.items=[...$('itemsBody').rows].map(tr=>({desc:tr.querySelector('.desc').value,qty:tr.querySelector('.qty').value,unit:tr.querySelector('.unit').value,rate:tr.querySelector('.rate').value}));d.totalValue=$('netTotal').textContent;d._recordKey=String(d.quoteNo||'').trim()||('QUOTE-'+Date.now());return d;}
function populateQuotationSelector(preselect=''){const sel=$('invoiceFromQuotation');if(!sel)return;let list=readSavedQuotations();try{const legacy=JSON.parse(localStorage.getItem(quoteSaveKey)||'null');if(legacy&&typeof legacy==='object'){const key=String(legacy.quoteNo||'').trim()||'LEGACY-QUOTATION';if(!list.some(q=>(q._recordKey||String(q.quoteNo||'').trim())===key)){legacy._recordKey=key;list.push(legacy);localStorage.setItem(savedQuotesKey,JSON.stringify(list));}}}catch(e){}const prior=preselect||sel.value;sel.innerHTML='';const blank=document.createElement('option');blank.value='';blank.textContent='Choose a saved quotation…';sel.appendChild(blank);const current=document.createElement('option');current.value='__current__';current.textContent='Current quotation on screen';sel.appendChild(current);list.forEach((q,i)=>{const opt=document.createElement('option');opt.value=q._recordKey||String(q.quoteNo||('saved-'+i));opt.textContent=[q.quoteNo||'Quotation',q.clientName||'Unnamed client',q.quoteDate||''].filter(Boolean).join(' — ');sel.appendChild(opt);});if(prior&&[...sel.options].some(o=>o.value===prior))sel.value=prior;}
function saveQuotation(){const d=quotationSnapshot();try{localStorage.setItem(quoteSaveKey,JSON.stringify(d));let list=readSavedQuotations();const key=d._recordKey;const idx=list.findIndex(q=>(q._recordKey||String(q.quoteNo||'').trim())===key);if(idx>=0)list[idx]=d;else list.push(d);if(list.length>100)list=list.slice(-100);localStorage.setItem(savedQuotesKey,JSON.stringify(list));populateQuotationSelector(key);renderSavedQuotations();$('quoteSaveStatus').textContent='Quotation saved in this browser and added to Saved Quotations.';$('quoteSaveStatus').style.color='var(--green)';}catch(e){$('quoteSaveStatus').textContent='Could not save quotation in browser storage.';$('quoteSaveStatus').style.color='#b43a2d';}}
function renderSavedQuotations(){
 const host=$('savedQuotationList'); if(!host)return;
 const list=readSavedQuotations().slice().reverse();
 if(!list.length){host.innerHTML='<p class="note">No saved quotations yet. Open Quotation, complete the details, and click Save quotation.</p>';return;}
 host.innerHTML='<div class="tablewrap"><table><thead><tr><th>Quotation No.</th><th>Client</th><th>Date</th><th>Capacity</th><th>Total Value</th><th>Actions</th></tr></thead><tbody>'+list.map((q)=>{
  const key=q._recordKey||String(q.quoteNo||'').trim();
  const total=q.totalValue||q.netTotal||q.paymentTotal||'';
  return '<tr><td>'+escapeHtml(q.quoteNo||'—')+'</td><td>'+escapeHtml(q.clientName||'—')+'</td><td>'+escapeHtml(q.quoteDate||'—')+'</td><td>'+escapeHtml(q.capacity||'—')+' kW</td><td>'+escapeHtml(total||'Open to recalculate')+'</td><td style="white-space:nowrap"><button type="button" class="btn secondary saved-quote-open" data-record-key="'+escapeHtml(key)+'">Open / Edit</button> <button type="button" class="btn secondary saved-quote-invoice" data-record-key="'+escapeHtml(key)+'">Create invoice</button> <button type="button" class="btn secondary saved-quote-delete" data-record-key="'+escapeHtml(key)+'">Delete</button></td></tr>';
 }).join('')+'</tbody></table></div>';
 host.querySelectorAll('.saved-quote-open').forEach(b=>b.addEventListener('click',()=>loadQuotationRecord(b.dataset.recordKey)));
 host.querySelectorAll('.saved-quote-invoice').forEach(b=>b.addEventListener('click',()=>{populateQuotationSelector(b.dataset.recordKey);$('invoiceFromQuotation').value=b.dataset.recordKey;importQuotationItems();setTab('taxinvoice');window.scrollTo({top:0,behavior:'smooth'});}));
 host.querySelectorAll('.saved-quote-delete').forEach(b=>b.addEventListener('click',()=>{const key=b.dataset.recordKey;if(!confirm('Delete this saved quotation?'))return;const next=readSavedQuotations().filter(q=>(q._recordKey||String(q.quoteNo||'').trim())!==key);localStorage.setItem(savedQuotesKey,JSON.stringify(next));renderSavedQuotations();populateQuotationSelector();}));
}
function loadQuotationRecord(key){
 const q=readSavedQuotations().find(x=>(x._recordKey||String(x.quoteNo||'').trim())===key);if(!q)return;
 document.querySelectorAll('.sheet input,.sheet textarea,.sheet select').forEach(el=>{if(el.id&&Object.prototype.hasOwnProperty.call(q,el.id)&&typeof q[el.id]!=='object')el.value=q[el.id];});
 if(Array.isArray(q.items)){$('itemsBody').innerHTML='';q.items.forEach(x=>addItem({desc:x.desc||'',qty:Number(x.qty)||0,unit:x.unit||'Nos',rate:Number(x.rate)||0}));}
 calculate();setTab('quotation');$('quoteSaveStatus').textContent='Opened saved quotation '+(q.quoteNo||'')+'. Edit it and click Save quotation to update the saved record.';$('quoteSaveStatus').style.color='var(--green)';window.scrollTo({top:0,behavior:'smooth'});
}
function loadSavedQuotation(){try{const d=JSON.parse(localStorage.getItem(quoteSaveKey)||'null');if(!d)return;Object.entries(d).forEach(([id,v])=>{const el=$(id);if(el&&typeof v!=='object')el.value=v;});if(Array.isArray(d.items)){$('itemsBody').innerHTML='';d.items.forEach(x=>addItem({desc:x.desc,qty:Number(x.qty)||0,unit:x.unit||'Nos',rate:Number(x.rate)||0}));}}catch(e){}}
function addInvoiceItem(x={desc:'Solar equipment / service',hsn:'',qty:1,unit:'Nos',rate:0}){const tr=document.createElement('tr');tr.innerHTML='<td class="inv-sn"></td><td><input class="inv-desc" aria-label="Particulars"></td><td><input class="inv-hsn" aria-label="HSN/SAC"></td><td><input class="inv-qty" type="number" min="0" step="0.01" aria-label="Quantity"></td><td><input class="inv-unit" aria-label="Unit"></td><td><input class="inv-rate" type="number" min="0" step="0.01" aria-label="Rate"></td><td class="inv-amount">₹0.00</td><td class="no-print"><button type="button" class="remove inv-remove">×</button></td>';tr.querySelector('.inv-desc').value=x.desc||'';tr.querySelector('.inv-hsn').value=x.hsn||'';tr.querySelector('.inv-qty').value=x.qty??1;tr.querySelector('.inv-unit').value=x.unit||'Nos';tr.querySelector('.inv-rate').value=x.rate??0;tr.querySelectorAll('input').forEach(e=>e.addEventListener('input',calculateInvoice));tr.querySelector('.inv-remove').addEventListener('click',()=>{tr.remove();calculateInvoice()});$('invItemsBody').appendChild(tr);calculateInvoice();}
function wordsIN(value){let n=Math.round(Number(value)||0);if(!n)return 'Zero Rupees Only';const a=['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'],t=['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];const u=x=>x<20?a[x]:t[Math.floor(x/10)]+(x%10?' '+a[x%10]:'');const h=x=>(x>=100?a[Math.floor(x/100)]+' Hundred '+(x%100?u(x%100):''):u(x)).trim();let out=[];let c=Math.floor(n/10000000);n%=10000000;let l=Math.floor(n/100000);n%=100000;let th=Math.floor(n/1000);n%=1000;if(c)out.push(h(c)+' Crore');if(l)out.push(h(l)+' Lakh');if(th)out.push(h(th)+' Thousand');if(n)out.push(h(n));return out.join(' ')+' Rupees Only';}
function calculateInvoice(){let taxable=0;[...$('invItemsBody').rows].forEach((tr,i)=>{tr.querySelector('.inv-sn').textContent=i+1;const q=Math.max(0,Number(tr.querySelector('.inv-qty').value)||0),r=Math.max(0,Number(tr.querySelector('.inv-rate').value)||0),a=Math.round(q*r*100)/100;taxable+=a;tr.querySelector('.inv-amount').textContent=money(a);});const rate=Math.max(0,Math.min(100,Number($('invGstRate').value)||0)),cgst=taxable*rate/200,sgst=taxable*rate/200,round=Number($('invRoundOff').value)||0,grand=Math.round((taxable+cgst+sgst+round)*100)/100;$('outTaxable').textContent=money(taxable);$('outCGST').textContent=money(cgst);$('outSGST').textContent=money(sgst);$('outIGST').textContent='—';$('outRoundOff').textContent=money(round);$('outInvGrand').textContent=money(grand);$('outAmountWords').textContent=wordsIN(grand);$('outInvItems').innerHTML=[...$('invItemsBody').rows].map((tr,i)=>'<tr><td>'+(i+1)+'</td><td>'+escapeHtml(tr.querySelector('.inv-desc').value)+'</td><td class="gst-detail">'+escapeHtml(tr.querySelector('.inv-hsn').value)+'</td><td>'+escapeHtml(tr.querySelector('.inv-qty').value)+'</td><td>'+escapeHtml(tr.querySelector('.inv-unit').value)+'</td><td style="text-align:right">'+money(tr.querySelector('.inv-rate').value)+'</td><td style="text-align:right">'+tr.querySelector('.inv-amount').textContent+'</td></tr>').join('');const map={outBillName:'billName',outBillName2:'billName',outBuilding:'invBuilding',outInvNo:'invNo',outWo:'invWo',outInvDate:'invDate',outNature:'invNature',outBillAddress:'billAddress',outBillPhone:'billPhone',outBillGSTIN:'billGSTIN',outShipName:'shipName',outShipAddress:'shipAddress',outShipGSTIN:'shipGSTIN'};Object.entries(map).forEach(([out,id])=>$(out).textContent=$(id).value);$('invoiceFooterNo').textContent=$('invNo').value;$('outSignatory').textContent=$('invSignatory').value;$('outBankDetails').innerHTML=['Bank: '+$('invBank').value,'Account No.: '+$('invAccount').value,'IFSC: '+$('invIFSC').value,'Branch: '+$('invBranch').value].map(escapeHtml).join('<br>');}
function readSavedInvoices(){try{const v=JSON.parse(localStorage.getItem('veeraSolarSavedInvoicesV1')||'[]');return Array.isArray(v)?v:[];}catch(e){return [];}}
function invoiceSnapshot(){const d={};document.querySelectorAll('#taxInvoicePage input,#taxInvoicePage textarea,#taxInvoicePage select').forEach(e=>{if(e.id)d[e.id]=e.type==='checkbox'?e.checked:e.value;});d.items=[...$('invItemsBody').rows].map(tr=>({desc:tr.querySelector('.inv-desc').value,hsn:tr.querySelector('.inv-hsn').value,qty:tr.querySelector('.inv-qty').value,unit:tr.querySelector('.inv-unit').value,rate:tr.querySelector('.inv-rate').value}));d._recordKey=String(d.invNo||'').trim()||('INVOICE-'+Date.now());d._savedAt=new Date().toISOString();calculateInvoice();d.totalValue=$('outInvGrand').textContent;return d;}
function saveInvoice(){const d=invoiceSnapshot();try{localStorage.setItem(invoiceSaveKey,JSON.stringify(d));let list=readSavedInvoices();const key=d._recordKey;const idx=list.findIndex(x=>(x._recordKey||String(x.invNo||'').trim())===key);if(idx>=0)list[idx]=d;else list.push(d);if(list.length>200)list=list.slice(-200);localStorage.setItem('veeraSolarSavedInvoicesV1',JSON.stringify(list));$('invoiceStatus').textContent='Invoice '+(d.invNo||'')+' saved and added to Saved Tax Invoices.';$('invoiceStatus').style.color='var(--green)';renderSavedInvoices();}catch(e){$('invoiceStatus').textContent='Could not save invoice in browser storage.';$('invoiceStatus').style.color='#b43a2d';}}
function renderSavedInvoices(){const host=$('savedInvoiceList');if(!host)return;let list=readSavedInvoices();try{const draft=JSON.parse(localStorage.getItem(invoiceSaveKey)||'null');if(draft&&!list.some(x=>(x._recordKey||String(x.invNo||'').trim())===(draft._recordKey||String(draft.invNo||'').trim())))list.push({...draft,_recordKey:draft._recordKey||String(draft.invNo||'LEGACY-INVOICE')});}catch(e){}if(!list.length){host.innerHTML='<p class="note">No saved tax invoices yet. Open Tax Invoice and click Save invoice.</p>';return;}host.innerHTML='<div class="tablewrap"><table><thead><tr><th>Invoice No.</th><th>Customer</th><th>Date</th><th>Total</th><th>Actions</th></tr></thead><tbody>'+list.slice().reverse().map(d=>{const key=d._recordKey||String(d.invNo||'');return '<tr><td>'+escapeHtml(d.invNo||'—')+'</td><td>'+escapeHtml(d.billName||'—')+'</td><td>'+escapeHtml(d.invDate||'—')+'</td><td>'+escapeHtml(d.totalValue||'—')+'</td><td style="white-space:nowrap"><button type="button" class="btn secondary saved-invoice-open" data-record-key="'+escapeHtml(key)+'">Open / Edit</button> <button type="button" class="btn secondary saved-invoice-delete" data-record-key="'+escapeHtml(key)+'">Delete</button></td></tr>';}).join('')+'</tbody></table></div>';host.querySelectorAll('.saved-invoice-open').forEach(b=>b.addEventListener('click',()=>loadInvoiceRecord(b.dataset.recordKey)));host.querySelectorAll('.saved-invoice-delete').forEach(b=>b.addEventListener('click',()=>{const key=b.dataset.recordKey;if(!confirm('Delete this saved invoice?'))return;const next=readSavedInvoices().filter(x=>(x._recordKey||String(x.invNo||'').trim())!==key);localStorage.setItem('veeraSolarSavedInvoicesV1',JSON.stringify(next));try{const draft=JSON.parse(localStorage.getItem(invoiceSaveKey)||'null');if(draft&&(draft._recordKey||String(draft.invNo||'').trim())===key)localStorage.removeItem(invoiceSaveKey);}catch(e){}renderSavedInvoices();}));}
function loadInvoiceRecord(key){const d=readSavedInvoices().find(x=>(x._recordKey||String(x.invNo||'').trim())===key)||(()=>{try{const x=JSON.parse(localStorage.getItem(invoiceSaveKey)||'null');return x&&(x._recordKey||String(x.invNo||'').trim())===key?x:null;}catch(e){return null;}})();if(!d)return;Object.entries(d).forEach(([id,v])=>{const el=$(id);if(el&&typeof v!=='object'){if(el.type==='checkbox')el.checked=!!v;else el.value=v;}});if(Array.isArray(d.items)){$('invItemsBody').innerHTML='';d.items.forEach(x=>addInvoiceItem({desc:x.desc||'',hsn:x.hsn||'',qty:x.qty??1,unit:x.unit||'Nos',rate:x.rate??0}));}localStorage.setItem(invoiceSaveKey,JSON.stringify(d));calculateInvoice();applyGstVisibility();setTab('taxinvoice');$('invoiceStatus').textContent='Opened saved invoice '+(d.invNo||'')+'. Edit it and click Save invoice to update the record.';$('invoiceStatus').style.color='var(--green)';window.scrollTo({top:0,behavior:'smooth'});}
function loadSavedInvoice(){try{const d=JSON.parse(localStorage.getItem(invoiceSaveKey)||'null');if(!d)return;Object.entries(d).forEach(([id,v])=>{const el=$(id);if(el&&typeof v!=='object'){if(el.type==='checkbox')el.checked=!!v;else el.value=v;}});if(Array.isArray(d.items)){$('invItemsBody').innerHTML='';d.items.forEach(addInvoiceItem);}}catch(e){}calculateInvoice();}
function importQuotationData(q,sourceLabel='quotation'){if(!q){$('invoiceStatus').textContent='Select a saved quotation or choose Current quotation on screen.';$('invoiceStatus').style.color='#b43a2d';return;}const val=(id)=>String(q[id]??'');$('billName').value=val('clientName');$('billPhone').value=val('clientPhone');$('billGSTIN').value='';$('billAddress').value=val('siteAddress');$('shipName').value=val('clientName');$('shipGSTIN').value='';$('shipAddress').value=val('siteAddress');$('invNo').value=val('quoteNo').replace(/^VS/i,'INV')||'INV-0001';$('invDate').value=val('quoteDate')||$('invDate').value;$('invBuilding').value=val('siteAddress');$('invWo').value='';$('invChallan').value='';const system=val('systemType')||'Solar';const capacity=val('capacity');$('invNature').value=`Supply and installation of ${system.toLowerCase()} solar power system${capacity?' ('+capacity+' kW)':''}`;$('invGstRate').value=val('gstRate')||'0';$('invItemsBody').innerHTML='';(Array.isArray(q.items)?q.items:[]).forEach(x=>addInvoiceItem({desc:x.desc||'',hsn:'',qty:x.qty??1,unit:x.unit||'Nos',rate:x.rate??0}));calculateInvoice();$('invoiceStatus').textContent=`Imported customer, project, item, rate and GST details from ${sourceLabel}. Invoice fields remain editable.`;$('invoiceStatus').style.color='var(--green)';}
function importQuotationItems(){const sel=$('invoiceFromQuotation');const chosen=sel?sel.value:'';if(chosen==='__current__'||!chosen){const q=quotationSnapshot();importQuotationData(q,'the current quotation');return;}const q=readSavedQuotations().find(x=>(x._recordKey||String(x.quoteNo||'').trim())===chosen);importQuotationData(q,`quotation ${q?.quoteNo||''}`);}
function onQuotationSelectionChanged(){const sel=$('invoiceFromQuotation');if(!sel||!sel.value)return;importQuotationItems();}
function previewInvoice(){calculateInvoice();const clone=$('invoiceSheet').cloneNode(true);clone.style.display='block';clone.querySelectorAll('.no-print').forEach(e=>e.remove());$('previewPaper').innerHTML='';$('previewPaper').appendChild(clone);$('previewModal').querySelector('.preview-toolbar strong').textContent='Tax Invoice Preview';$('previewModal').classList.add('open');}
function resetPreviewTitle(){$('previewModal').querySelector('.preview-toolbar strong').textContent='Quotation Preview';}

const initialItems=[
 {desc:'Solar PV Modules',qty:6,unit:'Nos',rate:9500},
 {desc:'Grid-tie Inverter',qty:1,unit:'Nos',rate:28000},
 {desc:'Mounting Structure',qty:1,unit:'Set',rate:18000},
 {desc:'DC/AC Cables, Protection & Earthing',qty:1,unit:'Lot',rate:12500},
 {desc:'Installation, Testing & Commissioning',qty:1,unit:'Job',rate:15000}
];

const $=id=>document.getElementById(id); const money=n=>'₹'+Number(n||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
const brandDefaults={name:'VEERA SOLAR',location:'VELAPUR',mobile:'+91 8625919670',email:'veerasolar@gmail.com',address:'Velapur, Maharashtra',authorized:'',gstin:'',logo:'veera-solar-logo.png',header:'',qr:''};
let activeBrand={...brandDefaults};
function syncBrand(){ $('quotationBrandName').textContent=activeBrand.name||'Solar Company';$('quotationBrandLocation').textContent=activeBrand.location||'';$('quotationLogo').src=activeBrand.logo||'';$('quotationLogo').style.display=activeBrand.logo?'':'none';$('headerBanner').src=activeBrand.header||'';$('headerBanner').style.display=activeBrand.header?'block':'none';$('quotationHeader').classList.toggle('has-banner',!!activeBrand.header);$('brandIdentity').style.display=activeBrand.header?'none':'flex';$('headerPhone').textContent=activeBrand.mobile?'Mobile: '+activeBrand.mobile:'';$('headerEmail').textContent=activeBrand.email||'';$('footerBrandName').textContent=[activeBrand.name,activeBrand.location].filter(Boolean).join(' ');$('footerTagline').textContent=activeBrand.address||'Solar energy solutions tailored to your site.';$('footerContact').textContent=[activeBrand.mobile,activeBrand.email,activeBrand.address].filter(Boolean).join(' • ')||'Add business contact details.';
$('appBrandName').textContent=activeBrand.name||'Solar Company';$('topbarCompany').textContent=[activeBrand.name,activeBrand.location].filter(Boolean).join(' ');$('topbarContact').textContent=[activeBrand.mobile,activeBrand.email,activeBrand.address].filter(Boolean).join(' · ')||'Add business contact details';$('appLogo').src=activeBrand.logo||'veera-solar-logo.png';
$('logoThumb').src=activeBrand.logo||'';$('headerThumb').src=activeBrand.header||'';$('qrThumb').src=activeBrand.qr||'';$('qrThumb').style.display=activeBrand.qr?'block':'none';
 $('preparedBy').value=activeBrand.authorized||'';$('businessPhone').value=activeBrand.mobile||'';$('businessEmail').value=activeBrand.email||'';$('businessAddress').value=[activeBrand.address,activeBrand.gstin?'GSTIN: '+activeBrand.gstin:''].filter(Boolean).join(' | ');$('invSignatory').value=activeBrand.authorized||'';
 $('invoiceHeaderBanner').src=activeBrand.header||'';$('invoiceHeaderBanner').style.display=activeBrand.header?'block':'none';$('invoiceHeader').classList.toggle('has-banner',!!activeBrand.header);$('invoiceBrandIdentity').style.display=activeBrand.header?'none':'flex';$('invoiceBrandContact').textContent=[activeBrand.mobile,activeBrand.email].filter(Boolean).join(' • ');
 $('invoiceLogo').src=activeBrand.logo||'veera-solar-logo.png';$('invoiceBrandName').textContent=activeBrand.name||'VEERA SOLAR';$('invoiceBrandLocation').textContent=activeBrand.location||'';$('invoiceCompanyTitle').textContent=activeBrand.name||'';$('invoiceCompanyAddress').textContent=activeBrand.address||'';$('invoiceCompanyGSTIN').textContent=activeBrand.gstin?'GSTIN: '+activeBrand.gstin:'';$('invoiceFooterBrand').textContent=[activeBrand.name,activeBrand.location].filter(Boolean).join(' ');$('invoiceFooterAddress').textContent=activeBrand.address||'';$('invoiceFooterContact').textContent=[activeBrand.mobile,activeBrand.email].filter(Boolean).join(' • ');$('outForCompany').textContent='For '+(activeBrand.name||'Company');$('outInvoiceQR').src=activeBrand.qr||'';$('outInvoiceQR').style.display=activeBrand.qr?'block':'none';} 
async function uploadBrandFile(file,kind){if(!file)return '';const fd=new FormData();fd.append('brand_upload','1');fd.append('kind',kind);fd.append('asset',file);const r=await fetch(location.pathname,{method:'POST',body:fd});const d=await r.json();if(!r.ok||!d.ok)throw new Error(d.error||'Upload failed');return d.url;}
async function applyBrand(){try{activeBrand.name=$('brandName').value.trim()||'Solar Company';activeBrand.location=$('brandLocation').value.trim();activeBrand.mobile=$('brandMobile').value.trim();activeBrand.email=$('brandEmail').value.trim();activeBrand.address=$('brandAddress').value.trim();activeBrand.authorized=$('brandAuthorized').value.trim();activeBrand.gstin=$('brandGSTIN').value.trim();const logo=await uploadBrandFile($('logoUpload').files[0],'logo');const header=await uploadBrandFile($('headerUpload').files[0],'header');const qr=await uploadBrandFile($('qrUpload').files[0],'qr');if(logo)activeBrand.logo=logo;if(header)activeBrand.header=header;if(qr)activeBrand.qr=qr;localStorage.setItem('veeraSolarBrandText',JSON.stringify({name:activeBrand.name,location:activeBrand.location,mobile:activeBrand.mobile,email:activeBrand.email,address:activeBrand.address,authorized:activeBrand.authorized,gstin:activeBrand.gstin,logo:activeBrand.logo,header:activeBrand.header,qr:activeBrand.qr}));syncBrand();$('brandStatus').textContent='Saved successfully. Images are stored in /uploads.';$('brandStatus').style.color='var(--green)';}catch(e){$('brandStatus').textContent=e.message+' Ensure the PHP project folder is writable.';$('brandStatus').style.color='#b43a2d';}}
function initBrand(){try{const d=JSON.parse(localStorage.getItem('veeraSolarBrandText')||'null');if(d)activeBrand={...brandDefaults,...d};}catch(e){}$('brandName').value=activeBrand.name;$('brandLocation').value=activeBrand.location;$('brandMobile').value=activeBrand.mobile;$('brandEmail').value=activeBrand.email;$('brandAddress').value=activeBrand.address;$('brandAuthorized').value=activeBrand.authorized||'';$('brandGSTIN').value=activeBrand.gstin||'';syncBrand();}
function resetBrand(){activeBrand={...brandDefaults};localStorage.removeItem('veeraSolarBrandText');$('brandName').value=activeBrand.name;$('brandLocation').value=activeBrand.location;$('brandMobile').value='';$('brandEmail').value='';$('brandAddress').value=activeBrand.address;$('brandAuthorized').value='';$('brandGSTIN').value='';$('qrUpload').value='';$('brandStatus').textContent='Default fields restored. Existing uploaded files are not deleted.';syncBrand();}
function openPreview(){calculate();const clone=document.querySelector('.sheet').cloneNode(true);clone.querySelectorAll('.no-print,.internal-only,.item-code,[data-internal="true"],.notice').forEach(e=>e.remove());clone.querySelectorAll('input,textarea,select').forEach(el=>{const span=document.createElement('span');span.textContent=el.tagName==='SELECT'?el.options[el.selectedIndex]?.text:(el.value||'');span.style.whiteSpace='pre-wrap';el.replaceWith(span)});$('previewPaper').innerHTML='';$('previewPaper').appendChild(clone);$('previewModal').classList.add('open');}

function addItem(item={desc:'New material / service',qty:1,unit:'Nos',rate:0}){const tr=document.createElement('tr');tr.innerHTML=`<td><input class="desc" aria-label="Description" value=""></td><td><input class="qty" aria-label="Quantity" type="number" min="0" step="0.01"></td><td><input class="unit" aria-label="Unit" value="Nos"></td><td><input class="rate" aria-label="Unit rate" type="number" min="0" step="0.01"></td><td class="amount">₹0.00</td><td class="no-print"><button type="button" class="remove" aria-label="Remove row">×</button></td>`;tr.querySelector('.desc').value=item.desc;tr.querySelector('.qty').value=item.qty;tr.querySelector('.unit').value=item.unit;tr.querySelector('.rate').value=item.rate;tr.querySelectorAll('input').forEach(i=>i.addEventListener('input',calculate));tr.querySelector('.remove').addEventListener('click',()=>{tr.remove();calculate()});$('itemsBody').appendChild(tr);calculate()}
function updatePaymentAmounts(total){
 const advancePct=Math.min(100,Math.max(0,Number($('advancePct').value)||0));
 const installPct=Math.min(100,Math.max(0,Number($('installPct').value)||0));
 const commissionPct=Math.min(100,Math.max(0,Number($('commissionPct').value)||0));
 const advanceAmount=Math.round((total*advancePct/100)*100)/100;
 const installAmount=Math.round((total*installPct/100)*100)/100;
 const commissionAmount=Math.round((total*commissionPct/100)*100)/100;
 $('advanceAmt').value=money(advanceAmount);
 $('installAmt').value=money(installAmount);
 $('commissionAmt').value=money(commissionAmount);
 const pctTotal=advancePct+installPct+commissionPct;
 const totalCents=Math.round(Number(total||0)*100);
 const paymentCents=Math.round((advanceAmount+installAmount+commissionAmount)*100);
 const priceMatches=Math.abs(pctTotal-100)<0.001 && paymentCents===totalCents;
 const status=$('paymentWarning');
 status.classList.toggle('match',priceMatches);
 status.classList.toggle('mismatch',!priceMatches);
 status.textContent=priceMatches
   ? '✓ Price matches total quotation value. Payment milestones total 100%.'
   : '✕ Price must be equal to total price. Payment percentages currently total '+pctTotal.toFixed(2)+'%; adjust them to 100%.';
 $('dashPaymentStatus').textContent=pctTotal.toFixed(0)+'%';
}
function calculate(){let subtotal=0;document.querySelectorAll('#itemsBody tr').forEach(tr=>{let q=Math.max(0,Number(tr.querySelector('.qty').value)||0),r=Math.max(0,Number(tr.querySelector('.rate').value)||0),a=q*r;subtotal+=a;tr.querySelector('.amount').textContent=money(a)});const gstRate=Math.max(0,Number($('gstRate').value)||0),discount=Math.min(subtotal,Math.max(0,Number($('discount').value)||0)),gst=Math.max(0,subtotal-discount)*gstRate/100,gross=Math.max(0,subtotal-discount+gst),subsidy=Math.min(gross,Math.max(0,Number($('subsidy').value)||0)),net=gross-subsidy,kw=Math.max(0,Number($('capacity').value)||0),watts=kw*1000;
$('subtotal').textContent=money(subtotal);$('gstAmount').textContent=money(gst);$('discountAmount').textContent='− '+money(discount);$('grossTotal').textContent=money(gross);$('subsidyAmount').textContent='− '+money(subsidy);$('netTotal').textContent=money(net);$('paymentTotal').textContent=money(net);$('dashCapacity').textContent=kw.toFixed(1)+' kW';$('dashSubtotal').textContent=money(subtotal);$('dashTotal').textContent=money(net);$('panelCount').value=Number($('panelWatt').value)>0?Math.ceil(watts/Number($('panelWatt').value)):0;
updatePaymentAmounts(net);
$('footerQuoteNo').textContent=$('quoteNo').value||'—';$('headerPhone').textContent='Contact: '+($('businessPhone').value||'Add business phone');$('footerContact').textContent=[$('businessPhone').value,$('businessEmail').value,$('businessAddress').value].filter(Boolean).join(' • ')||'Add business contact details';}
$('addRow').addEventListener('click',()=>addItem());['gstRate','discount','subsidy','capacity','panelWatt','advancePct','installPct','commissionPct','quoteNo','businessPhone','businessEmail','businessAddress','panelWatt','quoteNo'].forEach(id=>{if($(id)){ $(id).addEventListener('input',calculate); $(id).addEventListener('change',calculate); }});
$('saveBtn').addEventListener('click',saveQuotation);
$('resetBtn').addEventListener('click',()=>{ $('itemsBody').innerHTML='';initialItems.forEach(addItem);$('gstRate').value=8.9;$('discount').value=0;$('subsidy').value=0;$('capacity').value=3.3;$('panelWatt').value=550;$('apiStatus').textContent='';calculate()});

function setTab(tab){
 document.querySelectorAll('.nav-item').forEach(b=>b.classList.toggle('active',b.dataset.tab===tab));
 document.body.classList.toggle('invoice-mode',tab==='taxinvoice');
 const settings=$('brandSettings'),dashboard=$('dashboardPage'),materials=$('materialsPage'),savedPage=$('savedQuotationsPage'),savedInvoicesPage=$('savedInvoicesPage'),taxForm=$('taxInvoicePage'),sheet=document.querySelector('.sheet'),invoiceSheet=$('invoiceSheet'),toolbar=$('quotationToolbar');
 dashboard.style.display=tab==='dashboard'?'block':'none';materials.style.display=tab==='materials'?'block':'none';savedPage.style.display=tab==='savedquotations'?'block':'none';if(savedInvoicesPage)savedInvoicesPage.style.display=tab==='savedinvoices'?'block':'none';settings.style.display=tab==='settings'?'block':'none';taxForm.style.display=tab==='taxinvoice'?'block':'none';
 sheet.style.display=(tab==='quotation'||tab==='costing')?'block':'none';invoiceSheet.style.display='none';
 const header=sheet.querySelector('.brandbar'),content=sheet.querySelector('.content'),footer=sheet.querySelector('.footer');
 if(tab==='costing'){header.style.display='none';footer.style.display='none';content.querySelectorAll('.quotation-section').forEach(sec=>sec.style.display=sec.dataset.section==='costing'?'block':'none');content.querySelector('.eyebrow').style.display='none';content.querySelector('.title').style.display='none';content.querySelector('.intro').style.display='none';}
 else if(tab==='quotation'){header.style.display='';footer.style.display='';content.querySelectorAll('.quotation-section').forEach(sec=>sec.style.display='');content.querySelector('.eyebrow').style.display='';content.querySelector('.title').style.display='';content.querySelector('.intro').style.display='';}
 if(['settings','dashboard','materials','savedquotations','savedinvoices','taxinvoice'].includes(tab))sheet.style.display='none';
 if(tab==='materials')renderMaterialMaster();if(tab==='savedquotations')renderSavedQuotations();if(tab==='savedinvoices')renderSavedInvoices();if(toolbar)toolbar.style.display=(tab==='quotation'||tab==='costing')?'flex':'none';
 $('topPreviewBtn').textContent=tab==='taxinvoice'?'Preview invoice':'Preview quotation';$('topPrintBtn').textContent=tab==='taxinvoice'?'Print invoice / PDF':'Print / Save PDF';
}
function renderMaterialMaster(){
 const list=$('materialMasterList');if(!list)return;
 list.innerHTML='<div class="tablewrap"><table><thead><tr><th>Material / Service</th><th>Qty</th><th>Unit</th><th>Rate</th><th>Amount</th></tr></thead><tbody>'+[...document.querySelectorAll('#itemsBody tr')].map(tr=>'<tr><td>'+escapeHtml(tr.querySelector('.desc').value)+'</td><td>'+escapeHtml(tr.querySelector('.qty').value)+'</td><td>'+escapeHtml(tr.querySelector('.unit').value)+'</td><td>'+money(tr.querySelector('.rate').value)+'</td><td>'+tr.querySelector('.amount').textContent+'</td></tr>').join('')+'</tbody></table></div>';
}
function escapeHtml(v){return String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
// Payment percentages have their own immediate listeners. Recalculate the quotation total,
// then update all three amounts from that same total on every keystroke.
['advancePct','installPct','commissionPct'].forEach(id=>{
 const field=$(id);
 if(field){
   field.addEventListener('input',()=>{
     const entered=field.value.trim();
     if(entered!=='' && Number(entered)>100) field.value='100';
     if(entered!=='' && Number(entered)<0) field.value='0';
     calculate();
   });
   field.addEventListener('change',()=>calculate());
   field.addEventListener('blur',()=>{if(field.value.trim()==='')field.value='0';calculate();});
 }
});


function applyGstVisibility(){document.body.classList.toggle('invoice-hide-gst',!$('showGstDetails').checked);}
$('showGstDetails').addEventListener('change',applyGstVisibility);
if($('addInvoiceRow'))$('addInvoiceRow').addEventListener('click',()=>addInvoiceItem());
if($('importQuoteInvoice'))$('importQuoteInvoice').addEventListener('click',e=>{e.preventDefault();populateQuotationSelector($('invoiceFromQuotation')?.value||'');importQuotationItems();});
if($('invoiceFromQuotation'))$('invoiceFromQuotation').addEventListener('change',onQuotationSelectionChanged);
if($('saveInvoiceBtn'))$('saveInvoiceBtn').addEventListener('click',e=>{e.preventDefault();saveInvoice();});
if($('previewInvoiceBtn'))$('previewInvoiceBtn').addEventListener('click',e=>{e.preventDefault();previewInvoice();});
if($('printInvoiceBtn'))$('printInvoiceBtn').addEventListener('click',e=>{e.preventDefault();calculateInvoice();setTab('taxinvoice');window.print();});
['invGstRate','invRoundOff','invNo','invDate','invChallan','invBuilding','invWo','invNature','billName','billPhone','billGSTIN','billAddress','shipName','shipGSTIN','shipAddress','invBank','invAccount','invIFSC','invBranch','invSignatory'].forEach(id=>{const el=$(id);if(el){el.addEventListener('input',calculateInvoice);el.addEventListener('change',calculateInvoice);}});
$('qrUpload').addEventListener('change',async e=>{const file=e.target.files[0];if(!file)return;try{const url=await uploadBrandFile(file,'qr');activeBrand.qr=url;syncBrand();$('brandStatus').textContent='QR code uploaded. Click Apply Header & Footer to save settings.';$('brandStatus').style.color='var(--green)';}catch(err){$('brandStatus').textContent=err.message;$('brandStatus').style.color='#b43a2d';}});

document.querySelectorAll('.nav-item').forEach(b=>b.addEventListener('click',()=>setTab(b.dataset.tab)));
document.querySelectorAll('[data-goto]').forEach(b=>b.addEventListener('click',()=>setTab(b.dataset.goto)));
$('topPreviewBtn').addEventListener('click',()=>{if(document.body.classList.contains('invoice-mode'))previewInvoice();else{setTab('quotation');openPreview();}});
$('topPrintBtn').addEventListener('click',()=>{if(document.body.classList.contains('invoice-mode')){calculateInvoice();window.print();}else{setTab('quotation');window.print();}});
$('applyBrand').addEventListener('click',applyBrand);$('resetBrand').addEventListener('click',resetBrand);$('previewBtn').addEventListener('click',openPreview);$('closePreview').addEventListener('click',()=>{$('previewModal').classList.remove('open');resetPreviewTitle();});$('previewPrint').addEventListener('click',()=>window.print());$('previewModal').addEventListener('click',e=>{if(e.target===$('previewModal')){$('previewModal').classList.remove('open');resetPreviewTitle();}});initBrand();
const now=new Date();$('quoteDate').value=now.toISOString().slice(0,10);const later=new Date(now);later.setDate(later.getDate()+15);$('validUntil').value=later.toISOString().slice(0,10);$('invDate').value=now.toISOString().slice(0,10);
initialItems.forEach(addItem);loadSavedQuotation();calculate();populateQuotationSelector();renderSavedQuotations();renderSavedInvoices();addInvoiceItem({desc:'Solar equipment / service and Installation, Testing & Commissioning',hsn:'',qty:1,unit:'Job',rate:0});loadSavedInvoice();calculateInvoice();applyGstVisibility();setTab('dashboard');
// Extra safeguard: update payment amounts directly from the displayed quotation total
// whenever any payment percentage changes, without depending on other costing updates.
function recalculatePaymentAmountsImmediately(){
  const totalElement = $('paymentTotal') || $('netTotal');
  const total = Number(String(totalElement ? totalElement.textContent : '0').replace(/[^0-9.-]/g, '')) || 0;
  updatePaymentAmounts(total);
}
['advancePct','installPct','commissionPct'].forEach(id => {
  const field = $(id);
  if (!field) return;
  field.addEventListener('input', recalculatePaymentAmountsImmediately);
  field.addEventListener('change', recalculatePaymentAmountsImmediately);
});

</script></div></div></body></html>
