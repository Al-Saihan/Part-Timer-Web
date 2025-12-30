# Part-Timer-Web

A Laravel + Livewire application that connects part-time job seekers with recruiters. This repository contains a work-in-progress job marketplace with role-based users, job postings, applications, basic chat, and rating features. The project is intended for learning, prototyping, and small demos.

---

## Course Information

- **Course Name:** CSE391  
- **Name:** Al-Saihan Tajvi  
- **Student ID:** 23301219  
- **Email:** al.saihan.tajvi@gmail.com  

---

## Table of contents

- [Features](#features)
- [Tech stack](#tech-stack)
- [Requirements](#requirements)
- [Screenshots](#screenshots)
- [Testing](#testing)
- [Known issues](#known-issues)
- [Online resources used](#online-resources-used)
- [Future enhancements](#future-enhancements)
- [Contributing](#contributing)
- [License](#license)

---

## Features

- Role-based users: `job seeker` and `recruiter`
- User profiles and account management
- Recruiter job posting (descriptions, requirements, working hours)
- Requirements-based validation for part-time job suitability
- Quick-apply flow for job seekers
- User ratings and basic reputation tracking
- In-app chat system (non real-time)
- Livewire-driven reactive UI with minimal frontend JavaScript

---

## Tech stack

- PHP 8.2  
- Laravel Framework (v12)  
- Livewire (Flux & Volt)  
- Laravel Fortify (authentication)  
- Laravel Sanctum (session/API protection)  
- Tailwind CSS (via Vite)  
- Vite for asset bundling  
- Composer (PHP dependencies) and npm (JS tooling)

---

## Requirements

- PHP 8.2+
- Composer
- Node.js and npm
- A database (MySQL / PostgreSQL)
- Optional: Redis for sessions and queues

---

## Screenshots

Below are screenshots demonstrating the app UI and key flows. Images are stored in the `documentaiton/` folder.

- Landing page: ![Landing page](documentaiton/LandingPage.png)
- Sign-in page: ![Sign-in page](documentaiton/signinPage.png)
- Job detail: ![Job detail](documentaiton/jobDetail.png)
- Posted jobs (recruiter): ![Posted jobs](documentaiton/postedJobs.png)
- Recruiter dashboard: ![Recruiter dashboard](documentaiton/recruiterDashboard.png)
- Profile section: ![Profile section](documentaiton/profileSection.png)
- Seeker extra profile: ![Seeker extra profile](documentaiton/seekerExtraProfileSection.png)
- Ratings view: ![Ratings](documentaiton/ratings.png)
---

## Testing

Run the test suite using:

```bash
php artisan test
```