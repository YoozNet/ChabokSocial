## Installation Guide

Follow these steps on your Ubuntu server to install and configure ChabokSocial end-to-end:

```bash
# System update & basic tools
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common curl wget nano ufw

# Create and enable 4 GB swap
sudo fallocate -l 4G /swapfile && sudo chmod 600 /swapfile && sudo mkswap /swapfile && sudo swapon /swapfile && echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab && free -h

# Nginx installation & basic setup
sudo apt install nginx 
sudo ufw allow 'Nginx HTTP' && sudo ufw enable && sudo ufw status 

# Create web root and set permissions
sudo mkdir -p /var/www/your_domain && sudo chown -R $USER:$USER /var/www/your_domain 

# Nginx site config
sudo tee /etc/nginx/sites-available/your_domain > /dev/null << 'EOF'
server {
    listen 80;
    server_name your_domain www.your_domain;

    root /var/www/your_domain/public/;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }

    location /ws {
        proxy_pass http://127.0.0.1:6001; 
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 120s;
    }
}
EOF

sudo ln -s /etc/nginx/sites-available/your_domain /etc/nginx/sites-enabled/
sudo unlink /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx

# SSL with Certbot

sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your_domain

# Database setup
sudo apt install mysql-server
sudo mysql_secure_installation
sudo apt install phpmyadmin
sudo ln -s /usr/share/phpmyadmin /var/www/your_domain/public/phpmyadmin

# PHP 8.2 & extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-common php8.2-mbstring php8.2-xml php8.2-zip php8.2-curl php8.2-gd php8.2-intl php8.2-bcmath php8.2-tokenizer php8.2-pdo php8.2-mysql php8.2-soap php8.2-opcache php8.2-readline

# Redis
sudo apt install -y redis-server

# Node.js & Composer
apt install composer
curl -fsSL https://deb.nodesource.com/setup_23.x -o nodesource_setup.sh
sudo -E bash nodesource_setup.sh
sudo apt-get install -y nodejs

# Supervisor for Laravel workers & listeners

sudo apt install -y supervisor
sudo systemctl enable supervisor && sudo systemctl start supervisor

# Create Supervisor configs
sudo tee /etc/supervisor/conf.d/laravel-worker.conf > /dev/null << 'EOF'
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/your_domain/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
startsecs=5
startretries=3
numprocs=1
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/dev/null
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
EOF

sudo tee /etc/supervisor/conf.d/chat-listen.conf > /dev/null << 'EOF'
[program:chat-listen]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/your_domain/artisan chat:listen
autostart=true
autorestart=true
startsecs=5
startretries=3
numprocs=1
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/dev/null
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
EOF

sudo tee /etc/supervisor/conf.d/heartbeat-listener.conf > /dev/null << 'EOF'
[program:heartbeat-listener]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/your_domain/artisan heartbeat:listen
autostart=true
autorestart=true
startsecs=5
startretries=3
numprocs=1
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/dev/null
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
EOF

sudo tee /etc/supervisor/conf.d/my-check-online.conf > /dev/null << 'EOF'
[program:my-check-online]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/bash -c "while true; do /usr/bin/php /var/www/your_domain/artisan my:check-online; sleep 15; done"
autostart=true
autorestart=true
startsecs=5
startretries=3
numprocs=1
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/dev/null
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
EOF

sudo tee /etc/supervisor/conf.d/socketio.conf > /dev/null << 'EOF'
[program:socketio]
directory=/var/www/laravel-socket-server
command=/usr/bin/node server.js
autostart=true
autorestart=true
startsecs=5
startretries=3
numprocs=1
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/dev/null
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
EOF

sudo tee /etc/supervisor/conf.d/friend-listen.conf > /dev/null << 'EOF'
[program:friend-listen]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/your_domain/artisan friend:listen
autostart=true
autorestart=true
startsecs=5
startretries=3
numprocs=1
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/dev/null
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
EOF

sudo tee /etc/supervisor/conf.d/session-listen.conf > /dev/null << 'EOF'
[program:session-listen]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/your_domain/artisan session:listen
autostart=true
autorestart=true
startsecs=5
startretries=3
numprocs=1
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/dev/null
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
EOF

sudo supervisorctl reread
sudo supervisorctl update

# Laravel app setup
cd /var/www/your_domain
cp .env.example .env
composer install
php artisan key:generate
chmod -R 775 storage bootstrap/cache
php artisan storage:link
php artisan migrate