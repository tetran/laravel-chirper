# Entity Relationship Diagram

## Database Schema Overview

```mermaid
erDiagram
    users ||--o{ chirps : "creates"
    users ||--o{ sessions : "has"
    users ||--o{ password_reset_tokens : "requests"

    users {
        integer id PK
        varchar name
        varchar email UK "UNIQUE"
        datetime email_verified_at
        varchar password
        varchar remember_token
        datetime created_at
        datetime updated_at
    }

    chirps {
        integer id PK
        integer user_id FK "CASCADE DELETE"
        varchar message
        datetime created_at
        datetime updated_at
    }

    sessions {
        varchar id PK
        integer user_id "INDEXED"
        varchar ip_address
        text user_agent
        text payload
        integer last_activity "INDEXED"
    }

    password_reset_tokens {
        varchar email PK
        varchar token
        datetime created_at
    }

    failed_jobs {
        integer id PK
        varchar uuid UK "UNIQUE"
        text connection
        text queue
        text payload
        text exception
        datetime failed_at
    }

    jobs {
        integer id PK
        varchar queue "INDEXED"
        text payload
        integer attempts
        integer reserved_at
        integer available_at
        integer created_at
    }

    job_batches {
        varchar id PK
        varchar name
        integer total_jobs
        integer pending_jobs
        integer failed_jobs
        text failed_job_ids
        text options
        integer cancelled_at
        integer created_at
        integer finished_at
    }

    cache {
        varchar key PK
        text value
        integer expiration
    }

    cache_locks {
        varchar key PK
        varchar owner
        integer expiration
    }
```

## Core Business Tables

The main business logic revolves around:

- **users**: User accounts and authentication
- **chirps**: User-generated messages with foreign key relationship to users
- **sessions**: Active user sessions for authentication state
- **password_reset_tokens**: Temporary tokens for password reset flow

## Supporting Infrastructure Tables

- **jobs** / **failed_jobs** / **job_batches**: Queue system for background processing
- **cache** / **cache_locks**: Application caching layer

## Key Relationships

1. **users → chirps**: One-to-many relationship with cascade delete
2. **users → sessions**: One-to-many relationship (indexed for performance)
3. **users → password_reset_tokens**: One-to-one relationship via email

## Indexes

- `users.email`: Unique index for authentication
- `sessions.user_id`: Index for session lookups
- `sessions.last_activity`: Index for cleanup operations
- `jobs.queue`: Index for queue processing
- `failed_jobs.uuid`: Unique index for job identification
