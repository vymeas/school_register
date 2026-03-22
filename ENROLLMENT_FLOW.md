# Enrollment Feature Flow (Primary School System)

## Purpose

Define how student enrollment works with full history tracking and
upgrade flow.

------------------------------------------------------------------------

## Core Principles

-   One student can have multiple enrollments
-   Only ONE enrollment is current at a time
-   NEVER update old enrollment → always create new
-   Enrollment connects student → term → grade → classroom

------------------------------------------------------------------------

## Data Structure

### enrollments

-   id
-   student_id
-   term_id
-   grade_id
-   classroom_id
-   status (pending, active, completed, transferred)
-   start_date
-   end_date
-   is_current (true/false)
-   created_at

------------------------------------------------------------------------

## Enrollment Lifecycle

### 1. New Student Registration

-   Create student
-   Create enrollment:
    -   status = pending
    -   is_current = true

------------------------------------------------------------------------

### 2. First Payment

-   Link payment to enrollment
-   Update enrollment:
    -   status = active

------------------------------------------------------------------------

### 3. Study Period Active

-   Student attends class
-   Payment determines active/expired

------------------------------------------------------------------------

### 4. Expired Payment

-   Enrollment remains
-   System marks student as expired (based on payment)

------------------------------------------------------------------------

### 5. Upgrade Grade (IMPORTANT FLOW)

Step 1: Close current enrollment

-   status = completed
-   is_current = false
-   end_date = today

Step 2: Create new enrollment

-   new grade_id
-   new classroom_id
-   status = pending
-   is_current = true
-   start_date = today

------------------------------------------------------------------------

### 6. Change Classroom

Step 1: - old enrollment → status = transferred - is_current = false

Step 2: - create new enrollment with new classroom

------------------------------------------------------------------------

## Enrollment Status Meaning

  Status        Description
  ------------- ------------------------
  pending       Not paid yet
  active        Paid and studying
  completed     Finished term
  transferred   Moved to another class

------------------------------------------------------------------------

## Relationship with Payment

payments.enrollment_id → enrollments.id

Each enrollment has its own payment records.

------------------------------------------------------------------------

## Queries

### Get Current Enrollment

SELECT \* FROM enrollments WHERE student_id = ? AND is_current = true

------------------------------------------------------------------------

### Get Enrollment History

SELECT \* FROM enrollments WHERE student_id = ? ORDER BY created_at DESC

------------------------------------------------------------------------

### Get Students in Classroom

SELECT \* FROM enrollments WHERE classroom_id = ? AND is_current = true
AND status = 'active'

------------------------------------------------------------------------

## Feature Flow Summary

Student ↓ Enrollment (create) ↓ Payment ↓ Enrollment active ↓ Upgrade /
transfer ↓ New enrollment created

------------------------------------------------------------------------

## Validation Rules

-   Only one current enrollment per student
-   Cannot create payment without enrollment
-   Cannot upgrade without closing old enrollment
-   Classroom must belong to grade

------------------------------------------------------------------------

## Suggested Services (Laravel)

Create:

app/Services/EnrollmentService.php

Functions:

-   createEnrollment()
-   closeEnrollment()
-   upgradeGrade()
-   transferClassroom()
-   getCurrentEnrollment()

------------------------------------------------------------------------

## Controller

EnrollmentController:

-   store()
-   upgrade()
-   transfer()
-   history()
-   current()

------------------------------------------------------------------------

## Future Enhancements

-   Enrollment approval flow
-   Auto grade promotion
-   Bulk enrollment import
-   Parent notification
