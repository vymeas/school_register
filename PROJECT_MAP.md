# Primary School Registration & Payment System

Framework: Laravel

## 1. System Overview

This system manages:

-   Student Registration
-   Classroom Management
-   Teacher Assignment
-   Academic Terms
-   Study Time Packages
-   Student Payments
-   User Management (Admin / Staff)
-   Reports

Main focus: Payment tracking for student study periods.

------------------------------------------------------------------------

# 2. User Roles

  Role          Permission
  ------------- -------------------------
  Super Admin   Full system control
  Admin         Manage school data
  Accountant    Manage payments
  Registrar     Register students


------------------------------------------------------------------------
# Teacher 
  - recommend info please
------------------------------------------------------------------------
# 3. Core Database Models

## 3.1 Users

Fields

-   id (uuid)
-   username
-   password
-   full_name
-   role
-   status (active / inactive)
-   is_deleted
-   created_at
-   updated_at
-   last_login_at
-   phone
-   email

------------------------------------------------------------------------

## 3.2 Terms

Fields

-   id
-   name (2025-2026)
-   start_date
-   end_date
-   status
-   created_at

------------------------------------------------------------------------

## 3.3 Grades

Fields

-   id
-   name
-   description
-   created_at

Example:

-   Grade 1
-   Grade 2
-   Grade 3
-   Grade 4
-   Grade 5
-   Grade 6

------------------------------------------------------------------------

## 3.4 Classrooms

Fields

-   id
-   grade_id
-   name
-   capacity
-   created_at

Example:

-   Grade 1A
-   Grade 1B
-   Grade 2A

------------------------------------------------------------------------

## 3.5 Teachers

Fields

-   id
-   name
-   phone
-   email
-   classroom_id
-   status
-   address
-   hire_date
-   created_at

------------------------------------------------------------------------

## 3.6 Students

Fields

-   id
-   student_code
-   first_name
-   last_name
-   gender
-   date_of_birth
-   parent_name
-   parent_phone
-   address
-   classroom_id
-   term_id
-   status
-   registration_date
-   photo
-   created_at

------------------------------------------------------------------------

## 3.7 Tuition Plans

Example

  name       duration   price
  ---------- ---------- -------
  1 Month    1          200
  3 Months   3          550
  6 Months   6          1000
  1 Year     12         1800

Fields

-   id
-   name
-   duration_month
-   price
-   status
-   created_at

------------------------------------------------------------------------

## 3.8 Enrollments

Fields

-   id
-   student_id
-   classroom_id
-   term_id
-   enrollment_date
-   status

------------------------------------------------------------------------

## 3.9 Payments

Fields

-   id
-   student_id
-   tuition_plan_id
-   amount
-   payment_date
-   start_study_date
-   end_study_date
-   payment_method
-   reference_number
-   note
-   created_by
-   created_at

Payment methods example:

-   cash
-   aba
-   acleda
-   wing

------------------------------------------------------------------------

## 3.10 Payment Logs

Fields

-   id
-   payment_id
-   action
-   user_id
-   created_at

------------------------------------------------------------------------

# 4. Important System Features

## Student Registration Flow

Create Student\
↓\
Assign Classroom\
↓\
Choose Tuition Plan\
↓\
Make First Payment\
↓\
System generates study period

------------------------------------------------------------------------

## Payment Logic Example

If student pays **3 months**:

start_date = 01 Jan\
duration = 3 months

end_date = 01 Apr

The system calculates the end date automatically.

------------------------------------------------------------------------

## Student Status

  Status    Condition
  --------- -----------------------
  Active    Payment valid
  Expired   Study period finished
  Pending   Not yet paid

------------------------------------------------------------------------

# 5. API Example Structure

/api/auth/login

/api/students\
/api/students/create\
/api/students/{id}

/api/classrooms\
/api/grades

/api/terms

/api/payments\
/api/payments/create

/api/reports/payments\
/api/reports/students

------------------------------------------------------------------------

# 6. Admin Dashboard Pages

-   Dashboard
-   Students
-   Teachers
-   Classrooms
-   Grades
-   Terms
-   Tuition Plans
-   Payments
-   Reports
-   Users
-   Settings

------------------------------------------------------------------------

# 7. Recommended Features

## Student Code Generator

Example

STU-0001\
STU-0002

------------------------------------------------------------------------

## Payment Receipt

Generate a PDF receipt after payment.

------------------------------------------------------------------------

## Expiry Reminder

Notify students who will expire within 7 days.

------------------------------------------------------------------------

# 8. Recommended Laravel Folder Structure

app ├── Models ├── Http │ ├── Controllers │ ├── Requests │ ├── Services
│ database ├── migrations ├── seeders

routes ├── api.php ├── web.php

------------------------------------------------------------------------

# 9. Future Upgrade Ideas

-   Parent Portal
-   Student ID Card
-   Attendance System
-   SMS Payment Reminder
-   Online Payment Integration
