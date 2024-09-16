## Overview

The Wallet System API allows users to A user to create an account and authenticate with a unique phone number and password
A user can create many wallets, each with a unique currency
User can credit their wallets using payStack
A user can transfer from one wallet to another
Wallet transfers over N1,000,000 must be approved by an ADMIN user
An admin gets monthly payment summaries - capturing all payments made in the system


## Prerequisites

- PHP 8.0 or higher - but the version used is PHP 8.0.30
- Composer
- A database (MySQL.)

## Setup Guide

### Clone the Repository

```bash
git clone <repository-url>
cd <project-directory>
Install Dependencies
Install PHP dependencies using Composer:
bash
Copy code
composer install
Set Up Environment Variables
Copy .env:
bash
Copy code
cp .env
Edit the .env file to configure your database and other environment variables. Example configuration for MySQL:

makefile
Copy code
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wallet_system
DB_USERNAME=root
DB_PASSWORD=


PAYSTACK_PUBLIC_KEY=yourkey
PAYSTACK_SECRET_KEY=yourkey
PAYSTACK_PAYMENT_URL=https://api.paystack.co
MERCHANT_EMAIL=your_email@example.com


Generate Application Key
Generate a new application key:
bash
Copy code
php artisan key:generate
Run Migrations
Run database migrations to set up your database schema:
bash
Copy code
php artisan migrate
Start the Development Server
Start the Laravel development server:
bash
Copy code
php artisan serve
