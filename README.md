# Leaderboard Project

Welcome to the Leaderboard project! This application is designed to track user activities and calculate their ranks based on points earned over different periods (daily, monthly, yearly).

## Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Requirements](#requirements)
- [Installation](#installation)
- [Running the Application](#running-the-application)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)
- [License](#license)

## Features

- User registration and login.
- Track user activities with points.
- Calculate and display ranks based on user activities.
- Filter leaderboard by daily, monthly, and yearly performance.
- Search for users by User ID.

## Technologies Used

- **Backend**: Laravel
- **Frontend**: Blade
- **Database**: MySQL
- **Others**: PHP, Composer, Node.js, npm

## Requirements

Before you begin, ensure you have met the following requirements:

- [PHP](https://www.php.net/) (version 8.0 or higher)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) (version 14 or higher)
- [MySQL](https://www.mysql.com/) (version 5.7 or higher)

## Installation

Follow these steps to set up the project locally:

1. **Clone the Repository**

   ```bash
   git clone https://github.com/proficient-pravin/leaderboard.git
   cd leaderboard

1. **Setup**

   ```bash
    composer install
   
    cp .env.example .env
   
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password

    php artisan key:generate

    php artisan migrate

    php artisan serve

    php artisan db:seed --class=UsersSeeder

    php artisan db:seed --class=ActivitiesSeeder
   
## Once you run Must click on the Re-calculate button OR

 ```bash
    // uncomment this line in ActivitiesSeeder
   app(LeaderboardController::class)->calculateAndStorePeriodicRanks();


