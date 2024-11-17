# News Aggregator Project

## Setup Instructions

### Prerequisites
- PHP 8.2 or later
- Composer
- Docker (Docker Desktop/Engine)
- Docker Compose
- MySQL 5.7 or later

### Installation

1. Clone the Repository:
   ```bash
   git clone https://github.com/syedhaziqhamdani/news-aggregator.git
   cd news-aggregator
   ```

2. Install Dependencies:
   ```bash
   composer install
   ```

3. Set Up Environment:
   - Duplicate the `.env.example` file and rename it to `.env`.
   - Update `.env` with your database and application configuration.

4. Generate Application Key:
   ```bash
   php artisan key:generate
   ```

### Running the Docker Environment

#### Prerequisites:
- Docker (Docker Desktop/Engine)
- Docker Compose

#### Project Structure:
Ensure your Laravel project includes the following files and directories:

```
laravel-project/
├── docker/nginx/default.conf
├── docker-compose.yml
├── Dockerfile
├── .env
├── Laravel source files
```

#### Required Files:

`Dockerfile`:
```dockerfile
FROM php:8.2-fpm
RUN apt-get update && apt-get install -y git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY . /var/www
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www
EXPOSE 9000
CMD ["php-fpm"]
```

`docker-compose.yml`:
```yaml
version: '3.8'
services:
  app:
    build: .
    container_name: laravel_app
    volumes:
      - .:/var/www
    ports:
      - "9000:9000"

  webserver:
    image: nginx:alpine
    container_name: laravel_webserver
    volumes:
      - .:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    ports:
      - "8080:80"
    depends_on:
      - app

  db:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: laravel
      MYSQL_USER: laravel
      MYSQL_PASSWORD: password
    ports:
      - "3306:3306"
```

`docker/nginx/default.conf`:
```nginx
server {
    listen 80;
    root /var/www/public;
    index index.php index.html;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

#### Update `.env` File:
```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=password
```

#### Build and Run Docker Containers:
1. Build and start containers:
   ```bash
   docker-compose build
   docker-compose up -d
   ```

2. Install Laravel dependencies and generate app key:
   ```bash
   docker exec -it laravel_app bash
   composer install
   php artisan key:generate
   exit
   ```

3. Access the application:
   - Laravel: [http://localhost:8080](http://localhost:8080)
   - MySQL: `localhost:3306`

#### Troubleshooting:
- Containers not starting: Ensure Docker is running.
- Database connection issues: Verify `.env` matches `docker-compose.yml`.
- Permissions issues: Run in container:
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```

#### Stop and Clean Up:
```bash
docker-compose down  # Stop containers
docker-compose down --volumes  # Remove volumes
```

---

## API Documentation

To view the API documentation, visit the hosted Swagger/OpenAPI docs:
[News Aggregator API Documentation](https://app.swaggerhub.com/apis/SyedHaziqHamdani/news-aggregator_api/1.0.0)

---

## Git Instructions

### Cloning the Repository
1. Open a terminal and navigate to your desired directory.
2. Clone the repository:
   ```bash
   git clone https://github.com/syedhaziqhamdani/news-aggregator.git
   ```
3. Navigate to the project directory:
   ```bash
   cd news-aggregator
   ```

### Working with Git

#### Creating a New Branch
1. Create and switch to a new branch:
   ```bash
   git checkout -b <branch-name>
   ```

#### Making Changes
1. Stage your changes:
   ```bash
   git add .
   ```
2. Commit your changes with a descriptive message:
   ```bash
   git commit -m "Your commit message"
   ```

#### Pushing Changes
1. Push your branch to the remote repository:
   ```bash
   git push origin <branch-name>
   ```

#### Pulling Latest Changes
1. Fetch and merge the latest changes from the main branch:
   ```bash
   git pull origin main
   ```

#### Merging a Branch
1. Switch to the branch you want to merge into (e.g., `main`):
   ```bash
   git checkout main
   ```
2. Merge your feature branch:
   ```bash
   git merge <branch-name>
   ```

#### Deleting a Branch
1. Delete a branch locally:
   ```bash
   git branch -d <branch-name>
   ```
2. Delete a branch remotely:
   ```bash
   git push origin --delete <branch-name>
   ```

