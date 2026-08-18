# Portal-BTMK&KKP
A role-based management portal with dual authentication (Admin/Staff), customized user dashboards, and landing page. 

## Features
* **Role-Based Authentication:** Separate login flows for Admin and Staff.
* **Admin Dashboard:** System management and overview control.
* **Staff Dashboard:** User-level tools and daily workflow management.
* **Homepage:** Public landing page.
* **Public Hazard Reporting:** Visitors can submit hazard reports directly from the homepage without logging in.

## Project Structure & Database
* The database file/schema is located in the `/db` folder.
* Refer to `/db` for initial setup scripts and migration files.

* ## OPTIONAL (Guna XAMPP)

### Step 1: Download Code
1. Klik butang hijau **`< Code >`** dkat atas, lepas tu tekan **Download ZIP**.
2. *Unzip* folder yang dimuat turun tadi.

### Step 2: Simpan dalam Folder XAMPP
1. *Copy* folder projek tu.
2. *Paste* dalam folder ini di komputer anda:  
   `C:\xampp\htdocs\`

### Step 3: Setup Database
1. Buka **XAMPP Control Panel** -> Tekan **Start** pada **Apache** dan **MySQL**.
2. Buka *browser* dan pergi ke `http://localhost/phpmyadmin`.
3. Buat *database* baru.
4. Tekan **Import** -> Pilih fail `.sql` di dalam folder `/db` projek anda -> Tekan **Go**.

### Step 4: Buka Portal
Buka *browser* dan layari:  
`http://localhost/nama-folder-anda`
