# Client Project Tracker

A full-stack Client Project Tracker application built for a digital agency.

## Features

- Create, view, update, and delete projects
- Search projects
- Filter by status and priority
- Sort projects
- Form validation
- Confirmation dialog before closing a form with unsaved changes
- Delete confirmation dialog with a loading state
- Success notifications after create, update, and delete
- Animated modals, table rows, and button/input feedback
- Reduced-motion support for accessibility
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

Create the `dbtracker` database before running the migrations.

```bash
cd client-project-tracker-backend
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

The API is then available at `http://127.0.0.1:8000/api`.

## Frontend Setup

The frontend reads the API URL from `.env` (`NUXT_PUBLIC_API_BASE`), so start the
backend first.

```bash
cd client-project-tracker-frontend
npm install
npm run dev
```

The application is then available at `http://localhost:3000`.

## Available Commands

Frontend:

```bash
npm run dev         # start the development server
npm run build       # production build
npm run typecheck   # static type checking
```

Backend:

```bash
php artisan test    # run the API tests
```

## Project Structure

```
client-project-tracker-backend/
  app/Http/Controllers/ProjectController.php
  app/Models/Project.php
  routes/api.php
  tests/Feature/ProjectTest.php

client-project-tracker-frontend/
  app/
    app.vue                    # page state, filters, and CRUD orchestration
    assets/css/main.css        # global styles and transitions
    components/
      ProjectFilters.vue       # search, filter, and sort controls
      ProjectTable.vue         # project table
      ToastNotification.vue    # success notifications
      modal/                   # form, delete, and unsaved-changes dialogs
    composables/               # useToasts, useModalDismiss
    constants/project.ts       # filter options, badge classes, sort comparators
    types/                     # Project, ProjectFormData, Toast
```

## Assumptions Made

- Authentication was not implemented because it was listed as an optional feature.
- MySQL/MariaDB was used as the database.
- The frontend and backend are maintained as separate applications using Nuxt and Laravel.
- Project status and priority values are limited to the options specified in the assessment.
- The due date must be the same as or later than the start date.
- The frontend calls the Laravel API directly over HTTP using the URL in `.env`.