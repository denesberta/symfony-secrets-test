# Secret Server

A simple web service for storing and retrieving secrets. The service is built using the Symfony framework and is containerized with Docker.

## Features

- Create and retrieve secrets.
- Secrets are deleted after being viewed once.
- Set an expiration time for secrets.
- Supports both JSON and XML response formats.

## Technology Stack

- PHP
- Symfony
- Doctrine ORM
- Nginx
- Docker

## Prerequisites

- Docker
- Docker Compose

## Getting Started

1.  **Clone the repository**
    ```bash
    git clone <repository-url>
    cd secret-server
    ```

2.  **Set up environment variables**

    Navigate to the `.docker` directory and create a local `.env` file from the example file.
    ```bash
    cd .docker
    cp .env.example .env
    ```
    You can modify the variables in `.env` if needed.

3.  **Build and run the containers**
    ```bash
    docker-compose up -d --build
    ```

4.  **Install PHP dependencies**
    ```bash
    docker-compose exec php composer install
    ```

5.  **Run database migrations**
    ```bash
    docker-compose exec php bin/console doctrine:migrations:migrate
    ```

The application should now be running and accessible at `http://localhost:8000` (or the port you configured in your `.env` file).

## API Usage

The API supports both JSON (`application/json`) and XML (`application/xml`) formats. Set the `Accept` and `Content-Type` headers accordingly.

### Create a Secret

- **Endpoint:** `POST /api/v1/secret`
- **Description:** Creates a new secret in the database.
- **Request Body:**
  ```json
  {
    "secret": "This is a secret message.",
    "expireAfterViews": 2,
    "expireAfter": 3600
  }
  ```
  - `secret` (string): The secret text to be stored.
  - `expireAfterViews` (integer): The maximum number of times the secret can be viewed before it's deleted.
  - `expireAfter` (integer): The number of minutes after which the secret will expire and be deleted.

### Get a Secret

- **Endpoint:** `GET /api/v1/secret/{hash}`
- **Description:** Retrieves a secret by its unique hash. The secret is deleted after being retrieved.
- **Example:**
  ```bash
  curl -X GET http://localhost:8080/api/v1/secret/your-secret-hash -H "Accept: application/json"
  ```

## Running Tests

To run the PHPUnit test suite, execute the following command from the root directory:

```bash
docker-compose exec php ./vendor/bin/phpunit
```
