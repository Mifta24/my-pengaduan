# Introduction

REST API untuk sistem pengaduan masyarakat RT/RW. API ini memungkinkan warga untuk melaporkan keluhan, melihat pengumuman, dan berinteraksi dengan admin.

<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

    ## Selamat Datang di MyPengaduan API

    API ini menyediakan akses penuh ke sistem pengaduan masyarakat untuk aplikasi mobile Flutter.

    ### Base URL
    Semua endpoint dapat diakses melalui: `{base_url}/api`

    ### Authentication
    Sebagian besar endpoint memerlukan autentikasi menggunakan Laravel Sanctum bearer token.
    Dapatkan token dengan login melalui endpoint `/api/login`.

    ### Response Format
    Semua response menggunakan format JSON dengan struktur:
    ```json
    {
        "status": true,
        "message": "Success message",
        "data": {}
    }
    ```

    <aside>Scroll ke bawah untuk melihat daftar endpoint lengkap dengan contoh request dan response.</aside>

