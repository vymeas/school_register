# Payment Feature Specification (Primary School System)

## Purpose

Design a robust payment system that controls student access to
classrooms based on paid study periods.

------------------------------------------------------------------------

## Core Principles

-   Do NOT store "paid = true/false"
-   Always calculate payment status using date ranges
-   One student can have multiple payments
-   Payments must be linked to enrollment

------------------------------------------------------------------------

## Data Flow

Student ↓ Enrollment (term + classroom) ↓ Payment (multiple records) ↓
System calculates: - Active / Expired - Paid until date - Classroom
access

------------------------------------------------------------------------

## Database Design

### enrollments

-   id
-   student_id
-   classroom_id
-   term_id
-   status (pending, active, blocked)
-   joined_at

------------------------------------------------------------------------

### payments

-   id
-   student_id
-   enrollment_id
-   tuition_plan_id
-   amount
-   payment_date
-   start_date
-   end_date
-   status
-   created_by

------------------------------------------------------------------------

## Payment Logic

### First Payment

start_date = today\
end_date = today + duration

------------------------------------------------------------------------

### Continue Payment

start_date = last_payment.end_date\
end_date = start_date + duration

------------------------------------------------------------------------

## Student Status Logic

if today \<= latest_payment.end_date\
→ ACTIVE

if today \> latest_payment.end_date\
→ EXPIRED

------------------------------------------------------------------------

## Classroom Display

  Student   Status    Paid Until
  --------- --------- ------------
  John      Active    01 Mar
  Dara      Expired   01 Jan

------------------------------------------------------------------------

## Feature Flow

1.  Register Student\
2.  Create Enrollment (status = pending)\
3.  Make Payment\
4.  Update Enrollment → active\
5.  Allow student to join classroom

------------------------------------------------------------------------

## Validation Rules

-   Prevent overlapping payments
-   Must select tuition plan
-   Payment amount must match plan
-   Cannot create payment without enrollment

------------------------------------------------------------------------

## Service Layer (Laravel)

Create:

app/Services/PaymentService.php

Responsibilities:

-   calculateStartDate()
-   calculateEndDate()
-   getLastPayment()
-   updateEnrollmentStatus()

------------------------------------------------------------------------

## Controller

PaymentController:

-   store()
-   index()
-   showStudentPayments()

------------------------------------------------------------------------

## Extra Features

-   Expiry alert (7 days before)
-   Payment history timeline
-   Receipt (PDF)
-   Monthly tracking (optional)

------------------------------------------------------------------------

## Future Upgrade

-   Online payment integration
-   Parent notification
-   Auto reminders
