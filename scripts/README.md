# Queue Worker Service Setup

This directory contains scripts to set up and manage the Laravel queue worker as a Windows service.

## Files

- `queue-worker.bat`: The batch script that runs the Laravel queue worker
- `setup-queue-worker.ps1`: PowerShell script to install and configure the queue worker service
- `README.md`: This documentation file

## Installation

1. Open PowerShell as Administrator
2. Navigate to this directory
3. Run the setup script:
   ```powershell
   .\setup-queue-worker.ps1
   ```

## Managing the Service

After installation, you can manage the service using these commands in PowerShell:

```powershell
# Start the service
nssm start LaravelQueueWorker

# Stop the service
nssm stop LaravelQueueWorker

# Restart the service
nssm restart LaravelQueueWorker

# Check service status
nssm status LaravelQueueWorker
```

You can also manage the service through Windows Services:
1. Press `Win + R`
2. Type `services.msc`
3. Find "Laravel Queue Worker" in the list
4. Right-click to start, stop, or restart

## Configuration

The queue worker is configured with these settings:
- Tries: 3 (will attempt failed jobs 3 times)
- Timeout: 60 seconds (maximum time for a job to complete)

To modify these settings, edit `queue-worker.bat` and restart the service.

## Troubleshooting

If you encounter issues:

1. Check the Windows Event Viewer for errors
2. Verify PHP is in your system PATH
3. Ensure the project path in `queue-worker.bat` is correct
4. Check Laravel's `storage/logs` directory for error logs

## Uninstallation

To remove the service:

```powershell
nssm remove LaravelQueueWorker confirm
```
