#!/bin/bash

# Check if running from the Laravel project directory
if [ ! -f "./artisan" ]; then
    echo "Error: artisan file not found in current directory"
    echo "Please run this script from your Laravel project root directory"
    exit 1
fi

# Check if .env exists
if [ ! -f "./.env" ]; then
    echo "Error: .env file not found in current directory"
    echo "Please ensure your .env file exists"
    exit 1
fi

# Get absolute path of artisan
ARTISAN_PATH=$(readlink -f ./artisan)
echo "✓ Found artisan at: ${ARTISAN_PATH}"

# Function to check if command was successful
check_status() {
    if [ $? -eq 0 ]; then
        echo "✓ $1"
    else
        echo "✗ $1"
        exit 1
    fi
}

# Function to generate random string
generate_random_string() {
    local length=$1
    cat /dev/urandom | tr -dc 'a-z0-9' | fold -w $length | head -n 1
}

# Generate Reverb credentials
REVERB_APP_ID=$(shuf -i 100000-999999 -n 1)
REVERB_APP_KEY=$(generate_random_string 20)
REVERB_APP_SECRET=$(generate_random_string 20)

# Update .env file with new Reverb credentials
echo "Updating Reverb configuration in .env..."
# Backup .env file
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Update or add Reverb settings
if grep -q "REVERB_APP_ID" .env; then
    sed -i "s/REVERB_APP_ID=.*/REVERB_APP_ID=$REVERB_APP_ID/" .env
else
    echo "REVERB_APP_ID=$REVERB_APP_ID" >> .env
fi

if grep -q "REVERB_APP_KEY" .env; then
    sed -i "s/REVERB_APP_KEY=.*/REVERB_APP_KEY=$REVERB_APP_KEY/" .env
else
    echo "REVERB_APP_KEY=$REVERB_APP_KEY" >> .env
fi

if grep -q "REVERB_APP_SECRET" .env; then
    sed -i "s/REVERB_APP_SECRET=.*/REVERB_APP_SECRET=$REVERB_APP_SECRET/" .env
else
    echo "REVERB_APP_SECRET=$REVERB_APP_SECRET" >> .env
fi

check_status "Reverb configuration updated in .env"
echo "✓ Backup created at .env.backup.$(date +%Y%m%d_%H%M%S)"
echo "✓ New Reverb credentials:"
echo "  APP ID: $REVERB_APP_ID"
echo "  APP KEY: $REVERB_APP_KEY"
echo "  APP SECRET: $REVERB_APP_SECRET"

# Check if script is run with sudo
if [ "$EUID" -ne 0 ]; then 
    echo "Please run with sudo"
    echo "Usage: sudo ./$(basename $0)"
    exit 1
fi

# Enable required Apache modules
echo "Enabling required Apache modules..."
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod proxy_balancer
sudo a2enmod proxy_wstunnel
sudo a2enmod rewrite
sudo a2enmod lbmethod_byrequests
check_status "Apache modules enabled"

# Create Reverb proxy configuration
REVERB_CONF="/etc/apache2/conf-available/reverb-proxy.conf"
echo "Creating Reverb proxy configuration..."
cat > "$REVERB_CONF" << 'EOF'
<IfModule mod_proxy.c>
    <IfModule mod_proxy_wstunnel.c>
        RewriteEngine On
        RewriteCond %{HTTP:Upgrade} =websocket [NC]
        RewriteCond %{HTTP:Connection} upgrade$ [NC]
        RewriteRule ^/app(.*)$ ws://127.0.0.1:8080/app$1 [P,L]

        ProxyPass /app ws://127.0.0.1:8080/app
        ProxyPassReverse /app ws://127.0.0.1:8080/app
    </IfModule>

    RewriteCond %{REQUEST_URI} ^/app [NC]
    RewriteRule ^/app(.*)$ http://127.0.0.1:8080/app$1 [P,L]

    ProxyPass /app http://127.0.0.1:8080/app
    ProxyPassReverse /app http://127.0.0.1:8080/app
</IfModule>
EOF
check_status "Reverb proxy configuration created"

# Enable the configuration
sudo a2enconf reverb-proxy
check_status "Reverb proxy configuration enabled"

# Restart Apache
echo "Restarting Apache..."
sudo systemctl restart apache2
check_status "Apache restarted"

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

# Create Laravel Websocket Worker configuration
cat > /etc/supervisor/conf.d/laravel-reverb.conf << EOF
[program:laravel-reverb]
process_name=%(program_name)s_%(process_num)02d
command=php ${ARTISAN_PATH} reverb:start
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/laravel-reverb.log
stopwaitsecs=3600
EOF
check_status "Websocket worker configuration created"

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

# Start the processes
supervisorctl start laravel-reverb:*
check_status "Processes started"

# Show status
echo -e "\nCurrent supervisor status:"
supervisorctl status

echo -e "\nSetup completed successfully!"
echo "You can monitor the processes using 'supervisorctl status'"
echo "Logs are available at:"
echo "- Websocket: /var/log/supervisor/laravel-reverb.log"
echo "- Supervisor: /var/log/supervisor/supervisord.log"
echo -e "\nIMPORTANT: New Reverb credentials have been generated and saved to .env"
echo "A backup of your original .env has been created"
echo "Apache Reverb proxy configuration has been set up at: $REVERB_CONF"
