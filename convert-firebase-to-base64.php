#!/usr/bin/env php
<?php

/**
 * Convert Firebase Credentials to Base64
 * 
 * Helper script untuk convert firebase credentials JSON ke base64
 * untuk deployment di Railway/Heroku
 * 
 * Usage: php convert-firebase-to-base64.php
 */

echo "🔐 Firebase Credentials to Base64 Converter\n";
echo str_repeat("=", 60) . "\n\n";

// Check if credentials file exists
$credentialsPath = __DIR__ . '/storage/app/firebase-credentials.json';

if (!file_exists($credentialsPath)) {
    echo "❌ Firebase credentials file not found!\n";
    echo "   Expected location: {$credentialsPath}\n\n";
    echo "Please make sure you have:\n";
    echo "1. Downloaded service account JSON from Firebase Console\n";
    echo "2. Saved it to: storage/app/firebase-credentials.json\n\n";
    exit(1);
}

// Read file
$credentials = file_get_contents($credentialsPath);

if ($credentials === false) {
    echo "❌ Failed to read credentials file!\n";
    exit(1);
}

// Validate JSON
$json = json_decode($credentials);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "❌ Invalid JSON file!\n";
    echo "   Error: " . json_last_error_msg() . "\n";
    exit(1);
}

// Verify it's a service account
if (!isset($json->type) || $json->type !== 'service_account') {
    echo "❌ Not a valid service account JSON!\n";
    echo "   Make sure you downloaded the correct file from Firebase Console\n";
    exit(1);
}

// Convert to base64
$base64 = base64_encode($credentials);

echo "✅ Conversion successful!\n\n";
echo "📋 Firebase Project Info:\n";
echo "   Project ID: " . ($json->project_id ?? 'N/A') . "\n";
echo "   Client Email: " . ($json->client_email ?? 'N/A') . "\n";
echo "   File Size: " . strlen($credentials) . " bytes\n";
echo "   Base64 Size: " . strlen($base64) . " bytes\n";
echo "\n";

echo str_repeat("=", 60) . "\n";
echo "🔑 Add this to your Railway Environment Variables:\n";
echo str_repeat("=", 60) . "\n\n";

echo "Variable Name:\n";
echo "FIREBASE_CREDENTIALS_BASE64\n\n";

echo "Variable Value:\n";
echo wordwrap($base64, 70, "\n", true) . "\n\n";

echo str_repeat("=", 60) . "\n\n";

// Save to file for easy copy
$outputFile = __DIR__ . '/firebase-base64.txt';
file_put_contents($outputFile, $base64);

echo "✅ Base64 string also saved to: firebase-base64.txt\n";
echo "   You can copy from this file to Railway dashboard\n\n";

echo "📝 Next Steps:\n";
echo "1. Copy the base64 string above\n";
echo "2. Go to Railway Dashboard → Your Service → Variables\n";
echo "3. Add new variable:\n";
echo "   - Name: FIREBASE_CREDENTIALS_BASE64\n";
echo "   - Value: [paste base64 string]\n";
echo "4. Restart your service\n\n";

echo "🚨 IMPORTANT:\n";
echo "- Delete firebase-base64.txt after copying (contains sensitive data)\n";
echo "- Don't commit this file to git!\n";
echo "- The base64 string is sensitive - treat it like a password\n\n";

echo "✅ Done!\n";
