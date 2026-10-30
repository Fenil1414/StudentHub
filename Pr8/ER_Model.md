# StudentHub ER Model

## Entities
- **Course** (`course_id` PK, `course_name`, `course_code`)
- **Student** (`student_id` PK, `full_name`, `email` UNIQUE, `enrollment_no` UNIQUE, `course_id` FK, `created_at`)

## Relationship
One Course can have many Students; each Student belongs to one Course (1:N).

```mermaid
erDiagram
    COURSES ||--o{ STUDENTS : contains
    COURSES {
        int course_id PK
        varchar course_name
        varchar course_code
    }
    STUDENTS {
        int student_id PK
        varchar full_name
        varchar email UK
        varchar enrollment_no UK
        int course_id FK
        timestamp created_at
    }
```
