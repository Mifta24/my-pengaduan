erDiagram
    USERS {
        bigint id PK
        string name
        string email
        string password
        string role
        text alamat
        timestamp created_at
        timestamp updated_at
    }

    COMPLAINTS {
        bigint id PK
        bigint user_id FK
        string judul
        text deskripsi
        string kategori
        string foto
        string lokasi
        enum status
        text tanggapan
        date tanggal_laporan
        timestamp created_at
        timestamp updated_at
    }

    RESPONSES {
        bigint id PK
        bigint complaint_id FK
        bigint user_id FK
        text isi
        string foto
        timestamp created_at
    }

    ANNOUNCEMENTS {
        bigint id PK
        string judul
        text isi
        date tanggal
        bigint created_by FK
        timestamp created_at
        timestamp updated_at
    }

    %% --- RELATIONSHIPS ---
    USERS ||--o{ COMPLAINTS : "mengajukan"
    COMPLAINTS ||--o{ RESPONSES : "memiliki"
    USERS ||--o{ RESPONSES : "memberikan"
    USERS ||--o{ ANNOUNCEMENTS : "membuat"
