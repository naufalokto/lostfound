@echo off
echo Starting Lost & Found Application...
echo Server running at: http://localhost:8000
echo Press Ctrl+C to stop the server
echo.
php -S localhost:8000 -t public server.php
pause

