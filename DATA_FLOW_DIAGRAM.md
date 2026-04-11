# Data Flow Diagram (DFD) - School Registration & Payment System

This document outlines the flow of data within the School Registration & Payment System, illustrating how information moves between external entities, processes, and data stores.

## 1. System Context Diagram (Level 0)

The Level 0 diagram shows the system as a single process interacting with external entities.

```mermaid
graph LR
    subgraph External_Entities
        Registrar((Registrar))
        Accountant((Accountant))
        Admin((Admin))
        StudentParent((Student/Parent))
    end

    System[School Registration & Payment System]

    Registrar -- "Student/Enrollment Data" --> System
    Accountant -- "Payment/Tuition Plan Data" --> System
    Admin -- "Configuration & User Data" --> System
    
    System -- "Registration Codes / Receipts" --> StudentParent
    System -- "Dashboards & Reports" --> Admin
    System -- "Financial Summaries" --> Accountant
```

---

## 2. Detailed Data Flow Diagram (Level 1)

The Level 1 diagram breaks down the main system into its primary functional processes.

```mermaid
graph TD
    %% Entities
    Registrar((Registrar))
    Accountant((Accountant))
    Admin((Admin))
    StudentParent((Student/Parent))

    %% Processes
    P1[P1: Student Management]
    P2[P2: Academic Configuration]
    P3[P3: Enrollment Processing]
    P4[P4: Payment & Tuition Management]
    P5[P5: Reporting & Analytics]

    %% Data Stores
    D1[(D1: Students)]
    D2[(D2: Enrollments)]
    D3[(D3: Payments)]
    D4[(D4: Academic Config<br/>Grades/Classes/Terms/Turns)]
    D5[(D5: Users/Roles)]

    %% Flow: Student Registration
    Registrar -- "Personal Info" --> P1
    P1 -- "Store Student" --> D1
    D1 -- "Student Code" --> P1
    P1 -- "Code/Confirmation" --> StudentParent

    %% Flow: Academic Setup
    Admin -- "Grade/Class/Term Setup" --> P2
    P2 -- "Define Structure" --> D4
    P2 -- "Assign Roles" --> D5

    %% Flow: Enrollment
    Registrar -- "Assign Class/Term" --> P3
    D4 -- "Available Classes" --> P3
    D1 -- "Student ID" --> P3
    P3 -- "Record Enrollment" --> D2

    %% Flow: Payment
    Accountant -- "Payment Details" --> P4
    D2 -- "Enrollment Status" --> P4
    P4 -- "Calculate Study Period" --> D3
    D3 -- "Payment Receipt" --> P4
    P4 -- "Issue Receipt" --> StudentParent
    P3 -- "Trigger Status Update" --> P4

    %% Flow: Reporting
    D1 & D2 & D3 -- "Raw Data" --> P5
    P5 -- "Performance Logs" --> Admin
    P5 -- "Financial Reports" --> Accountant
```

---

## 3. Data Flow Definitions

| Entity/Store | Description |
| :--- | :--- |
| **Registrar** | Staff responsible for entering student details and performing initial class assignments. |
| **Accountant** | Staff managing the financial side, from setting tuition prices to recording payments. |
| **D1: Students** | Stores `first_name`, `last_name`, `student_code`, `date_of_birth`, and `parent_contact`. |
| **D2: Enrollments** | Links a Student to a Classroom and a Term. Tracks `status` (Active/Dropped). |
| **D3: Payments** | Stores transaction details including `start_study_date` and `end_study_date`. |
| **D4: Academic Config** | Central store for Grades (1-6), Classrooms (A, B, C), Terms (2025, 2026), and Turns (Morning, Afternoon). |

---

## 4. Prompt Used to Generate This Diagram

If you need to regenerate or expand this diagram in the future, you can use the following prompt:

> **"Act as a System Architect. Based on a Laravel School Management codebase, create a Level 1 Data Flow Diagram (DFD) using Mermaid.js. Focus on the lifecycle of a Student: from initial registration (P1) and enrollment into a class (P3), to payment processing (P4) which calculates study duration based on monthly plans. Include data stores for Students, Enrollments, Payments, and Academic Configuration (Grades/Classrooms/Terms). Ensure entities like Registrar, Accountant, and Admin are clearly mapped to their respective processes and data flows."**
