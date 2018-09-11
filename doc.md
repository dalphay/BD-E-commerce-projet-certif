# E-commerce api doc

## Routes

### `User` Resource

This resource is provided by UserController and SecurityController (which manage login and logout logic).

| Method  | Route          | Min access level | Description               | Payload example                                                                                                                                   |
| ------- | -------------- | ---------------- | ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| `GET`   | `/user`        | `ROLE_USER`      | get current logged user   |                                                                                                                                                   |
| `POST`  | `/user`        | `ANONYMOUS`      | registering a new user    | ```{ "name": "Paul", "surname": "Paul", "gender": 1, "email": "paul@paul.pl", "address": "123 rue bidon 12345 Néant", "password": "Paul1234" }``` |
| `PATCH` | `/user`        | `ROLE_USER`      | update current user datas | ```{ "name": "Paul", "surname": "Paul", "gender": 1, "email": "paul@paul.pl" }```                                                                 |
| `POST`  | `/user/login`  | `ANONYMOUS`      | connect user              | ```{ "email": "paul@paul.pl", "password": "Paul1234" }```                                                                                         |
| `GET`   | `/user/logout` | `ROLE_USER`      | disconnect user           |                                                                                                                                                   |

### `ShoppingCart` Resource

This resource is provided by ShoppingCartController.

| Method   | Route                                   | Min access level | Description                         | Payload example     |
| -------- | --------------------------------------- | ---------------- | ----------------------------------- | ------------------- |
| `GET`    | `/user/shopping_cart`                   | `ROLE_USER`      | get current user's shopping cart    |                     |
| `POST`   | `/user/shopping_cart/product/{product}` | `ROLE_USER`      | adds a product to shopping cart     | ``` { "qty": 1 }``` |
| `PUT`    | `/user/shopping_cart/product/{product}` | `ROLE_USER`      | update product quantity             | ``` { "qty": 1 }``` |
| `DELETE` | `/user/shopping_cart/product/{product}` | `ROLE_USER`      | remove a product from shopping cart |                     |

### `Product` Resource

This resource is provided by ProductController

| Method   | Route                | Min access level | Description            | Payload example                                                                                                                         |
| -------- | -------------------- | ---------------- | ---------------------- | --------------------------------------------------------------------------------------------------------------------------------------- |
| `GET`    | `/product`           | `ANONYMOUS`      | get all products       |                                                                                                                                         |
| `GET`    | `/product/{product}` | `ANONYMOUS`      | get one product        |                                                                                                                                         |
| `POST`   | `/product`           | `ROLE_ADMIN`     | creating a new product | ``` { "name": "thing'o'matic", "description": "this thing is automatic", "price": "1000", "base64Image": "<BASE 64 ENCODED IMAGE>" }``` |
| `PATCH`  | `/product/{product}` | `ROLE_ADMIN`     | updating a product     | ``` { "name": "thing'o'matic", "description": "this thing is automatic", "price": "1000" }```                                           |
| `DELETE` | `/product/{product}` | `ROLE_ADMIN`     | deleting a product     |                                                                                                                                         |

### Roles hierarchy

See `config/package/security.yaml` for hierarchy declaration.
Each route access levels are defined inside controllers annotations with `@IsGranted` flag.

| Role         | Rights                                                                                                     |
| ------------ | ---------------------------------------------------------------------------------------------------------- |
| `ANONYMOUS`  | Lambda user, can see products, register and login                                                          |
| `ROLE_USER`  | `ANONYMOUS` + seeing or editing his profile and seeing, adding or deleting products from his shopping cart |
| `ROLE_ADMIN` | `ROLE_USER` + adding, updating or deleting products                                                        |