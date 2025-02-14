# Szallas.hu Test Rest API application

# Table of Contents

- [Minimum Requirements](#minimum-requirements)
- [Docker Services](#docker-services)
- [Installation With Docker](#installation-with-docker)    
- [Installation Without Docker](#installation-without-docker)
- [API Endpoints](#api-endpoints)
- [Optional CLI Commands](#optional-cli-commands)    
- [Testing and Analysis Tools](#testing-and-analysis-tools)    
- [Running Tests](#running-tests)    
- [Docker Installation](#docker-installation)    
- [Docker Compose Installation](#docker-compose-installation)
- [Example Images](#example-images)

## Minimum Requirements

- **Docker and Docker Compose**

**...or without Docker:**

- **PHP**: 8.2 or higher
- **Composer**: 2.0 or higher
- **MySQL**: 5.7 or higher
- **Laravel**: 10.x
- **Node.js 20** or higher

## Docker Services

This project uses Docker to containerize the different components of the application. Below is a description of each service defined in the `docker-compose.yml` file:

- **nginx**: The Nginx service serves as a reverse proxy for the application, routing HTTP requests to the appropriate backend services.

- **php**: This service runs the PHP application (Laravel) using PHP 8.1. It is the core backend service that handles HTTP requests, interacts with the database, and manages the application logic. The service shares the application codebase with the host machine to enable hot-reloading during development.

- **mysql**: This service runs the MySQL database server, which stores the application's data. The database is configured with persistent storage to retain data across container restarts. The database credentials and other environment variables are defined in the `.env` file.

- **phpmyadmin**: A web-based interface for managing MySQL databases. It allows developers to interact with the database, run queries, and manage tables via a user-friendly UI. The service is accessible via a browser on port 80 (or a custom port defined in the `.env` file).

- **node**: This service is responsible for building and serving the Vue.js frontend application. It runs the Node.js server, compiles assets, and serves the frontend during development.

- **php-cli-cron**: A dedicated service for running scheduled cron jobs. This service is built from the `Dockerfile_php_cron` file located in the `docker_settings` directory. It mounts the application code and configuration files from the host machine, ensuring that any changes are reflected in the container. This service depends on the `php` service and uses the shared application codebase.

- **elasticsearch**: This service provides a search engine for the application, enabling efficient and scalable full-text search capabilities. It uses the `docker.elastic.co/elasticsearch/elasticsearch:8.10.1` image and runs as a single-node instance. Persistent data storage ensures that search indexes are retained across container restarts. The service is secured with an environment variable-defined password (`ELASTICSEARCH_PASS`) and is accessible on port `9200`. Memory usage is configured to optimize performance.


## Installation With Docker

First, need a fresh installation of Docker and Docker Compose

### 1. Clone the Project

Clone the repository to your local machine:

```bash
git clone https://github.com/ubul86/szallas_hu_test.git
cd szallas_hu_test
```

### 2. Copy Environment File

Copy the .env.example file to .env

```bash
cp .env.example .env
```

Copy the .env.testing.example file to .env.testing
```bash
cp .env.testing.example .env.testing
```

### 3. Set Environment Variables
In the .env file, you need to set the DB connections and some Host and Elasticsearch params.
Here is an example configuration:

```env
DB_CONNECTION=mysql
DB_HOST=mysql82
DB_PORT=3306
DB_DATABASE=your_database_name # for example szallashu_test
DB_USERNAME=your_database_username # for example szallashu
DB_PASSWORD=your_database_password # for example szallashu
DB_ROOT_PASSWORD=your_database_root_password # for example szallashuadmin

NGINX_PORT=8080
PHPMYADMIN_PORT=45678

...

ELASTICSEARCH_HOST=elasticsearch:9200
ELASTICSEARCH_USER=elastic
ELASTICSEARCH_PASS=elastic

```

The DB_HOST needs to be mysql82 service name.

### 4. Build The Containers

Go to the project root directory, where is the docker-compose.yml file and add the following command:

```bash
docker-compose up -d --build
```

### 5. Install Dependencies:

Install PHP dependencies using Composer:

```bash
docker exec -it {php_fpm_container_name} composer install
```

or
```bash
docker exec -it {php_fpm_container_name} bash
composer install
```

### 6. Generate Application Key

```bash
docker exec -it {php_fpm_container_name} php artisan key:generate
```

or
```bash
docker exec -it {php_fpm_container_name} bash
php artisan key:generate
```


### 7. Run Migrations

Run the database migrations with seed:

```bash
docker exec -it {php_fpm_container_name} php artisan migrate:fresh
```

or

```bash
docker exec -it {php_fpm_container_name} bash
php artisan migrate:fresh
```

### 8. Install Npm Packages


```bash
docker exec -it {node_container_name} npm install
```

or

```bash
docker exec -it {node_container_name} npm install
```

### 10. Migrate datas from the given csv

```bash
docker exec -it {php_fpm_container_name} php artisan import:csv
```

### 10. (Optional) Change User and Group in php container

You may need to change the user and group inside the PHP container if you encounter an error. Unfortunately, this is a known issue when using the application with Docker.

```bash
docker exec -it {php_container} chown -R www-data:www-data *
```

## Installation Without Docker

### 1. Clone the Project

Clone the repository to your local machine:

```bash
git clone https://github.com/yourusername/your-repository.git
cd your-repository
```

### 2. Install Dependencies

Install PHP dependencies using Composer:

```bash
composer install
```

### 3. Copy Environments File

Copy the .env.example file to .env

```bash
cp .env.example .env
```

Copy the .env.testing.example file to .env.testing

```bash
cp .env.testing.example .env.testing
```

### 5. Configure the Database

Create a new database for the project and set the database connection in the .env file. Update the following lines in your .env file. There is an example setting:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### 6. Run Migrations

Run the database migrations:

```bash
php artisan migrate
```

### 7. Import the data from the given csv

Seed the database with from csv data:

```bash
php artisan import:csv
```

### 8. Install Npm Packages

```bash
npm install
```

### 9. Start the Development Server

Run the Laravel development server:

```bash
php artisan serve
```

The application should now be accessible at http://localhost:8000.

## API Endpoints

Detailed information about the available API endpoints can be found in the [API_ENDPOINTS.md](./API_ENDPOINTS.md) file.


## Optional CLI Commands

The project includes several optional CLI commands to manage and process company data. These commands can be executed via `php artisan`.

---

### 1. Import Companies from CSV

**Command:**  
`php artisan import:csv`

**Description:**  
Imports companies along with their associated addresses, owners, and employees based on the `employees` field in the CSV file.

---

### 2. Foundation Date Paginator

**Command:**  
`php artisan company:foundation-date-paginator [--page] [--date-start] [--date-end] [--per-page]`

**Description:**  
Displays all dates since `2001-01-01` with two columns:
- The first column lists the date.
- The second column lists the company name if a company was founded on that day, otherwise remains empty.

**Parameters:**


| **Name**     | **Required** | **Type** | **Description**                                                 |
|--------------|--------------|----------|-----------------------------------------------------------------|
| `page`       | Optional     | integer  | Specifies the page number to list, starting from 1. If not provided, the first page is shown by default.           |
| `per-page`   | Optional     | Integer  | The number of records to return per page. Defaults to 50 if not provided.|
| `date-start` | Optional     | date     | The start date for the date range. Defaults to 2001-01-01 if not provided.                      |
| `date-end`   | Optional     | date     | The end date for the date range. If not provided, the query will use the current date as the end date.


---

### 3. Foundation Date Paginator with Recursive CTE

**Command:**  
`php artisan company:foundation-date-paginator-with-recursive-cte [--page]`

**Description:**  
Similar to the `foundation-date-paginator` command but uses a **recursive CTE** (Common Table Expression) to calculate the results.
When using this command, ensure that `cte_max_recursion_depth` is set to a sufficiently high value in the database configuration. Note: This is not necessary in a **Docker** environment, as it is already configured automatically

---

### 4. Generate Pivot Table of Companies by Activity

**Command:**  
`php artisan company:generate-pivot-companies`

**Description:**  
Generates an aggregated table where:
- **Columns** dynamically represent values from the `activity` field of the `companies` table.
- **Rows** contain the names of companies associated with each activity.

The command invokes and executes a predefined **stored procedure** created through a migration.

---

### Notes

- Ensure the database configurations are properly set for commands involving **CTEs** or **stored procedures**.

## Testing and Analysis Tools

### PHP CodeSniffer (PHPCS)

PHPCS is used to check coding standards and style violations.

```bash
composer lint
```

or

```bash
docker exec -it {php_fpm_container} composer lint
```

### PHPStan

PHPStan is used for static code analysis to find bugs and improve code quality.

Run PHPStan:

```bash
composer analyse
```

or

```bash
docker exec -it {php_fpm_container} composer analyse
```

Note: You might need to update your phpstan.neon configuration if you encounter issues or deprecations.

### PHPUnit Coverage XML to SonarQube

To generate code coverage data and send it to SonarQube, run the PHPUnit tests with coverage enabled and specify the output file for the coverage report:

```bash
php artisan test --coverage-clover=tests/_output/coverage.xml
```

### SonarQube

1. **Login to SonarQube:**

    Access SonarQube at the following address: http://sonarqube:9000. The default login credentials are:

    Username: admin
    Password: admin

2. **Create a New Project:**

   After logging in, click on Create Project. 
   Follow the steps to create a new project by naming it and setting up the necessary parameters.
   
3. **Generate a Token:**

    Once the project is created, go to My Account > Security.
    Under the Tokens section, generate a new token and save it. You will use this token for authentication when running SonarScanner.

### Sonar Scanner Client

To run SonarQube analysis using the SonarScanner client, follow these steps:

#### With Docker:

1. Run the docker-compose command
   ```bash
   docker-compose run sonar-scanner
   ```
   
#### Without Docker:

1. Install SonarScanner:

    Install the SonarScanner on your local machine or CI/CD pipeline environment. Follow the installation guide on the official SonarScanner website.

2. Run the Scanner:

    Once SonarScanner is installed and configured, navigate to the project directory and run the following command, replacing YOUR_SONARQUBE_TOKEN with the token generated earlier:

    ```bash
    sonar-scanner \
    -Dsonar.projectKey=your_project_key \
    -Dsonar.sources=. \
    -Dsonar.host.url=http://sonarqube:9000 \
    -Dsonar.login=YOUR_SONARQUBE_TOKEN
    ```
    This will send the code analysis results to SonarQube.


## Running Tests

### PHPUnit

Unit tests are written using PHPUnit. To run tests, first configure the .env.testing file

Run the tests:

```bash
php artisan test
```

or

```bash
docker exec -it {php_fpm_container} php artisan test
```


This will execute all tests in the tests directory and provide a summary of test results.

## Docker Installation

### Linux

- Ubuntu: https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-20-04
- For Linux Mint: https://computingforgeeks.com/install-docker-and-docker-compose-on-linux-mint-19/

### Windows

- https://docs.docker.com/desktop/windows/install/

## Docker Compose Installation

### Linux

https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-compose-on-ubuntu-20-04

### Windows
- Docker automatically installs Docker Compose.

## Example Images

![List Companies](documents/sample_images/company_list.png )
![List Companies Mobile](documents/sample_images/company_list_mobile.png )
![Edit Company](documents/sample_images/edit_company.png )
![View Company Details](documents/sample_images/view_company_details.png )
![Company Foundation Date Result](documents/sample_images/company_foundation_date_result.png )
![Pivot Table Result](documents/sample_images/pivot_table_result.png )
![PHPStan Result](documents/sample_images/phpstan_result.png )
![SonarQube Result](documents/sample_images/sonarqube_result.png )
![PHPMetrics Result](documents/sample_images/phpmetrics_result.png )

