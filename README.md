# About the UserBlog

This is a mini API for managing user articles

## 1. Clone the repository

```shell
git clone git@github.com:abdeljabar/userblog.git
```

## 2. CD to the new directory

```shell
cd userblog
```

## 3. Run and build the docker containers

```shell
docker compose up -d --build
```

## 4. Run composer install

```shell
docker compose run php composer install
```

## 5. Generate jwt keypair

```shell
docker compose run php bin/console lexik:jwt:generate-keypair
```

## 6. Link to the API

```shell
http://localhost:8000
```

# Auto testing The API

To run the tests:
```shell
docker compose run php APP_ENV=test bin/phpunit
```

# Getting started with the Api

## To create/register a new user

```shell
POST http://localhost:8000/users
```

The json body:
```json
{
    "name": "John Doe",
    "email": "taoufikallah@gmail.com",
    "plainPassword": "myPassword"
}
```

## User authentication

To login please send json in the body of this uri. A fresh token will be generated for you to use in the endpoints that need authentication.

```shell
POST http://localhost:8000/login_check
```

The json body:

```json
{
    "email": "some@email.com",
    "password": "password"
}
```

Response body:

```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

Add the new generated token to the headers of the rest of the endpoints like this:

```shell
-H 'Authorization: Bearer YOUR_TOKEN_HERE'
```

## To update logged user

```shell
PUT http://localhost:8000/update-profile
```

The json body:

```json
{
    "name": "Jane Doe"
}
```

## Get all articles

```shell
GET http://localhost:8000/articles
```

## To create a new article

```shell
POST http://localhost:8000/articles
```

The json body:
```json
{
    "title": "My title",
    "content": "This is a new post about IT"
}
```

## Get a single article

```shell
GET http://localhost:8000/articles/{id}
```

## To update an article

```shell
PUT http://localhost:8000/articles/{id}
```

The json body:
```json
{
    "title": "The New Generation Of AI"
}
```

## To delete an article

```shell
DELETE http://localhost:8000/articles/{id}
```
