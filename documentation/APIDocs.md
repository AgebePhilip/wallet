

 # Wallet System API Documentation
## Overview
The Wallet System API allows users to register accounts, manage wallets, and perform transactions. Admin users have additional functionalities like reviewing monthly payment summaries and approving large transactions.

### Base URL for this api is:
 http://127.0.0.1:8000/api

## uthentication
User Authentication
Endpoint: /login
Method: POST
Description: Authenticates a user and returns an authentication token.
##

## Request Body:
```json
{
    "phone_number": "07099446637",
    "password": "mercy123"
}


Success Response:

Status: 200 OK
json
{
    "message": "User successfully signed in",
    "phone_number": "07099446637",
    "token": "5|mMOf361eTaardmZfHBMZjlTOxSSngIrKDoVR7lHN3d7a3b4a"
}
Error Response:

Status: 401 Unauthorized
json

{
    "error": "Invalid credentials"
}


Admin Authentication
Endpoint: /admin/login
Method: POST
Description: Authenticates an admin and returns an authentication token.

Request Body:
json
{
    "phone_number": "string",
    "password": "string"
}
Success Response:
Status: 200 OK
json
{
    "token": "string"
}
Error Response:
Status: 401 Unauthorized
json
{
    "error": "Invalid credentials"
}


User Endpoints
Register User
Endpoint: /register
Method: POST
Description: Registers a new user and returns a token.
Request Body:
json

{
    "phone_number": "07099446637",
    "password": "mercy123"
}
Success Response:
Status: 201 Created
json
{
    "message": "User successfully created",
    "phone_number": "07099446637",
    "token": "4|5q9lLj6z72UaT6FShGF2b2uVDSvSa2A6Idm13FcZ49ef5f50"
}
Error Response:
Status: 400 Bad Request
json

{
    "error": "Validation errors"
}

//
Create Wallet
Endpoint: /wallets/create
Method: POST
Description: Creates a new wallet for the authenticated user.
Request Body:
json

{
    "user_id": "4",
    "currency": "USD"
}
Success Response:
Status: 201 Created
json
{
    "message": "Wallet successfully created",
    "wallet": {
        "user_id": "4",
        "currency": "USD",
        "balance": 0,
        "updated_at": "2024-09-15T01:58:31.000000Z",
        "created_at": "2024-09-15T01:58:31.000000Z",
        "id": 4
    }
}
Error Response:
Status: 400 Bad Request
json
{
    "error": "Validation errors"
}

//
Credit Wallet (Paystack Integration)
Endpoint: /wallets/credit
Method: POST
Description: Credits a specified amount to a wallet using Paystack for payment processing.

Request Body:
format: json

{
  "wallet_id": 5,
  "amount": 200,
  "email": "youremail@gmail.com"
}
wallet_id: The ID of the wallet to be credited.
amount: The amount to be credited.
email: The email address associated with the Paystack transaction.
Success Response (Payment Initiation):
Status: 200 OK

json

{
    "message": "Payment initiated, redirect user to Paystack",
    "authorization_url": "https://checkout.paystack.com/7ywpcodbskesvgp"
}

message: Indicates that payment has been initiated.
authorization_url: The URL where the user should be redirected to complete the payment on Paystack.
Success Response (After Paystack Payment Verification):
Status: 200 OK

json
{
    "message": "Wallet successfully credited",
    "wallet": {
        "id": 5,
        "user_id": 3,
        "currency": "EUR",
        "balance": 200,
        "created_at": "2024-09-15T19:08:21.000000Z",
        "updated_at": "2024-09-16T03:22:37.000000Z"
    }
}
message: Indicates the wallet has been successfully credited.
wallet.id: The ID of the credited wallet.
wallet.user_id: The ID of the user associated with the wallet.
wallet.currency: The currency of the wallet, e.g., EUR.
wallet.balance: The updated balance of the wallet after the credit.
created_at: The timestamp when the wallet was created.
updated_at: The timestamp when the wallet was last updated.

Error Response:
Status: 400 Bad Request

json

{
    "error": "Validation errors"
}
error: Describes any validation errors that occurred during the request.


//
Transfer Funds
Endpoint: /transactions/transfer
Method: POST
Description: Transfers funds from one wallet to another.

Request Body:
format: json

{
    "from_wallet_id": 1,
    "to_wallet_id": 2,
    "amount": 2000000
}
Success Response:
Status: 201 Created

json
{
    "message": "Transaction submitted for approval",
    "from_wallet_id": 1,
    "to_wallet_id": 2,
    "amount": 2000000
}
Error Response:
Status: 400 Bad Request
json

{
    "error": "Validation errors"
}
Status: 403 Forbidden
json

{
    "error": "Transaction requires admin approval"
}

Approve Transaction
Endpoint: /api/admin/transactions/{transaction_id}/approve
Method: POST
Description: Approves a pending transaction submitted for approval.

Request Headers:

Authorization: Bearer token (token used for the transfer)
Content-Type: application/json
Request Body:
No request body is required for this endpoint.

Success Responses:

Status: 200 OK
Response Body:

format:json

{
    "message": "Transaction approved successfully"
}
Status: 400 Bad Request
Response Body:

format:json

{
    "error": "Invalid request data"
}
Status: 403 Forbidden
Response Body:

format:json

{
    "error": "Unauthorized access"
}
Status: 409 Conflict
Response Body:

format: json

{
    "message": "Transaction already approved"
}
Description of Responses:

200 OK: The transaction has been approved successfully.
400 Bad Request: The request data is invalid.
403 Forbidden: Unauthorized access to approve the transaction.
409 Conflict: The transaction has already been approved previously


//
Admin Endpoints
Register Admin
Endpoint: /admin/register
Method: POST
Description: Registers a new admin and returns an authentication token.
Request Body:
json
Copy code
{
    "phone_number": "90834567890",
    "password": "passw123"
}
Success Response:
Status: 201 Created
json
Copy code
{
    "message": "Admin successfully created",
    "phone_number": "90834567890",
    "token": "6|fmVndgT6hvwrnvr70aXz8gg5PPME7zr2i14FeZF3df1143cb"
}
Error Response:
Status: 400 Bad Request
json
Copy code
{
    "error": "Validation errors"
}

//
Monthly Summary
Endpoint: /admin/monthly-summary
Method: GET
Description: Retrieves the monthly payment summary for admin.
Success Response:
Status: 200 OK

json
{
    "total_amount": 2000050,
    "transactions": [
        {
            "id": 1,
            "from_wallet_id": 1,
            "to_wallet_id": 2,
            "amount": "50.00",
            "approved": 1,
            "created_at": "2024-09-15T01:39:52.000000Z",
            "updated_at": "2024-09-15T01:39:52.000000Z"
        },
        {
            "id": 2,
            "from_wallet_id": 1,
            "to_wallet_id": 2,
            "amount": "2000000.00",
            "approved": 0,
            "created_at": "2024-09-15T01:41:35.000000Z",
            "updated_at": "2024-09-15T01:41:35.000000Z"
        }
    ]
}
Error Response:
Status: 401 Unauthorized
json
Copy code
{
    "error": "Unauthorized access"
}
