# Client Project Tracker

A full-stack Client Project Tracker application built for a digital agency.

## Features

- Create, view, update, and delete projects
- Search projects
- Filter by status and priority
- Sort projects
- Form validation
- Responsive interface
- Automated API tests

## Technologies

- Laravel 12
- PHP
- MySQL
- Nuxt 4
- Vue.js
- TypeScript

## Requirements

- PHP 8.2+
- Composer
- Node.js
- npm
- MySQL/MariaDB

## Backend Setup

```bash
Create the dbtracker database before running php artisan migrate.
cd client-project-tracker-backend
composer install
php artisan key:generate
php artisan migrate
php artisan serve

## Assumptions Made

- Authentication was not implemented because it was listed as an optional feature.
- MySQL/MariaDB was used as the database.
- The frontend and backend are maintained as separate applications using Nuxt and Laravel.
- Project status and priority values are limited to the options specified in the assessment.
- The due date must be the same as or later than the start date.