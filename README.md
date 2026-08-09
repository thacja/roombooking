# ระบบจองห้องเรียน (Room Booking System)

ระบบจัดการการจองห้องเรียนสำหรับมหาวิทยาลัย โรงเรียน หรือองค์กรที่ต้องการจัดการการใช้ห้องอย่างมีประสิทธิภาพ พร้อมระบบสิทธิ์การเข้าถึง แจ้งเตือน และติดตามสถานะการจอง

## คุณสมบัติหลัก

- ค้นหาห้องว่างตามวันที่ เวลา ความจุ และอาคาร
- จองห้องเรียนออนไลน์ พร้อมระบบอนุมัติ
- บัญชีผู้ใช้งานหลายบทบาท: ผู้ใช้ทั่วไป, ผู้จัดการห้อง, ผู้ดูแลระบบ
- ติดตามสถานะการจอง: รออนุมัติ, อนุมัติแล้ว, ปฏิเสธ
- แจ้งเตือนภายในระบบ
- บันทึกประวัติการเปลี่ยนแปลงสถานะ
- จัดการวันหยุดและการตั้งค่าระบบ

## เทคโนโลยี

- PHP 8.2+
- MySQL 8.x
- XAMPP (Apache + MySQL)
- Composer

## การติดตั้ง

### 1. Clone โปรเจกต์

```bash
git clone <repository-url> roombooking
cd roombooking
```

### 2. ติดตั้ง Dependencies

```bash
composer install
```

### 3. ตั้งค่าฐานข้อมูล

สร้างฐานข้อมูล MySQL:

```sql
CREATE DATABASE roombooking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. ตั้งค่า Environment

แก้ไขไฟล์ `.env`:

```env
APP_NAME="Room Booking System"
APP_ENV=local
APP_URL=http://localhost/roombooking

DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=roombooking
DB_USERNAME=root
DB_PASSWORD=
```

### 5. สร้างตารางและ Seed ข้อมูล

```bash
php storage/database/schema.sql
php storage/database/seeds/seed.sql
```

หรือใช้ mysql command line:

```bash
mysql -u root roombooking < storage/database/schema.sql
mysql -u root roombooking < storage/database/seeds/seed.sql
```

### 6. เริ่มรันเซิร์ฟเวอร์

เปิด XAMPP Control Panel และ Start Apache + MySQL

เปิดเบราว์เซอร์ไปที่:

```
http://localhost/roombooking
```

## บัญชีทดสอบ

| บทบาท | อีเมล | รหัสผ่าน |
|-------|-------|----------|
| ผู้ดูแลระบบ | admin@university.ac.th | password |
| ผู้จัดการห้อง | prof.somchai@university.ac.th | password |
| ผู้ใช้งานทั่วไป | student.panida@university.ac.th | password |

## โครงสร้างโปรเจกต์

```
roombooking/
├── app/
│   ├── Config/           # การตั้งค่าฐานข้อมูลและ session
│   ├── Controllers/      # Controller หลัก
│   ├── Database.php      # Database wrapper (PDO)
│   ├── Helpers/          # Helper functions และ auth
│   ├── Middleware/        # Auth middleware
│   ├── Routes/           # Route definitions
│   └── Views/            # HTML templates
├── public/               # Entry point (index.php)
├── storage/
│   └── database/
│       ├── schema.sql    # ตารางฐานข้อมูล
│       └── seeds/
│           └── seed.sql  # ข้อมูลตัวอย่าง
├── .env                  # Environment configuration
├── bootstrap.php         # App bootstrap
├── composer.json
└── index.php             # Front controller
```

## เส้นทาง (Routes)

| Method | Path | คำอธิบาย |
|--------|------|----------|
| GET | `/` | หน้าแรก |
| GET | `/rooms` | ค้นหาห้องว่าง |
| GET | `/login` | หน้าเข้าสู่ระบบ |
| POST | `/login` | ประมวลผลเข้าสู่ระบบ |
| GET | `/logout` | ออกจากระบบ |
| GET | `/dashboard` | หน้า Dashboard (ต้องการ login) |
| GET | `/admin` | หน้า Admin (ต้องการ role admin) |

## License

MIT
