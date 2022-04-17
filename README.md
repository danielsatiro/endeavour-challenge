### Server Requirements

- PHP >= 8.0
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

### Installation via Docker
    docker-compose up -d

### Server Installation Commands
    composer install
    cp .env.example .env
    php artisan migrate
If necessary configure the `.env` file according to the environment

### Write permissions on:
    bootstrap/cache/
    storage/

### Files used
    storage/app/challenge.csv
    storage/app/challenge.json
    storage/app/challenge.xml

### Endpoint
    http://localhost/upload