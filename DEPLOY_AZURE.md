# Deploying to Azure App Service with Docker

This guide provides step-by-step instructions to deploy the Nostalgia API to Azure App Service using a Docker image from Docker Hub.

## Prerequisites

- An active [Azure subscription](https://azure.microsoft.com/free/).
- [Azure CLI](https://docs.microsoft.com/cli/azure/install-azure-cli) installed.
- [Docker Desktop](https://www.docker.com/products/docker-desktop) installed.
- A [Docker Hub](https://hub.docker.com/) account.

## 1. Build and Push the Docker Image

These steps are automated by the `.github/workflows/azure-dockerhub-deploy.yml` workflow. However, you can run them manually if needed.

1.  **Log in to Docker Hub:**
    ```bash
    docker login -u YOUR_DOCKERHUB_USERNAME
    ```

2.  **Build the Docker image:**
    ```bash
    docker build -t omarabdelazeem/nostalgia:latest .
    ```

3.  **Push the image to Docker Hub:**
    ```bash
    docker push omarabdelazeem/nostalgia:latest
    ```

## 2. Configure Azure App Service

These steps assume you have already created a Resource Group, App Service Plan, and Web App as described in the initial request.

1.  **Navigate to your Web App** (`nostalgia-api`) in the Azure Portal.
2.  Go to **Deployment Center** in the left-hand menu.
3.  **Configure the following settings:**
    -   **Source:** Docker Hub
    -   **Repository:** `omarabdelazeem/nostalgia`
    -   **Tag:** `latest`
    -   **Private repository:** Off (since it's a public repo)
4.  Click **Save**. Azure will pull the latest image and restart your app.

## 3. Configure Application Settings

Your application needs several environment variables to run correctly.

1.  In your Web App, go to **Configuration** > **Application settings**.
2.  Click **New application setting** and add the following key-value pairs:
    -   `APP_KEY`: Generate a new key with `php artisan key:generate --show` and paste the `base64:...` value.
    -   `APP_ENV`: `production`
    -   `APP_DEBUG`: `false`
    -   `APP_URL`: `https://nostalgia-api.azurewebsites.net`
    -   `DB_CONNECTION`: `sqlite`
    -   `DB_DATABASE`: `/home/data/database.sqlite`
3.  Click **Save**. This will restart your app.

## 4. Final Setup via SSH

Connect to your App Service container to run the final setup commands.

1.  Go to **SSH** in the left-hand menu of your Web App and click **Go**.
2.  Run the following commands one by one in the SSH terminal:
    ```bash
    # Create the SQLite database file
    touch /home/data/database.sqlite

    # Set the correct ownership
    chown www-data:www-data /home/data/database.sqlite

    # Run database migrations
    php artisan migrate --force

    # Create the storage link
    php artisan storage:link
    ```

Your application should now be live at `https://nostalgia-api.azurewebsites.net`.

## Troubleshooting

-   **500 Server Error:**
    -   **Missing `APP_KEY`:** Ensure the `APP_KEY` is set in the application settings.
    -   **Database Path:** Double-check that `DB_DATABASE` is set to the correct persistent path.
    -   **File Permissions:** The `Dockerfile` should handle this, but you can verify permissions on `/var/www/html/storage` and `/var/www/html/bootstrap/cache` via SSH.
-   **Check Logs:**
    -   Go to **Log stream** in your Web App to view real-time logs from your application and the web server. This is the best place to diagnose errors.
