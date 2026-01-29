I consider the README an essential
part of a project because here, like a book, the
most important dynamics of the project are narrated.

I consider comments an integral part of the code because they
narrate behavior that you have only in that moment and then
lose forever.

---------------------------------------------------------
        Automatic Installation...
---------------------------------------------------------
Requirements: Linux (Debian/Ubuntu).
Open the terminal, navigate to the main project folder,
and type:

```bash
chmod +x init.sh
./init.sh
```

I wanted to write a bash file to configure everything
very quickly.

---------------------------------------------------------
        Manually Installation...
---------------------------------------------------------
If something should fail, here's what you need to do manually:
Install Docker and Docker Compose.
Inside the project folder, run:

```bash
docker compose up -d --build
```

Once the containers are up, enter the Laravel container:

```bash
docker exec -it laravel_app bash
```

This way you can use artisan.

```bash
php artisan migrate:fresh
php artisan db:seed --class=Database\\Seeders\\FakeDataSeeder
```

If everything went well, you should see the app at: `http://localhost:8080`

----------------------------------------------------------
        Structure
----------------------------------------------------------
Starting by building an abstract structure.

```
bookManager/
├── docker-compose.yml
├── nginx/
├── php/
├── src/
│   └── # Laravel
│
├── README.md
```

----------------------------------------------------------
        Book Manager 
----------------------------------------------------------
Book Manager is a Laravel software for library management
system.
We will use Docker to automate the deployment.

---------------------------------------------------------
        Architecture
---------------------------------------------------------
- **Backend**: PHP, Laravel
- **Frontend**: Blade, HTML, CSS, JS, Ajax
- **Database**: MySQL
- **Webserver**: Nginx

---------------------------------------------------------
        Step 1: System config
---------------------------------------------------------
I'm going to create a docker-compose.yml for installing 
services like Laravel, MySQL, Nginx.
I have created the docker-compose file and php/nginx folders.
I have chosen to split PHP-FPM and Nginx (best practice).
Obviously persistent MySQL volume.
Laravel is also mounted as a volume (hot reload).
Now we have a clean and replicable environment.

Run:
```bash
docker-compose up -d --build
```

It will install Laravel and other stuff and will start
the containers.

All done. Just visit: `http://localhost:8080`

---------------------------------------------------------
        Step 2: Database scheme
---------------------------------------------------------
Now I'm going to show you the db schema I think is the best
in this case:

---

**Users**

| Field      | Type                  | Constraints       | Description                    |
|------------|-----------------------|-------------------|--------------------------------|
| id         | BIGINT                | PK, AUTO_INCREMENT| Unique user identifier         |
| name       | VARCHAR(255)          | NOT NULL          | Full name                      |
| email      | VARCHAR(255)          | UNIQUE, NOT NULL  | User email                     |
| password   | VARCHAR(255)          | NOT NULL          | Bcrypt hashed password         |
| role       | ENUM('admin','user')  | NOT NULL          | User role (default: user)      |
| created_at | TIMESTAMP             | NOT NULL          | Creation timestamp             |
| updated_at | TIMESTAMP             | NOT NULL          | Last update timestamp          |
| deleted_at | TIMESTAMP             | NULL              | Soft delete timestamp          |

---

**Categories**

| Field       | Type         | Constraints       | Description                    |
|-------------|--------------|-------------------|--------------------------------|
| id          | BIGINT       | PK, AUTO_INCREMENT| Unique category identifier     |
| name        | VARCHAR(255) | NOT NULL          | Category name                  |
| description | TEXT         | NULL              | Optional description           |
| created_at  | TIMESTAMP    | NOT NULL          | Creation timestamp             |
| updated_at  | TIMESTAMP    | NOT NULL          | Last update timestamp          |
| deleted_at  | TIMESTAMP    | NULL              | Soft delete timestamp          |

---

**Books**

| Field        | Type         | Constraints       | Description                    |
|--------------|--------------|-------------------|--------------------------------|
| id           | BIGINT       | PK, AUTO_INCREMENT| Unique book identifier         |
| title        | VARCHAR(255) | NOT NULL          | Book title                     |
| isbn         | VARCHAR(20)  | UNIQUE, NOT NULL  | ISBN code                      |
| description  | TEXT         | NULL              | Book description               |
| author       | VARCHAR(255) | NOT NULL          | Author name                    |
| publisher    | VARCHAR(255) | NULL              | Publisher name                 |
| year         | YEAR         | NULL              | Publication year               |
| category_id  | BIGINT       | FK, NOT NULL      | Foreign key to categories      |
| cover_image  | VARCHAR(255) | NULL              | Cover image path               |
| created_at   | TIMESTAMP    | NOT NULL          | Creation timestamp             |
| updated_at   | TIMESTAMP    | NOT NULL          | Last update timestamp          |
| deleted_at   | TIMESTAMP    | NULL              | Soft delete timestamp          |

