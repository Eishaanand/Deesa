# Deesa UCAT AI Platform

Production-oriented Laravel architecture for an AI-powered UCAT preparation platform with:

- Student and admin roles
- Sequential timed UCAT mock exams
- Deep performance analytics
- Live exam monitoring
- Gemini-powered AI question generation

## Stack

- Laravel 11
- Livewire 3
- Tailwind CSS
- MySQL
- Gemini API via Guzzle

## Key Features

- Apple-inspired glass landing page
- Student dashboard for exam discovery, results, and trends
- Admin dashboard with live monitoring and student analytics
- Timed multi-section exam flow with autosave and section locking
- AI insights for weak areas and practice recommendations
- Question generation pipeline using Gemini

## Docker Setup

This repository is intended to be portable with Docker only. A user should be able to clone it on any machine with Docker installed and run the stack without installing PHP, Composer, Node, or MySQL on the host.

### Prerequisites

- Docker
- Docker Compose

### First run

1. Clone the repository.
2. Start the stack:
   - `docker compose up --build`
3. Open:
   - `http://localhost:8080`

### What happens automatically

- If `.env` does not exist, the app container copies `.env.example` to `.env`
- Composer dependencies are installed inside the container
- Frontend dependencies are installed and production assets are built inside Docker
- Laravel app key is generated automatically
- The app waits for MySQL, then runs migrations and seeders
- No host PHP, Composer, Node, or MySQL setup is required

### Optional configuration

- Edit `.env.example` before the first boot if you want different defaults committed to the repo
- Edit `.env` after the first boot if you need machine-specific overrides
- Set `GEMINI_API_KEY` in `.env` if you want AI question generation to work

### Services

- `app`: PHP 8.3 FPM container with Composer and Node available
- `web`: Nginx reverse proxy
- `db`: MySQL 8.4
- `node`: asset build container

### Notes

- The first boot can take a few minutes because dependencies are installed and assets are built inside containers.
- Application data is stored in the named Docker volume `mysql_data`.
- To rebuild from scratch, run `docker compose down -v` and then `docker compose up --build`.

## Default Seed Accounts

- Admin: `admin@deesa.test` / `password`
- Student: `student@deesa.test` / `password`

## Architecture

- `app/Http/Controllers`: page and flow orchestration
- `app/Livewire`: interactive dashboards and exam runner
- `app/Models`: exam, analytics, and audit entities
- `app/Services`: AI integration, exam orchestration, analytics
- `database/migrations`: schema
- `database/seeders`: demo data

## Important Notes

- Authentication scaffolding is designed to sit on top of Laravel Breeze or Jetstream.
- Live monitoring assumes periodic Livewire polling.
- Analytics are computed server-side via dedicated service classes.
- The Gemini integration expects strict JSON output and validates the shape before persistence.
