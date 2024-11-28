@echo off
cd C:\Users\DionJonesEryriConsul\Documents\Coding Projects\ContructionTraining\ConstructionLMS_Backup\ConstructionLMS
php artisan queue:work --tries=3 --timeout=60
