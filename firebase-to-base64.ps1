# Convert Firebase JSON to Base64 for Railway
# Usage: .\firebase-to-base64.ps1 path/to/firebase-credentials.json

param(
    [Parameter(Mandatory=$true)]
    [string]$JsonPath
)

if (!(Test-Path $JsonPath)) {
    Write-Host "❌ File not found: $JsonPath" -ForegroundColor Red
    exit 1
}

try {
    $jsonContent = Get-Content $JsonPath -Raw
    $bytes = [System.Text.Encoding]::UTF8.GetBytes($jsonContent)
    $base64 = [System.Convert]::ToBase64String($bytes)

    Write-Host "✅ Conversion successful!" -ForegroundColor Green
    Write-Host "`nCopy this value to Railway environment variable:" -ForegroundColor Yellow
    Write-Host "FIREBASE_CREDENTIALS_BASE64=" -NoNewline -ForegroundColor Cyan
    Write-Host $base64 -ForegroundColor White

    # Save to file
    $base64 | Out-File "firebase-base64.txt" -Encoding UTF8
    Write-Host "`n✅ Saved to: firebase-base64.txt" -ForegroundColor Green

} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
