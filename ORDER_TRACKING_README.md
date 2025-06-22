# Order Tracking System - HunarWali

## Overview
The Order Tracking System provides comprehensive tracking and history management for orders in the HunarWali platform. It allows both freelancers and clients to track order progress in real-time with detailed timeline history.

## Features

### 🎯 Core Features
- **Real-time Order Tracking**: Track order status changes with detailed descriptions
- **Timeline History**: Visual timeline showing all order status updates
- **Role-based Access**: Different views for freelancers and clients
- **Status Management**: Freelancers can update order status with custom descriptions
- **Automatic Tracking**: System automatically logs status changes

### 📊 Tracking Information
- Order creation timestamp
- Status change history
- Who made each change
- Detailed descriptions for each status update
- Visual timeline with icons and timestamps

## Database Schema

### Order Tracking Table
```sql
CREATE TABLE order_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    updated_by INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

### Indexes
- `idx_order_tracking_order_id` - For fast order lookups
- `idx_order_tracking_updated_at` - For chronological sorting

## Files Structure

### Backend Files
- `create_order_tracking_table.sql` - Database table creation script
- `initialize_order_tracking.php` - Initialization script for existing orders
- `login/update-order-tracking.php` - AJAX handler for status updates
- `login/process-order-update.php` - Enhanced order status processor

### Frontend Files
- `login/order-tracking.php` - Freelancer order tracking page
- `client-panel/order-tracking.php` - Client order tracking page

### Integration Files
- `login/dashboard.php` - Updated to include tracking page
- `login/orders.php` - Added tracking buttons
- `login/order-details.php` - Added tracking navigation
- `client-panel/ordered-services.php` - Added tracking buttons

## Installation & Setup

### 1. Database Setup
The system is designed for **zero-setup**. The required `order_tracking` table and its performance indexes will be **created automatically** the first time you visit the "Orders" page as a freelancer or the "Ordered Services" page as a client.

There are no manual database scripts to run.

### 2. File Permissions
Ensure the following directories are writable:
- `uploads/` (for any future file attachments)

### 3. Configuration
No additional configuration required. The system uses existing database connections.

## Usage

### For Freelancers
1. **Access Tracking**: Go to Dashboard → Orders → Click "Track" button
2. **Update Status**: Use the status update form on the tracking page
3. **View History**: See complete timeline of order changes
4. **Add Descriptions**: Provide detailed descriptions for status changes

### For Clients
1. **Access Tracking**: Go to Client Panel → Ordered Services → Click "Track" button
2. **View Progress**: See real-time order status and timeline
3. **Contact Freelancer**: Direct link to messaging system
4. **Monitor Updates**: Get notified of status changes

## Order Status Flow

### Valid Status Transitions
```
Pending → In Progress → Completed
Pending → Completed (direct)
```

### Status Descriptions
- **Pending**: Order placed, waiting for freelancer to start
- **In Progress**: Work has begun on the order
- **Completed**: Order finished successfully
- **Cancelled**: Order cancelled (if applicable)

## API Endpoints

### Update Order Status
**POST** `/login/update-order-tracking.php`

Parameters:
- `order_id` (int) - Order ID
- `newStatus` (string) - New status
- `statusDescription` (string) - Description of the change

Response:
```json
{
    "success": true,
    "message": "Order status updated successfully"
}
```

### Process Order Update
**POST** `/login/process-order-update.php`

Parameters:
- `order_id` (int) - Order ID
- `status` (string) - New status

Response:
```json
{
    "success": true,
    "message": "Order status updated successfully"
}
```

## Security Features

### Authentication
- Session-based authentication required
- User authorization checks for order access
- Role-based permissions (freelancer vs client)

### Data Validation
- SQL injection prevention with prepared statements
- Input sanitization for all user inputs
- Status transition validation
- Transaction-based updates for data integrity

### Access Control
- Users can only track their own orders
- Freelancers can only update orders they're working on
- Clients can only view orders they've placed

## UI/UX Features

### Visual Timeline
- Clean, modern timeline design
- Color-coded status indicators
- Responsive design for mobile devices
- Hover effects and smooth transitions

### Status Indicators
- Color-coded badges for different statuses
- Icons for visual clarity
- Real-time status updates

### Navigation
- Breadcrumb navigation
- Back buttons to previous pages
- Direct links to related actions

## Error Handling

### Common Errors
- **Order not found**: Redirects to orders list
- **Unauthorized access**: Redirects to login
- **Invalid status transition**: Shows error message
- **Database errors**: Graceful error handling with user-friendly messages

### Error Messages
- Clear, actionable error messages
- Logging for debugging purposes
- User-friendly notifications

## Performance Considerations

### Database Optimization
- Indexed queries for fast lookups
- Efficient JOIN operations
- Pagination for large order lists

### Caching
- Session-based caching for user data
- Minimal database queries per page load

## Future Enhancements

### Planned Features
- **Email Notifications**: Automatic email alerts for status changes
- **Push Notifications**: Real-time browser notifications
- **File Attachments**: Ability to attach files to status updates
- **Comments System**: Allow comments on status updates
- **Export Functionality**: Export tracking history to PDF/CSV

### Technical Improvements
- **WebSocket Integration**: Real-time updates without page refresh
- **Mobile App API**: RESTful API for mobile applications
- **Advanced Analytics**: Order completion time analytics
- **Integration APIs**: Third-party service integrations

## Troubleshooting

### Common Issues

1. **Tracking data not showing**
   - Check if `order_tracking` table exists
   - Run `initialize_order_tracking.php`
   - Verify database permissions

2. **Status updates not working**
   - Check user authentication
   - Verify order ownership
   - Check database connection

3. **Timeline not displaying**
   - Check for JavaScript errors
   - Verify CSS is loading properly
   - Check browser console for errors

### Debug Mode
Enable debug mode by adding to PHP files:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Support

For technical support or questions about the Order Tracking System:
1. Check this README for common solutions
2. Review the error logs
3. Contact the development team

## Version History

### v1.0.0 (Current)
- Initial implementation
- Basic tracking functionality
- Timeline visualization
- Role-based access control
- Database integration 