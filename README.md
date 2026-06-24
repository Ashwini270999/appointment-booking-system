# Appointment Booking System

## Assignment Completion Summary

### Implemented Features

* Doctor Availability Scheduling
* Available Slot Retrieval
* Appointment Booking
* Booking Reference Number Generation
* Double Booking Prevention
* Concurrent Request Handling
* Appointment Cancellation
* Cancellation Reason Storage
* Appointment Rescheduling
* Notification Management
* Event & Listener Architecture
* Queue Based Notification Processing
* Repository Pattern
* Form Request Validation
* MySQL Database Design
* Postman Collection
* Database Export

### Optional Enhancements Implemented

* Event / Listener
* Queue Processing
* Repository Pattern

---

# Overview

This project is a Laravel-based Appointment Booking System that allows patients to book appointments with doctors based on their availability schedules.

The system provides APIs for:

* Doctor availability management
* Viewing available slots
* Booking appointments
* Preventing double bookings
* Appointment cancellation
* Appointment rescheduling
* Notification handling using Events, Listeners, Queues, and Jobs

The application is designed to handle concurrent booking requests and follows clean coding practices using Laravel Form Requests, Repository Pattern, Database Transactions, Events, Listeners, and Queue Processing.

---

# Technology Stack

* PHP 8.2
* Laravel 12 (Laravel 10+ Requirement)
* MySQL
* Laravel Events & Listeners
* Laravel Queues

---

# Features

## 1. Doctor Availability Schedule

Doctors can define their availability by specifying:

* Date
* Start Time
* End Time
* Slot Duration

### Example

Availability:

* Date: 2026-06-30
* Start Time: 09:00
* End Time: 12:00
* Slot Duration: 30 Minutes

Generated Slots:

* 09:00
* 09:30
* 10:00
* 10:30
* 11:00
* 11:30

---

## 2. View Available Slots

Patients can view available appointment slots for a doctor on a selected date.

Already booked slots are automatically excluded from the response.

---

## 3. Book Appointment

Patients can:

* Select a doctor
* Select a date
* Select an available slot

After successful booking:

* Appointment is created
* Unique booking reference number is generated
* Notification record is created
* Notification job is queued

### Example Reference Number

APT-1782230062

---

## 4. Prevent Double Booking

The system prevents multiple patients from booking the same slot.

Implemented using:

* Database Transactions
* lockForUpdate()
* Slot Validation
* Existing Appointment Checks

This ensures concurrent requests cannot create duplicate appointments.

---

## 5. Cancel Appointment

Patients can cancel appointments.

Features:

* Stores cancellation reason
* Updates appointment status
* Makes slot available again

---

## 6. Reschedule Appointment

Patients can reschedule an appointment.

Validation includes:

* Doctor availability on selected date
* Slot validity
* Slot availability
* Duplicate booking prevention

When rescheduled:

* Old slot becomes available
* New slot is assigned

---

## 7. Notification System

After successful booking:

### Event

AppointmentBooked

### Listener

CreateAppointmentNotification

### Queue Job

SendAppointmentNotificationJob

### Flow

Appointment Booked

↓

Event Fired

↓

Notification Record Created

↓

Queue Job Dispatched

↓

Notification Sent (Simulated)

---

# Design Decisions

## Repository Pattern

Implemented using:

AppointmentRepository

### Responsibilities

* Appointment creation
* Slot booking checks
* Reschedule booking checks

### Benefits

* Separation of concerns
* Cleaner controllers
* Easier maintenance
* Better testability

---

## Event Driven Architecture

Implemented using:

* AppointmentBooked Event
* CreateAppointmentNotification Listener

### Benefits

* Loose coupling
* Better scalability
* Cleaner code structure

---

## Queue Processing

Implemented using:

SendAppointmentNotificationJob

### Benefits

* Faster API responses
* Background processing
* Better scalability

---

# Project Structure

