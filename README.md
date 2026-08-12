# Blog Post API

A robust, modern RESTful API built with Laravel 11 and PostgreSQL running inside Docker. This project manages blog posts and categories using professional backend standards, robust data validation, and clean JSON transformations.

## 🚀 Tech Stack & Infrastructure

- **Framework:** Laravel 11 (Minimalist App Architecture)
- **Database:** PostgreSQL (Official Docker Image)
- **Containerization:** Docker Desktop with a managed Named Volume (`pg_blog_data`)
- **Local Environment:** Native PHP (`php artisan serve`) connecting directly to the Docker DB port
- **API Testing:** Postman

---

## 🛠 Features Implemented

- **Advanced Eloquent Relationships:** Optimized Many-to-One mapping linking `BlogPost` to `CategoryBlogPost` using a custom naming convention.
- **N+1 Query Prevention:** Efficient usage of Eloquent Eager Loading (`with()`) and Lazy Eager Loading (`load()`).
- **Data Encapsulation:** Custom API Resources (`BlogPostResource` and `CategoryBlogPostResource`) utilizing `$this->whenLoaded()` to conditionally nest secure JSON relationships.
- **Strict Data Validation:** Custom Form Requests managing explicit type-casting rules (e.g., stopping numeric strings from reaching decimal database columns) with custom friendly error responses.
- **Clean String Interpolation:** Modern PHP Complex String Syntax parsing for dynamic and secure runtime return statements.

---

## ⚙️ Installation & Local Setup

### 1. Clone the Project
```bash
git clone https://github.com
cd Blog-Post-Api
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Spin Up the PostgreSQL Database via Docker
Run the exact Docker command to spin up the database container with a persistent named volume:
```bash
docker run --name my-postgres -e POSTGRES_PASSWORD=mysecretpassword -v pg_blog_data:/var/lib/postgresql/data -p 5432:5432 -d postgres
```

### 4. Setup Environment Configuration
Copy the `.env.example` file to `.env`:
```bash
cp .env.example .env
```
Open `.env` and set up the connection pointing to your live Docker container For example:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=mysecretpassword
```

### 5. Run Database Migrations
Wipe the schema clean and build the optimized table tables structure from scratch:
```bash
php artisan migrate:fresh
```

### 6. Boot the Local Server
```bash
php artisan serve
```
The application will be accessible locally at `http://127.0.0`.

---

## 🛣 API Endpoints (Routes Mapping)

You can check all active system endpoints using `php artisan route:list`. The main endpoints for `api/blog-posts` are:

| HTTP Method | URI | Controller Action | Purpose |
| :--- | :--- | :--- | :--- |
| **GET** | `/api/blog-posts` | `index` | Display list of posts + nested category metadata |
| **POST** | `/api/blog-posts` | `store` | Validate payload data type and save a new post |
| **GET** | `/api/blog-posts/{id}`| `show` | Fetch a single post (Supports optional `include_coach` / elements) |
| **PUT/PATCH**| `/api/blog-posts/{id}`| `update` | Update fields safely and reload structural arrays |
| **DELETE** | `/api/blog-posts/{id}`| `destroy` | Safely wipe post record and return dynamic validation strings |

---

## 🔒 Security & Request Validation Example
If a client sends an invalid payload data type (e.g., entering an integer for the `author` text string or a fake category pointer), the `StoreBlogPostRequest` halts execution immediately and passes back clean JSON validation feedback:

```json
{
    "message": "The author field must contain text characters only, not numbers.",
    "errors": {
        "author": [
            "The author field must contain text characters only, not numbers."
        ]
    }
}
```
