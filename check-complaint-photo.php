<?php
// Quick check untuk complaint ID tertentu
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$complaintId = $argv[1] ?? 14;

$complaint = DB::table('complaints')->where('id', $complaintId)->first(['id', 'photo', 'created_at']);

if ($complaint) {
    echo "Complaint ID: {$complaint->id}\n";
    echo "Photo in DB: {$complaint->photo}\n";
    echo "Created at: {$complaint->created_at}\n";
    
    if (strpos($complaint->photo, 'complaints/photos/') === 0) {
        echo "✅ Path CORRECT\n";
        echo "Expected URL: " . config('app.url') . "/storage/{$complaint->photo}\n";
    } else {
        echo "❌ Path WRONG - missing complaints/photos/ prefix\n";
        echo "Should be: complaints/photos/{$complaint->photo}\n";
    }
} else {
    echo "Complaint #{$complaintId} not found\n";
}
