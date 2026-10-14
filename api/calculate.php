<?php
// Lightweight JSON API for quotation calculation. No database is required.
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['error' => 'Use POST with a JSON quotation payload.']);
    exit;
}
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload.']);
    exit;
}
$capacity = filter_var($data['capacity_kw'] ?? 0, FILTER_VALIDATE_FLOAT);
$gstPercent = filter_var($data['gst_percent'] ?? 0, FILTER_VALIDATE_FLOAT);
$discount = filter_var($data['discount'] ?? 0, FILTER_VALIDATE_FLOAT);
$subsidy = filter_var($data['subsidy'] ?? 0, FILTER_VALIDATE_FLOAT);
if ($capacity === false || $capacity < 0 || $gstPercent === false || $gstPercent < 0 || $gstPercent > 100 || $discount === false || $discount < 0 || $subsidy === false || $subsidy < 0) {
    http_response_code(422);
    echo json_encode(['error' => 'Capacity, GST, discount or subsidy contains an invalid value.']);
    exit;
}
$items = $data['items'] ?? [];
if (!is_array($items) || count($items) > 100) {
    http_response_code(422);
    echo json_encode(['error' => 'Items must be an array with no more than 100 rows.']);
    exit;
}
$subtotal = 0.0;
$cleanItems = [];
foreach ($items as $item) {
    if (!is_array($item)) continue;
    $qty = filter_var($item['quantity'] ?? 0, FILTER_VALIDATE_FLOAT);
    $rate = filter_var($item['rate'] ?? 0, FILTER_VALIDATE_FLOAT);
    if ($qty === false || $qty < 0 || $rate === false || $rate < 0) {
        http_response_code(422);
        echo json_encode(['error' => 'Each item quantity and rate must be non-negative numbers.']);
        exit;
    }
    $amount = $qty * $rate;
    $subtotal += $amount;
    $cleanItems[] = [
        'description' => mb_substr(trim((string)($item['description'] ?? '')), 0, 200),
        'quantity' => $qty,
        'unit' => mb_substr(trim((string)($item['unit'] ?? '')), 0, 30),
        'rate' => round($rate, 2),
        'amount' => round($amount, 2),
    ];
}
$discount = min($subtotal, $discount);
$taxable = max(0, $subtotal - $discount);
$gst = $taxable * $gstPercent / 100;
$total = $taxable + $gst;
$subsidy = min($total, $subsidy);
$effective = $total - $subsidy;
$watts = $capacity * 1000;
echo json_encode([
    'success' => true,
    'currency' => 'INR',
    'capacity_kw' => round($capacity, 3),
    'items' => $cleanItems,
    'subtotal' => round($subtotal, 2),
    'discount' => round($discount, 2),
    'taxable_amount' => round($taxable, 2),
    'gst_percent' => round($gstPercent, 2),
    'gst_amount' => round($gst, 2),
    'total_project_cost' => round($total, 2),
    'subsidy_entered' => round($subsidy, 2),
    'effective_cost' => round($effective, 2),
    'rate_per_watt' => $watts > 0 ? round($effective / $watts, 2) : null,
    'rate_per_kw' => $capacity > 0 ? round($effective / $capacity, 2) : null,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
