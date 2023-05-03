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
- Create an application on the [App and keys](https://admindemo.docusign.com/authenticate?goTo=appsAndKeys) page and copy credentials to `backend/.env`:
  client ID (integration key), user ID, account ID and copy **RSA private key** to a file `storage/docusign_private.key`.
  This [**video**](https://www.youtube.com/watch?v=GgDqa7-L0yo) demonstrates how to create an integration key (client ID) for a user application like this example.
- Add redirect URI `{ PROTOCOL }://{ DOMAIN }/callback`
- Create [permission profiles](https://admindemo.docusign.com/authenticate?goTo=roles) with names: Admin, Manager and Employee
- [PHP 8.1](https://www.php.net/downloads.php)

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

## Installation

### - Backend

#### - Environment

Copy `backend/docker-compose.yml.local` to `backend/docker-compose.yml` and run a command `docker-compose up -d`.

#### - Packages installation

Enter a php container, by command execution `docker exec -ti myit_php /bin/bash` 
and install composer dependencies via `composer install` command.

#### - Setting application key

```shell
    php artisan key:generate
```

#### - Database

Install database structure

```shell
    php artisan migrate --seed
```

#### - Dependency configuration

Configure dependencies, execute following commands in php container:

- Dependency for authentication

```shell
    php artisan passport:install
```