```text
app/
├── Events
├── Listeners
├── Jobs
├── Repositories
├── Models
├── Http
│   ├── Controllers
│   │   └── Api
│   └── Requests

database/
├── migrations
├── seeders
├── appointment_booking.sql

postman/
├── Booking System.postman_collection.json
```

---

# API Endpoints

## Create Doctor Availability

### POST

/api/v1/availabilities

### Request

```json
{
    "doctor_id": 1,
    "date": "2026-06-30",
    "start_time": "09:00",
    "end_time": "12:00",
    "slot_duration": 30
}
```

---

## Get Available Slots

### GET

/api/v1/doctors/{doctor}/slots?date=2026-06-30

---

## Book Appointment

### POST

/api/v1/appointments

### Request

```json
{
    "doctor_id": 1,
    "patient_id": 1,
    "appointment_date": "2026-06-30",
    "start_time": "09:00"
}
```

---

## Cancel Appointment

### POST

/api/v1/appointments/{appointment}/cancel

### Request

```json
{
    "reason": "Personal Emergency"
}
```

---

## Reschedule Appointment

### POST

/api/v1/appointments/{appointment}/reschedule

### Request

```json
{
    "appointment_date": "2026-07-01",
    "start_time": "10:30"
}
```

---

# Validation

Implemented using Laravel Form Requests.

Validation includes:

* Doctor existence validation
* Patient existence validation
* Valid date checks
* Future date checks
* Time format validation
* Slot duration validation
* Cancellation reason validation

---

# Error Handling

Handled scenarios:

* Doctor unavailable
* Invalid slot
* Duplicate booking
* Concurrent booking attempts
* Invalid reschedule request
* Already cancelled appointment

---

# Database Structure

Main Tables:

* doctors
* patients
* doctor_availabilities
* appointments
* appointment_cancellations
* notifications
* jobs
* failed_jobs

---

# Database Setup

## 1. Create Database

```sql
CREATE DATABASE appointment_booking;
```

## 2. Configure Environment

Update `.env`

```env
DB_DATABASE=appointment_booking
DB_USERNAME=root
DB_PASSWORD=
```

## 3. Install Dependencies

```bash
composer install
```

## 4. Generate Application Key

```bash
php artisan key:generate
```

## 5. Run Migrations

```bash
php artisan migrate
```

## 6. Run Seeders

```bash
php artisan db:seed
```

---

# Queue Setup

Update `.env`

```env
QUEUE_CONNECTION=database
```

Run Queue Worker:

```bash
php artisan queue:work
```

Notification jobs require the queue worker to be running.

---

# Quick Testing Flow

1. Run migrations and seeders
2. Start queue worker
3. Create doctor availability
4. View available slots
5. Book appointment
6. Verify notification record
7. Reschedule appointment
8. Cancel appointment
9. Verify slot becomes available again

---

# Database Dump

A database export is included:

```text
database/appointment_booking.sql
```

Import directly using phpMyAdmin if required.

---

# Postman Collection

A Postman collection is included for quick API testing.

Location:

```text
postman/Booking System.postman_collection.json
```

Import the collection into Postman and run the APIs directly.

---

# Performance Considerations

The application is designed considering:

* 200 Doctors
* 10,000 Bookings Per Day

Implemented performance measures:

* Database Transactions
* lockForUpdate()
* Indexed Appointment Lookups
* Queue Based Notifications
* Availability Validation Before Booking

---

# Scalability Approach

For larger traffic volumes:

* Redis Queue Driver
* Slot Caching
* Multiple Queue Workers
* Database Read Replicas
* Horizontal Application Scaling
* Load Balancer
* API Rate Limiting

---

# Additional Architecture Implemented

Beyond the core requirements, the following architectural enhancements were implemented:

* Repository Pattern
* Event Driven Architecture
* Event & Listener Implementation
* Queue Based Notification Processing
* Database Transactions
* Concurrent Booking Protection using lockForUpdate()
* Form Request Validation
* Database Export
* Postman Collection for API Testing

---

# Author

Ashwini K
Laravel Developer

Assessment Submission: Appointment Booking System
