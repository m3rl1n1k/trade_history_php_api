# API Documentation V1

This API provides the ability to manage subcategories for authenticated users. All routes require the user to be fully
authenticated.

## Base URL

http://localhost/api/v1

## Authentication

- All endpoints are protected and require the user to be authenticated
- When login is successfully, you get `Bearer token`. Which need send with all requests in header `Authoriztion`
  for
  protect pages

## Notes

The request body for POST, PATCH, and PUT requests must be in JSON format.
Ensure that you handle user authentication and authorization appropriately before accessing any of the endpoints.

---

## Endpoints

<details>
<summary>
Main Category
</summary>

### 1. Get all main categories

#### **GET** `/categories/main/`

Retrieves a list of main categories for the authenticated user.

##### **Request**

- **Method**: `GET`
- **Headers**: `Authorization: Bearer token`

##### **Response**

- **Status Code**: `200 OK`
- **Body**:
  ```json
    {
      "main_categories": [
        {
          "id": 1,
          "name": "Category Name",
          "color": "#FFFFFF"
        }
      ]
    }

##### **Error Responses**

- **Status Code**:
    - `200 OK` Successfully retrieved categories.
    - `403 Forbidden` If the user does not have permission to access the resource.
    - `404 Not Found`  Category not found.

### 2. Get Single Category

#### **GET**  `/categories/main/{id}`

Retrieves a specific main category by ID.

##### **Request**

- **Method**: `GET`
- **Headers**: `Authorization: Bearer token`

##### **Parameters**:

- **id**: `Category ID (integer)`

##### **Response**

- **Status Code**: `200 OK`
- **Body**:
  ```json
    {
      "main_category": {
        "id": 1,
        "name": "Category Name",
        "color": "#FFFFFF"
      }
    }

##### **Error Responses**

- **Status Code**:
    - `200 OK` Successfully retrieved categories.
    - `403 Forbidden` If the user does not have permission to access the resource.
    - `404 Not Found`  Category not found.

### 3. Create New Category

#### **POST**  `/categories/main/new`

Creates a new category for the authenticated user.

##### **Request**

- **Method**: `POST`
- **Headers**: `Authorization: Bearer token`
- **Body**:
  ```json
    {
    "name": "New Category",
    "color": "#00ff00",
    "main": {
            "url": "/main-category-url"
            }
    }

#### **Response**

- **Status Code**: `201 Created`
- **Body**:
  ```json
    {
    "message": "Category 'New Category' created"
    }

##### **Error Responses**

- **Status Code**: `404 Not Found`  If the main category provided in the request body does not exist.
- **Status Code**: `400 Bad Request`  If the request body is invalid.

### 4. Update Category

#### **PATCH/PUT** `/categories/main/edit/{id}`

Updates an existing category.

#### **Request**

- **Method**: `PATCH` or `PUT`
- **Headers**: `Authorization: Bearer token`

##### **Parameters**:

- **id**: `Category ID (integer)`
- **Body**:
  ```json
    {
    "name": "Updated Category",
    "color": "#0000ff",
    "main": {
            "url": "/updated-main-category-url"
            }
    }

#### **Response**

- **Status Code**: `200 OK`
- **Body**:
  ```json
    {
    "message": "Category 'Updated Category' updated"
    }

##### **Error Responses**

- **Status Code**: `404 Not Found`  If the category with the provided ID does not exist.
- **Status Code**: `403 Forbidden` If the user does not have permission to update the resource.
- **Status Code**: `400 Bad Request` If the request body is invalid.

### 5. Delete Category

##### **DELETE** `/categories/main/delete/{id}`

Deletes an existing category.

#### **Request**

- **Method**: `DELETE`
- **Headers**: `Authorization: Bearer token`

##### **Parameters**:

- **id**: `Category ID (integer)`

#### **Response**

