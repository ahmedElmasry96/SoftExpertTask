SoftExpert Task

Requirements
PHP >= 8.2
Laravel 12
Composer
Database (MySQL)

=======================================================================================

Installation
Follow these steps to get the project up and running on your local machine:

1. Clone the Repository
Clone this repository to your local machine using Git.

git clone https://github.com/yourusername/yourprojectname.git
cd yourprojectname

=======================================================================================

2. Install Dependencies
Run the following command to install the project’s dependencies using Composer:
composer install

=======================================================================================

4. Setup Environment Configuration
Copy the .env.example file to create a new .env file. This file contains the environment settings for your application.
cp .env.example .env

======================================================================================

5. Generate Application Key
Generate a new application key, which is required to secure user sessions and other encrypted data:
php artisan key:generate

=====================================================================================

5. Run Migrations & Seed Database
Run the migrations to set up your database schema and seed it with initial data.
php artisan migrate --seed

====================================================================================

7. Start Development Server
Now, you can start the Laravel development server:
php artisan serve
By default, the application will be accessible at http://127.0.0.1:8000.

===================================================================================

Additional Notes
Make sure your database is correctly configured in the .env file.
If you're using MySQL, make sure the correct database settings are set for DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and DB_PASSWORD.

