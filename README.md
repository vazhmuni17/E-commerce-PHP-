# PHP E-commerce Platform

A comprehensive e-commerce platform built with PHP and MySQL, featuring full CRUD operations, user authentication, shopping cart, wishlist, and order management system.

## Features

### User Features
- **User Authentication**: Google OAuth integration for secure login
- **Product Catalog**: Browse products with search and sorting capabilities
- **Shopping Cart**: Add, update, and remove items from cart
- **Wishlist**: Save products for later
- **Checkout Flow**: Complete order process with address management
- **Order History**: View past orders and track status
- **Address Management**: Save multiple delivery addresses

### Admin Features
- **Admin Dashboard**: Overview of sales, products, and users
- **Product Management**: CRUD operations for products
- **Category Management**: Organize products into categories
- **Order Management**: View and update order statuses
- **User Management**: Monitor customer activity

## Project Structure

```
php-ecommerce/
├── config/                 # Configuration files
│   ├── config.php         # App configuration
│   └── database.php       # Database connection
├── database/              # Database schema
│   └── schema.sql         # SQL schema and seed data
├── public/                # Web accessible files
│   ├── index.php          # Homepage
│   ├── product.php        # Product detail page
│   ├── login.php          # User login
│   ├── cart.php           # Shopping cart
│   ├── wishlist.php       # Wishlist page
│   ├── dashboard.php      # User dashboard
│   ├── logout.php         # User logout
│   ├── api/               # API endpoints
│   │   ├── cart.php       # Cart API
│   │   └── wishlist.php   # Wishlist API
│   └── admin/             # Admin panel
│       ├── login.php      # Admin login
│       ├── dashboard.php  # Admin dashboard
│       ├── products.php   # Product management
│       ├── categories.php # Category management
│       ├── orders.php     # Order management
│       └── logout.php     # Admin logout
├── src/
│   ├── Models/            # Database models
│   │   ├── BaseModel.php  # Base model class
│   │   ├── User.php       # User model
│   │   ├── Admin.php      # Admin model
│   │   ├── Product.php    # Product model
│   │   ├── Category.php   # Category model
│   │   ├── CartItem.php   # Cart item model
│   │   ├── Wishlist.php   # Wishlist model
│   │   ├── Order.php      # Order model
│   │   ├── OrderItem.php  # Order item model
│   │   └── Address.php    # Address model
│   └── Utils/             # Utility classes
│       ├── Auth.php       # Authentication
│       └── Helper.php     # Helper functions
├── assets/                # Static assets
├── uploads/               # File uploads
└── README.md              # This file
```

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (for dependencies)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd php-ecommerce
   ```

2. **Set up the database**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

3. **Configure the application**
   - Edit `config/config.php` with your database credentials
   - Set up Google OAuth credentials in the config file
   - Configure your web server document root to `public/` directory

4. **Set up Google OAuth**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select existing one
   - Enable Google+ API
   - Create OAuth 2.0 credentials
   - Add redirect URI: `http://yourdomain.com/google-callback.php`
   - Update `config/config.php` with your client ID and secret

5. **Access the application**
   - Frontend: `http://yourdomain.com`
   - Admin Panel: `http://yourdomain.com/admin/login.php`
   - Default admin credentials: `admin@example.com` / `password`

## Database Schema

### Tables

- **users**: Customer information
- **admins**: Administrator accounts
- **categories**: Product categories
- **products**: Product catalog
- **addresses**: Customer addresses
- **cart_items**: Shopping cart items
- **wishlist**: User wishlist items
- **orders**: Customer orders
- **order_items**: Order line items

## Usage

### For Users
1. **Registration/Login**: Use Google OAuth to create an account or log in
2. **Browse Products**: Use search and filters to find products
3. **Add to Cart**: Add products to your shopping cart
4. **Checkout**: Complete the purchase process
5. **Track Orders**: View order history and status updates

### For Admins
1. **Admin Login**: Access the admin panel with credentials
2. **Manage Products**: Add, edit, or delete products
3. **Manage Categories**: Organize products into categories
4. **Process Orders**: View and update order statuses
5. **Monitor Activity**: View dashboard statistics

## Security Features

- Password hashing for admin accounts
- Google OAuth for secure user authentication
- Prepared statements to prevent SQL injection
- Input validation and sanitization
- Session management for user authentication

## Technologies Used

- **Backend**: PHP 7.4+, MySQL
- **Frontend**: Bootstrap 5, jQuery
- **Authentication**: Google OAuth 2.0
- **Database**: PDO for database interactions
- **Styling**: Bootstrap 5 with custom CSS

## API Endpoints

### Cart API (`api/cart.php`)
- `POST` - Add/update/remove cart items

### Wishlist API (`api/wishlist.php`)
- `POST` - Add/remove wishlist items

## Future Enhancements

- Payment gateway integration (Stripe/PayPal)
- Product reviews and ratings
- Email notifications
- Inventory management
- Sales analytics
- Multi-language support
- Mobile app API

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is open source and available under the MIT License.

## Support

For issues and questions, please create an issue in the repository or contact the development team.