A full-stack web application developed as my professional qualification project at the University of Latvia. It allows users to manage personal collections, participate in community discussions, exchange collection items with other users, and interact through social features. The project was developed using the Laravel framework following the MVC architecture. 
--- 
## Technologies 
- PHP
- Laravel
- Blade
- MySQL
- HTML5
- CSS3
- JavaScript
- Git
---
## Main Features 
### User Management 
- User registration and authentication
- User profile management
- Authorization system
- Administrator permissions
### Collection Management 
- Create and manage personal collections
- Edit and delete collection items
- Rate collections
- Browse other users' collections
### Exchange System 
- Create exchange offers
- Manage exchange requests
- Browse available exchanges
### Forum 
- Create discussion posts
- Edit and delete posts
- Comment on discussions
- Search forum posts
### Social Features 
- Add and manage friends
- View user profiles
- Exchange collections
### Media 
- Upload media files
- Manage images associated with collections
---
## Project Structure 
The project follows the Laravel MVC architecture. 
- Controllers
– business logic
- Models
– database interaction
- Blade Views
– user interface
- Routes
– application routing
- Middleware
– authentication and authorization
---
## Installation
```bash
git clone https://github.com/Soru2403/Laravel-collection-platform.git 
cd Laravel-collection-platform 
composer install 
cp .env.example .env 
php artisan key:generate 
php artisan migrate 
php artisan serve
```
--- 
## Purpose 
The purpose of this project was to apply the knowledge gained during university studies by designing and implementing a complete Laravel-based web application with database integration, user authentication and multiple interconnected modules.
