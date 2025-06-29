# Database Structure and Troubleshooting Guide

## Overview

This document provides information about the database structure used in the freelance website project, with a focus on the orders system and common troubleshooting steps.

## Database Tables

### Orders Table

The `orders` table stores information about orders placed between clients and freelancers.

```sql
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  freelancer_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  description TEXT NOT NULL,
  delivery_time INT NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Fields:

- `id`: Unique identifier for the order (auto-incremented)
- `client_id`: ID of the client who placed the order
- `freelancer_id`: ID of the freelancer who received the order
- `amount`: The price of the order
- `description`: Detailed description of the order
- `delivery_time`: Expected delivery time in days
- `status`: Current status of the order (pending, in_progress, completed, cancelled)
- `created_at`: Timestamp when the order was created
- `updated_at`: Timestamp when the order was last updated

## Common Issues and Troubleshooting

### "Prepare failed" Error

If you encounter a "Prepare failed" error when trying to accept custom orders or offers, it may be due to one of the following issues:

1. **Missing Orders Table**: The orders table may not exist in the database. Run the `create_orders_table.php` script to create it.

2. **Database Connection Issues**: Ensure that the database connection parameters in `config/database.php` are correct.

3. **SQL Syntax Errors**: Check for any syntax errors in the SQL statements.

4. **Data Type Mismatches**: Ensure that the data types being passed to the prepared statements match the expected types.

### Testing Database Functionality

The following scripts can be used to test and verify database functionality:

- `test_orders_table.php`: Checks if the orders table exists and displays its structure.
- `test_order_insert.php`: Tests inserting a sample order into the orders table.
- `test_db_connection.php`: Tests the database connection.

## Maintenance Scripts

- `create_orders_table.php`: Creates the orders table if it doesn't exist.

## Best Practices

1. **Always Use Prepared Statements**: To prevent SQL injection attacks, always use prepared statements when executing SQL queries with user input.

2. **Validate Input Data**: Always validate and sanitize input data before using it in database operations.

3. **Use Transactions**: When performing multiple related database operations, use transactions to ensure data integrity.

4. **Error Handling**: Implement proper error handling to catch and log database errors.

5. **Regular Backups**: Regularly backup the database to prevent data loss.

## Troubleshooting Steps

If you encounter database-related issues, follow these steps:

1. Check if the required tables exist using the test scripts.
2. Verify that the database connection parameters are correct.
3. Check for any error messages in the PHP error logs.
4. Ensure that the MySQL server is running.
5. Verify that the user has the necessary permissions to perform the required operations.

## Contact

If you need further assistance, please contact the system administrator.