---

**BookCopies**

| Field      | Type                                              | Constraints       | Description                    |
|------------|---------------------------------------------------|-------------------|--------------------------------|
| id         | BIGINT                                            | PK, AUTO_INCREMENT| Unique copy identifier         |
| book_id    | BIGINT                                            | FK, NOT NULL      | Foreign key to books           |
| barcode    | VARCHAR(64)                                       | UNIQUE, NOT NULL  | Unique barcode                 |
| condition  | ENUM('very good','good','bad')                    | NOT NULL          | Physical condition (good)      |
| status     | ENUM('available','reserved','loaned','maintenance')| NOT NULL         | Copy status (available)        |
| notes      | TEXT                                              | NULL              | Internal notes                 |
| created_at | TIMESTAMP                                         | NOT NULL          | Creation timestamp             |
| updated_at | TIMESTAMP                                         | NOT NULL          | Last update timestamp          |
| deleted_at | TIMESTAMP                                         | NULL              | Soft delete timestamp          |

---

**Reservations**

| Field         | Type                                    | Constraints       | Description                    |
|---------------|-----------------------------------------|-------------------|--------------------------------|
| id            | BIGINT                                  | PK, AUTO_INCREMENT| Unique reservation identifier  |
| user_id       | BIGINT                                  | FK, NOT NULL      | Foreign key to users           |
| book_copy_id  | BIGINT                                  | FK, NOT NULL      | Foreign key to book_copies     |
| reserved_at   | DATETIME                                | NOT NULL          | Reservation date and time      |
| due_date      | DATETIME                                | NOT NULL          | Expected return date           |
| returned_at   | DATETIME                                | NULL              | Actual return date             |
| status        | ENUM('active','completed','cancelled')  | NOT NULL          | Reservation status (active)    |
| extended_count| INTEGER                                 | NOT NULL          | Number of extensions (0)       |
| created_at    | TIMESTAMP                               | NOT NULL          | Creation timestamp             |
| updated_at    | TIMESTAMP                               | NOT NULL          | Last update timestamp          |
| deleted_at    | TIMESTAMP                               | NULL              | Soft delete timestamp          |

---

**Relations Schema:**
- Users <-> Reservations (1:N) 
- Books <-> BookCopies (1:N)
- Categories <-> Books (1:N)  
- BookCopies <-> Reservations (1:N)

**How REDIS could be implemented (not implemented):**
- **Books**: Filterable book catalog cache, book list, book details
- **Categories**: Quick filter category list
- **BookCopies**: Available copies count
- **Reservations**: Active reservation count, latest reservations

---------------------------------------------------------
        Step 3: UI Created
---------------------------------------------------------
UI is an important part because a clean UI will
also keep the backend clean.
This is a small project so we are going to use
vanilla HTML, CSS, JS, and Bootstrap.
I split the UI into two parts:
- **Dashboard**: Admin view focused on data and management (functional).
- **Showcase**: User view for booking and viewing reservations (attractive).

---------------------------------------------------------
        Step 4: Backend
---------------------------------------------------------
You will find specific comments in the controllers like:
```
/*
* @context: //
* @problem: //
* @solution: //
* @impact: //
*/
```
For standard session persistence, I usually handle it via 
Redis. However, since this environment uses the 'file' session 
driver, I retrieve user identity and roles directly from MySQL 
to ensure data integrity and avoid disk I/O overhead for 
custom session payloads.

To manipulate the database, being a small project, I 
decided to use Eloquent ORM which is heavier in 
RAM than the query builder, but being a small project it's 
not a problem because we won't load many objects in RAM.

I already mentioned that I decided that in this application there are 2 macro groups:
- Dashboard
- Showcase

This condition has been separated except in the authentication concept.
Authentication is managed by a single controller for simplicity reasons.
I have adopted the use of try-catch blocks.

To store book covers, I followed the best practice, which is to store
files inside `/storage/app/public/covers`.
As a plus, I could also lighten the filesystem by organizing subfolders.
For example: `covers/2021`, `covers/2022`.
Or even more subfolders.
I believe that in this case it would have been overkill.

---------------------------------------------------------
        Step 5: Security
---------------------------------------------------------
- **SQL Injection**: Protected thanks to Eloquent/PDO.
- **Brute Force**: Protected thanks to throttle middleware.
- **XSS**: Protected - I used these `{{ $variables }}` in Blade which already perform escaping.
- **CSRF**: Protected by `@csrf` in HTML forms.
- **Password Hashing**: Done with `Hash::check` (Bcrypt)

I used Route Model Binding - Laravel will automatically throw a 404 
error if an ID doesn't exist. I could have created custom 404 pages 
but I consider it overkill.
