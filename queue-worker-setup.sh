#!/bin/bash

# Check if running from the Laravel project directory
if [ ! -f "./artisan" ]; then
    echo "Error: artisan file not found in current directory"
    echo "Please run this script from your Laravel project root directory"
    exit 1
fi

# Get absolute path of artisan and project directory
ARTISAN_PATH=$(readlink -f ./artisan)
PROJECT_DIR=$(basename $(pwd))
# Sanitize project directory name for use in filenames
SAFE_PROJECT_NAME=$(echo "$PROJECT_DIR" | tr '[:upper:]' '[:lower:]' | sed 's/[^a-z0-9]/-/g')
echo "✓ Found artisan at: ${ARTISAN_PATH}"
echo "✓ Project directory: ${PROJECT_DIR}"

# Function to check if command was successful
check_status() {
    if [ $? -eq 0 ]; then
        echo "✓ $1"
    else
        echo "✗ $1"
        exit 1
    fi
}

# Check if script is run with sudo
if [ "$EUID" -ne 0 ]; then 
    echo "Please run with sudo"
    echo "Usage: sudo ./$(basename $0)"
    exit 1
fi

# Update package list
echo "Updating package list..."
apt-get update
check_status "Package list updated"

# Install supervisor
echo "Installing supervisor..."
apt-get install -y supervisor
check_status "Supervisor installed"

# Ensure supervisor is running
systemctl enable supervisor
systemctl start supervisor
check_status "Supervisor service started"

# Update main supervisor configuration to set minfds
cat > /etc/supervisor/supervisord.conf << EOF
[supervisord]
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid
childlogdir=/var/log/supervisor
minfds=10000

[unix_http_server]
file=/var/run/supervisor.sock
chmod=0700

[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

[supervisorctl]
serverurl=unix:///var/run/supervisor.sock

[include]
files = /etc/supervisor/conf.d/*.conf
EOF
check_status "Main supervisor configuration updated with minfds"

# Create Laravel Queue Worker configuration with unique name
cat > "/etc/supervisor/conf.d/laravel-queue-${SAFE_PROJECT_NAME}.conf" << EOF
[program:laravel-queue-${SAFE_PROJECT_NAME}]
process_name=%(program_name)s_%(process_num)02d
command=php ${ARTISAN_PATH} queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/laravel-queue-${SAFE_PROJECT_NAME}.log
stopwaitsecs=3600
EOF
check_status "Queue worker configuration created"

# Create log directory if it doesn't exist
mkdir -p /var/log/supervisor
check_status "Log directory created"

# Set proper permissions
chown -R www-data:www-data /var/log/supervisor
check_status "Log permissions set"

# Reload supervisor configuration
supervisorctl reread
supervisorctl update
check_status "Supervisor configuration reloaded"

# Start the processes with unique name
supervisorctl start "laravel-queue-${SAFE_PROJECT_NAME}:*"
check_status "Processes started"

# Show status
echo -e "\nCurrent supervisor status:"
supervisorctl status

echo -e "\nSetup completed successfully!"
echo "You can monitor the processes using 'supervisorctl status'"
echo "Logs are available at:"
echo "- Queue Worker: /var/log/supervisor/laravel-queue-${SAFE_PROJECT_NAME}.log"
echo "- Supervisor: /var/log/supervisor/supervisord.log"