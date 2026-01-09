# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer Bearer {YOUR_TOKEN_HERE}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Dapatkan token dengan melakukan <b>POST</b> ke <code>/api/login</code> menggunakan email dan password. Token harus disertakan di header <code>Authorization: Bearer {token}</code> untuk semua endpoint yang memerlukan autentikasi.
