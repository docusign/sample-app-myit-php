## How to Run Locally

1. Put all the necessary variables into the `./backend/.env` file.

2. Copy the `docusign_private.key` file to the `./backend/storage/` directory.

3. Run the stack using the following command. If you want to run it in the background, add the `-d` flag:

   ```
   docker compose -f docker-compose-local.yml up
   ```

4. Set the application key by running the following command:

   ```
   docker exec -it myit_php php artisan key:generate
   ```

5. Install the database structure:

   ```
   docker exec -it myit_php php artisan migrate --seed
   docker exec -it myit_php php artisan passport:install
   ```

6. Clear the cache:

   ```
   docker exec -it myit_php php artisan cache:clear
   ```

By following these steps, you should be able to run the application locally.

Please note that these instructions assume you have Docker and Docker Compose installed and configured on your machine.