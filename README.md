Technologies Used :
- PHP 8.1.10
- MySQL 8.0.30
- Bootstrap 5.3 (already Included via CDN)

Setup Instructions
1. Clone the Repository
2. Import the Database
Import the provided SQL file (database.sql) into your MySQL server to set up the BREVO database with all the necessary data and structures.
3. Configure the Database Connection
Update the db_connection.php file with your MySQL credentials
4. Run the Application
5. Run scripts 
To recalculate scores for all clients, navigate to: http://localhost/your-repository/scripts/calculate_scores.php
To categorize clients into groups, navigate to: http://localhost/your-repository/scripts/categorize_groups.php
6. Testing
Unit tests are available to validate the score calculation logic. To run the tests, use PHPUnit: ./vendor/bin/phpunit tests/ClientTest.php

Features : 
- Client List: View and search clients by ID, group, and sort order.
- Scoring Calculation: Automatically calculates scores based on client data, including bounce rates, open rates, unsubscription rates, and complaint rates.
- Client Categorization: Categorizes clients into groups based on their performance metrics.
- Pagination: Supports pagination for handling large sets of client data.

