# Download NSSM
$nssmUrl = "https://nssm.cc/release/nssm-2.24.zip"
$nssmZip = "C:\nssm.zip"
$nssmPath = "C:\nssm"

# Create NSSM directory if it doesn't exist
if (!(Test-Path $nssmPath)) {
    New-Item -ItemType Directory -Path $nssmPath
}

# Download NSSM
Invoke-WebRequest -Uri $nssmUrl -OutFile $nssmZip

# Extract NSSM
Expand-Archive -Path $nssmZip -DestinationPath $nssmPath -Force

# Copy the appropriate executable based on system architecture
$architecture = if ([Environment]::Is64BitOperatingSystem) { "win64" } else { "win32" }
Copy-Item "$nssmPath\nssm-2.24\$architecture\nssm.exe" "C:\Windows\System32\"

# Remove downloaded files
Remove-Item $nssmZip
Remove-Item $nssmPath -Recurse

# Get the full path to the queue worker batch script
$scriptPath = "C:\Users\DionJonesEryriConsul\Documents\Coding Projects\ContructionTraining\ConstructionLMS_Backup\ConstructionLMS\scripts\queue-worker.bat"

# Install the service using NSSM
nssm install LaravelQueueWorker $scriptPath

# Configure service settings
nssm set LaravelQueueWorker Description "Laravel Queue Worker Service for ConstructionLMS"
nssm set LaravelQueueWorker DisplayName "Laravel Queue Worker"
nssm set LaravelQueueWorker Start SERVICE_AUTO_START

# Start the service
nssm start LaravelQueueWorker

Write-Host "Laravel Queue Worker service has been installed and started!"
