# API Documentation

## 1. Get All Companies or a Selected Company

**Endpoint:**  
`GET /api/company`

Retrieve all companies or a selected list of companies by their IDs. This endpoint supports pagination through query parameters.

### Query Parameters

| **Name**          | **Required** | **Type**                                | **Description**                                                 |
|-------------------|--------------|-----------------------------------------|-----------------------------------------------------------------|
| `ids`             | Optional     | List of strings (comma-separated)       | List of company IDs to retrieve specific companies.             |
| `itemsPerPage`    | Optional     | Integer                                 | Number of companies per page for pagination (default: 10).      |
| `page`            | Optional     | Integer                                 | The page number to retrieve (default: 1).                       |


#### Response Example

```json
{
    "success": true,
    "message": "Request successful",
    "data": {
        "items": [
            {
                "id": 2,
                "name": "Magna Lorem Inc.",
                "registration_number": "896098-0855",
                "foundation_date": "1991-10-22T00:00:00.000000Z",
                "activity": "Building Industry",
                "active": 0,
                "created_at": "2024-11-27T13:30:52.000000Z",
                "updated_at": "2024-11-27T13:30:52.000000Z",
                "addresses": [
                    {
                        "id": 2,
                        "company_id": 2,
                        "country": "Philippines",
                        "city": "Bahawalnagar",
                        "zip_code": "60934",
                        "street_address": "6753 Sit Street",
                        "latitude": "4.696710",
                        "longitude": "-101.066120",
                        "created_at": "2024-11-27T13:30:52.000000Z",
                        "updated_at": "2024-11-27T13:30:52.000000Z"
                    }
                ],
                "employees": [],
                "owners": [
                    {
                        "id": 2,
                        "company_id": 2,
                        "name": "Hamilton Pearson",
                        "active": 1,
                        "order": 1,
                        "created_at": "2024-11-27T13:30:52.000000Z",
                        "updated_at": "2024-11-27T13:30:52.000000Z"
                    }
                ]
            }
        ],
        "meta": {
            "current_page": 1,
            "total_pages": 1,
            "total_items": 1,
            "items_per_page": 10
        }
    }
}
```


---

## 2. Show Information from Company

**Endpoint:**  
`GET /api/company/{id}`

Retrieve details for a specific company by its ID.

#### Response Example

```json
{
    "id": 1,
    "name": "Mauris PC",
    "registration_number": "177874-5578",
    "foundation_date": "1990-05-01T00:00:00.000000Z",
    "activity": "Car",
    "active": 0,
    "created_at": "2024-11-27T13:30:52.000000Z",
    "updated_at": "2024-11-27T13:30:52.000000Z"
}
```

or an error message:
```json
{
  "code": 404,
  "success": false,
  "message": "An error occurred",
  "errors": "Company not found 1"
}
```

---

## 3. Create a New Company

**Endpoint:**  
`POST /api/company`

Create a new company. The request body must contain the following fields:

### Post Parameters

| **Name**              | **Required** | **Type**         | **Description**                                                  |
|-----------------------|--------------|------------------|------------------------------------------------------------------|
| `name`                | Required     | String           | The name of the company.                                         |
| `registration_number` | Required     | String           | The registration number of the company.                          |
| `foundation_date`     | Required     | Date (YYYY-MM-DD)| The foundation date of the company.                              |
| `activity`            | Required     | String           | The main activity of the company (e.g., "Car", "Software").      |
| `active`              | Required     | Boolean          | Whether the company is active (`true`) or inactive (`false`).    |

#### Response Example

```json
{
    "name": "Test Company",
    "registration_number": "asd123",
    "foundation_date": "2001-01-01",
    "activity": "Car",
    "active": true
}
```

or an error message:
```json
{
    "code": 422,
    "success": false,
    "message": "An error occurred",
    "errors": {
        "registration_number": [
            "The registration number has already been taken."
        ]
    }
}
```

## 4. Update a Company

**Endpoint:**  
`PUT /api/company/{id}`

Update an existing company by its ID. You can modify the fields as needed. Note that if the foundation_date is updated, an error will be returned if it has already been set previously.


### Put Parameters

| **Name**              | **Required** | **Type**         | **Description**                                                  |
|-----------------------|--------------|------------------|------------------------------------------------------------------|
| `name`                | Required     | String           | The name of the company.                                         |
| `registration_number` | Required     | String           | The registration number of the company.                          |
| `foundation_date`     | Required     | Date (YYYY-MM-DD)| The foundation date of the company (cannot be changed if already set).                              |
| `activity`            | Required     | String           | The main activity of the company (e.g., "Car", "Software").      |
| `active`              | Required     | Boolean          | Whether the company is active (`true`) or inactive (`false`).    |

#### Response Example

```json
{
    "name": "Test Company",
    "registration_number": "asd123",
    "foundation_date": "2001-01-01",
    "activity": "Car",
    "active": true
}
```

or an error message:

```json

{
    "code": 422,
    "success": false,
    "message": "An error occurred",
    "errors": {
        "foundation_date": [
            "Company Foundation Date Cant modified."
        ]
    }
}

```

## 5. Delete a Company

**Endpoint:**  
`DELETE /api/company/{id}`

Delete a company by its ID.

#### Response Example

```json
{
    "success": true,   
}
```

or an error message:

```json
{
    "code": 404,
    "success": false,
    "message": "An error occurred",
    "errors": "Company not found: No query results for model [App\\Models\\Company] 1"
}
```
