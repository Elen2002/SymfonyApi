Сначала необходимо зарегистрировать администратора с помощью команды:
	php bin/console create:admin <электронная почта пользователя> <пароль>
После этого нужно сгенерировать API токен с помощью команды:
	php bin/console generate:api:token <электронная почта пользователя> <пароль>,
который впоследствии необходимо будет использовать при выполнении запросов в качестве auth key.

Во всех запросах необходимо в заголовке (header) указывать следующее:

Auth Type: API Key
Key: X-AUTH-TOKEN
Value: Выданный API токен

API
Request
Method: POST
URL : /api/
Body: { }
Response
Status Code : 200 OK
Body : {
  "data": [
        {
            "id": 1,
            "email": "admin@mail.ru",
            "username": "admin",
            "roles": [
                "ROLE_USER",
                "ROLE_ADMIN"
            ]
        },
        {
            "id": 2,
            "email": " nova@mail.ru",
            "username": " nova_dev",
            "roles": [
                "ROLE_USER"
            ]
        }
    ]
}

Request
Method: POST
URL : /api/register
Body: {
    "email": "user@mail.ru", // Обязательно
    "password": "qwerty", // Обязательно
    "username": "user" // Не обязательно
 }
Response
Status Code : 200 OK
Body : {
 "message": "User created"
}







Request
Method: POST
URL :  api/update/email
Body: {
    "email": "user@mail.ru",
    "oldPassword": "qwerty",
    "password": "qwerty1"
}

Response
Status Code : 200 OK
Body : {
    "status": true,
    "message": "User user@mail.ru updated successfully"
}

Request
Method: POST
URL : /api/update/id
Body: {
    "id": 3,
    "username": "USER" // Также можно изменить "password"
}
Response
Status Code : 200 OK
Body : {
    "status": true,
    "message": "User user@mail.ru updated successfully"
}


Request
Method: POST
URL : /api/detail
Body: {
    "email": "user@mail.ru"
}
Response
Status Code : 200 OK
Body : {
    "status": true,
    "user": {
        "id": 3,
        "username": "USER",
        "email": "user@mail.ru",
        "roles": [
            "ROLE_USER"
        ],
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidXNlckBtYWlsLnJ1IiwiZXhwIjoxNzQ0NzM5NzgyfQ.BommK3vOJ7AXI2t_vpWCLwnhFQWafk65oJvKLGA_0FU"
    }
}

Request
Method: POST
URL : /api/detail/id
Body: {
    "id": 3
}
Response
Status Code : 200 OK
Body : {
    "status": true,
    "user": {
        "id": 3,
        "email": "user@mail.ru",
        "username": "USER",
        "roles": [
            "ROLE_USER"
        ],
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidXNlckBtYWlsLnJ1IiwiZXhwIjoxNzQ0NzM5NzgyfQ.BommK3vOJ7AXI2t_vpWCLwnhFQWafk65oJvKLGA_0FU"
    }
}

Request
Method: POST
URL : /api/detail/alternate
Body: {
    "email": "user@mail.ru"
}

Response
Status Code : 200 OK
Body : {
    "status": true,
    "user": {
        "id": 3,
        "email": "user@mail.ru",
        "username": "USER",
        "roles": [
            "ROLE_USER"
        ],
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidXNlckBtYWlsLnJ1IiwiZXhwIjoxNzQ0NzM5NzgyfQ.BommK3vOJ7AXI2t_vpWCLwnhFQWafk65oJvKLGA_0FU"
    }
}

Request
Method: POST
URL : /api/ login
Body: {
    "email": "user@mail.ru",
    "password": "qwerty1"
}

Response
Status Code : 200 OK
Body : {
    "status": true,
    "message": "user@mail.ru User is login",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidXNlckBtYWlsLnJ1IiwiZXhwIjoxNzQ0NzM5NzgyfQ.BommK3vOJ7AXI2t_vpWCLwnhFQWafk65oJvKLGA_0FU"
}


Request
Method: POST
URL : /api /delete/email
Body: {
    "email": "user@mail.ru",
    "password": "qwerty1"
}
Response
Status Code : 200 OK
Body : {
    "status": true,
    "message": "User deleted successfully"
}


Request
Method: POST
URL : /api /delete/id
Body: {
    "id": 3
}

Response
Status Code : 200 OK
Body : {
    "status": true,
    "message": "User deleted successfully"
}
