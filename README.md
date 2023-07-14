# MyIT Sample Application: PHP and React

## Introduction

MyIT is a DocuSign sample application written in PHP (server) and React (client).

MyIT demonstrates the following:

1. **Authentication** with DocuSign via [JSON Web Token (JWT) Grant](https://developers.docusign.com/platform/auth/jwt/).
2. **Bulk send of multiple envelopes:**
   This example uses the DocuSign [eSignature REST API](https://developers.docusign.com/docs/esign-rest-api/) to [Bulk Send](https://developers.docusign.com/docs/esign-rest-api/reference/bulkenvelopes/bulksend/) multiple envelopes based on PDF document template, and fills data dynamically.
3. **Bulk assignment of permission profiles to employees:**
   This example uses the DocuSign [DocuSign Admin API](https://developers.docusign.com/docs/admin-api/) to [Update Users'](https://developers.docusign.com/docs/admin-api/reference/usermanagement/esignusermanagement/updateuser/) permission profiles in a single request.


## Prerequisites

- Create a DocuSign developer [account](https://go.docusign.com/o/sandbox/).
- Create an application on the [Apps and Keys](https://admindemo.docusign.com/authenticate?goTo=appsAndKeys) page and copy credentials to `backend/.env`:
  client ID (integration key), user ID, account ID and copy **RSA private key** to a file `storage/docusign_private.key`.
  This [**video**](https://www.youtube.com/watch?v=GgDqa7-L0yo) demonstrates how to create an integration key (client ID) for a user application like this example.
- Add redirect URI `{ PROTOCOL }://{ DOMAIN }/callback`
- Create [permission profiles](https://admindemo.docusign.com/authenticate?goTo=roles) with names: Admin, Manager and Employee
- [PHP 8.1](https://www.php.net/downloads.php)
- [Docker](https://www.docker.com/) installed and configured in your machine.
- [Composer](https://getcomposer.org/download/) set up in your PATH environment variable so you can invoke it from any folder.

> For first time use, paste login endpoint URL and grant consent to the app.

### Variables configuration

Create a copy of the file backend/.env.example, save the copy as backend/.env, and fill in the data:

- MANAGER_LOGIN - email used for getting access token for endpoints
- MANAGER_PASSWORD - password used for getting access token for endpoints
- CLIENT_URL - URL of the client, which is using CORS for security
- DOCUSIGN_BASE_URL - `https://demo.docusign.net/restapi` for development environment
- DOCUSIGN_CLIENT_ID - integration key GUID
- DOCUSIGN_USER_ID - impersonated user ID
- DOCUSIGN_ACCOUNT_ID - API account ID

## Local installation instructions

1. Build the images using the following command:
   
   ```
   docker compose -f docker-compose-local.yml build
   ```

2. Start the containers using the following command. Add the `-d` flag to run the process in the background:

   ```
   docker compose -f docker-compose-local.yml up
   ```

3. Open a new terminal and set the application key by running the command:

   ```
   docker exec -it myit_php php artisan key:generate
   ```

4. Install the database structure using these two commands:

   ```
   docker exec -it myit_php php artisan migrate --seed
   docker exec -it myit_php php artisan passport:install
   ```

5. Clear the cache:

   ```
   docker exec -it myit_php php artisan cache:clear
   ```

6. Open a browser to [localhost](http://localhost).