- **Status Code**: `200 OK`
- **Body**:
  ```json
    {
    "message": "Category 'Deleted Category' deleted"
    }

##### **Error Responses**

- **Status Code**: `404 Not Found` If the category with the provided ID does not exist.
- **Status Code**: `403 Forbidden` If the user does not have permission to delete the resource.
- **Status Code**: `400 Bad Request` If there is an error during the deletion process.

</details>

<details>
<summary>
Sub Category
</summary>

### 1. Get all sub categories

#### **GET** `/categories/sub/`

Retrieves a list of categories associated with the authenticated user.

##### **Request**

- **Method**: `GET`
- **Headers**: `Authorization: Bearer token`

##### **Response**

- **Status Code**: `200 OK`
- **Body**:
  ```json
  {
    "categories": [
      {
        "id": 1,
        "name": "Category 1",
        "color": "#ff0000",
        "main": "/category/main/ID"
      },
    ]
  }

##### **Error Responses**

- **Status Code**: `403 Forbidden` If the user does not have permission to access the resource.

### 2. Get Single Category

#### **GET**  `/categories/sub/{id}`

Retrieves details of a specific category by its ID.

##### **Request**

- **Method**: `GET`
- **Headers**: `Authorization: Bearer token`

##### **Parameters**:

- **id**: `Category ID (integer)`

##### **Response**

- **Status Code**: `200 OK`
- **Body**:
  ```json
  {
    "sub_category": [
      {
        "id": 1,
        "name": "Category 1",
        "color": "#ff0000",
        "main": "/category/main/ID"
      },
    ]
  }

##### **Error Responses**

- **Status Code**: `404 Not Found`  If the category with the provided ID does not exist.
- **Status Code**: `403 Forbidden`  If the user does not have permission to access the resource.

### 3. Create New Category

#### **POST**  `/categories/sub/new`

Creates a new category for the authenticated user.

##### **Request**

- **Method**: `POST`
- **Headers**: `Authorization: Bearer token`
- **Body**:
  ```json
    {
    "name": "New Category",
    "color": "#00ff00",
    "main": {
            "url": "/main-category-url"
            }
    }

#### **Response**

- **Status Code**: `201 Created`
- **Body**:
  ```json
    {
    "message": "Category 'New Category' created"
    }

##### **Error Responses**

- **Status Code**: `404 Not Found`  If the main category provided in the request body does not exist.
- **Status Code**: `400 Bad Request`  If the request body is invalid.

### 4. Update Category

#### **PATCH/PUT** `/categories/sub/edit/{id}`

Updates an existing category.

#### **Request**

- **Method**: `PATCH` or `PUT`
- **Headers**: `Authorization: Bearer token`

##### **Parameters**:

- **id**: `Category ID (integer)`
- **Body**:
  ```json
    {
    "name": "Updated Category",
    "color": "#0000ff",
    "main": {
            "url": "/updated-main-category-url"
            }
    }

#### **Response**

- **Status Code**: `200 OK`
- **Body**:
  ```json
    {
    "message": "Category 'Updated Category' updated"
    }

##### **Error Responses**

- **Status Code**: `404 Not Found`  If the category with the provided ID does not exist.
- **Status Code**: `403 Forbidden` If the user does not have permission to update the resource.
- **Status Code**: `400 Bad Request` If the request body is invalid.

### 5. Delete Category

##### **DELETE** `/categories/sub/delete/{id}`

Deletes an existing category.

#### **Request**

- **Method**: `DELETE`
- **Headers**: `Authorization: Bearer token`

##### **Parameters**:

- **id**: `Category ID (integer)`

#### **Response**

- **Status Code**: `200 OK`
- **Body**:
  ```json
    {
    "message": "Category 'Deleted Category' deleted"
    }

##### **Error Responses**

- **Status Code**: `404 Not Found` If the category with the provided ID does not exist.
- **Status Code**: `403 Forbidden` If the user does not have permission to delete the resource.
- **Status Code**: `400 Bad Request` If there is an error during the deletion process.

</details>    