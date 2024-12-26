# Test 2 - Laravel

Laravel: v11.36.1
Node: v20.9.0
PHP: v8.2.12

# Project setup
1. Clone the test-2-Laravel project.
2. Go to the development branch (git checkout development).
3. Create the environment file -> cp .env.example .env
4. Configure your .env (set your database, and a mail delivery system: mailhog, mailtrap etc...)
5. composer install
6. npm install
7. php artisan migrate
8. php artisan db:seed
9. Run php artisan serve
10. Run npm run dev
11. Run php artisan queue:listen

# Happy flow
1. Open your localhost
2. Login with the following credentials:
   Mail: test@example.com
   Password: password
3. Go to Data Import page
4. Select Import products and upload the products.xlsx file from the resources/imports folder
5. Select Import orders and upload the orders.xlsx file from the resources/imports folder
6. Select Import customers and transactions and upload the custoemrs.xlsx and transactions.xlsx files from the resources/imports folder

# In addition to Happy flow, all functions must work as described in the task.
