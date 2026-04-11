# School Registration & Payment System - Entity Relationship Diagram

This document illustrates the data structure and relationships within the School Registration & Payment System.

## ER Diagram (Mermaid)

```mermaid
erDiagram
    TERM ||--o{ GRADE : "has"
    TERM ||--o{ STUDENT : "has"
    TERM ||--o{ ENROLLMENT : "has"
    
    GRADE ||--o{ CLASSROOM : "has"
    GRADE ||--o{ ENROLLMENT : "has"
    
    TURN ||--o{ CLASSROOM : "has"
    
    TEACHER ||--o{ CLASSROOM : "instructs"
    TEACHER ||--o{ STUDENT : "mentors"
    
    CLASSROOM ||--o{ STUDENT : "contains"
    CLASSROOM ||--o{ ENROLLMENT : "records"
    
    STUDENT ||--o{ ENROLLMENT : "joins"
    STUDENT ||--o{ PAYMENT : "makes"
    
    ENROLLMENT ||--o{ PAYMENT : "generates"
    
    TUITION_PLAN ||--o{ PAYMENT : "defined_by"
    
    USER ||--o{ PAYMENT : "processes (created_by)"
    
    PAYMENT ||--o{ PAYMENT_LOG : "audits"

    STUDENT {
        id int
        student_code string
        first_name string
        last_name string
        gender enum
        date_of_birth date
        classroom_id int
        term_id int
        teacher_id int
        status enum
        study_status enum
    }

    ENROLLMENT {
        id int
        student_id int
        classroom_id int
        term_id int
        grade_id int
        is_current boolean
        status string
    }

    PAYMENT {
        id int
        student_id int
        enrollment_id int
        tuition_plan_id int
        amount decimal
        payment_date date
        status enum
        created_by int
    }

    CLASSROOM {
        id int
        grade_id int
        turn_id int
        teacher_id int
        name string
        capacity int
    }

    TEACHER {
        id int
        teacher_code string
        name string
        phone string
    }

    GRADE {
        id int
        term_id int
        name string
    }

    TERM {
        id int
        name string
        start_date date
        end_date date
    }

    TURN {
        id int
        name string
        start_time time
        end_time time
    }

    TUITION_PLAN {
        id int
        name string
        amount decimal
    }

    USER {
        id int
        name string
        email string
    }

    PAYMENT_LOG {
        id int
        payment_id int
        action string
        details text
    }
```

## Entity Descriptions

### Core Academic Entities
- **Term**: Represents academic periods (e.g., Semester 1, Semester 2). All students and enrollments are tied to a term.
- **Grade**: Logical levels (e.g., Grade 1, Grade 2). Each grade belongs to a specific term and contains classrooms.
- **Turn**: Study shifts (e.g., Morning, Afternoon, Evening).
- **Teacher**: Contains instructor details. A teacher can instruct multiple classrooms and be a mentor to specific students.
- **Classroom**: The physical or logical class unit. It links a Grade, a Turn, and a Teacher.

### Student & Enrollment
- **Student**: The central entity containing demographic and academic status information.
- **Enrollment**: Acts as a join table with historical tracking. It records which classroom, grade, and term a student was part of at a specific time. `is_current` indicates the active placement.

### Payment System
- **TuitionPlan**: Defined pricing models for different courses or grades.
- **Payment**: Records financial transactions. Each payment is linked to a student, an enrollment record, and a tuition plan.
- **PaymentLog**: Stores audit trails for payment modifications or status changes.
- **User**: The administrative staff member who processed the transaction.

## Key Relationships Logic
1. **Student Placement**: A Student is directly linked to a `classroom_id`, but the `enrollment` table tracks the history of these placements across terms and grades.
2. **Financial Tracking**: Payments are linked to both `student_id` and `enrollment_id`. This ensures that payments are correctly attributed to the specific class/term period.
3. **Teacher Assignment**: Teachers are linked both to `classrooms` (operational) and directly to `students` (mentorship/primary contact).
