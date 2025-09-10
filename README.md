# Logic Labyrinth 🧩

## 📋 About The Project

Logic Labyrinth adalah sebuah game puzzle berbasis web yang dibangun dengan Laravel untuk mengajarkan konsep pemrograman dan logika. Pemain harus menganalisis kode perintah Python dan menggerakkan karakter macan di grid 5x5 untuk mencapai posisi target yang benar.

### ✨ Fitur Utama

- **Game Logic Puzzle**: Analisis kode Python dan gerakkan karakter sesuai instruksi
- **Multi-level Difficulty**: Level mudah dan susah dengan kompleksitas berbeda
- **Admin Panel**: Kelola level, pemain, dan statistik gameplay
- **Level Generator**: Sistem seeder otomatis untuk menghasilkan level bervariasi
- **Responsive Design**: Tampilan optimal di berbagai perangkat
- **Real-time Gameplay**: Gerakan karakter dengan animasi smooth
- **Statistics Tracking**: Lacak performa pemain dan tingkat keberhasilan

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 11.x
- **Database**: MySQL
- **Frontend**: Blade Templating, Tailwind CSS, Prism.js
- **Autentikasi**: Laravel Breeze
- **Game Logic**: JavaScript ES6+
- **Styling**: Custom CSS dengan animasi
- **Icons**: Custom SVG icons

## 🚀 Panduan Instalasi

### Prasyarat

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL
- Web Server (Apache/Nginx/Laragon)

### Langkah-langkah Instalasi

1. Clone repository
   ```bash
   git clone https://github.com/adikysetiawan/logic-labyrinth.git
   cd logic-labyrinth
   ```

2. Install dependensi PHP
   ```bash
   composer install
   ```

3. Install dependensi JavaScript
   ```bash
   npm install
   ```

4. Salin file .env
   ```bash
   cp .env.example .env
   ```

5. Generate key aplikasi
   ```bash
   php artisan key:generate
   ```

6. Konfigurasi database di file .env
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=logic_labyrinth
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. Jalankan migrasi database
   ```bash
   php artisan migrate
   ```

8. Generate level data (100 level: 50 mudah + 50 susah)
   ```bash
   php artisan db:seed --class=EasyLevelsSeeder
   php artisan db:seed --class=HardLevelsSeeder
   ```

9. Buat symbolic link untuk penyimpanan
   ```bash
   php artisan storage:link
   ```

10. Compile aset frontend
    ```bash
    npm run build
    ```

11. Jalankan server development
    ```bash
    php artisan serve
    ```

12. Akses aplikasi di browser: `http://localhost:8000`

## 🎮 Cara Bermain

1. **Registrasi/Login**: Buat akun atau masuk dengan akun existing
2. **Mulai Game**: Klik "Mulai Bermain" di halaman utama
3. **Analisis Kode**: Baca kode Python yang ditampilkan di panel kanan
4. **Gerakkan Karakter**: Gunakan tombol panah untuk menggerakkan macan
5. **Capai Target**: Ikuti instruksi kode untuk mencapai posisi yang benar
6. **Selesaikan Level**: Klik "Selesai" ketika yakin sudah di posisi target

### Contoh Level Mudah:
```python
moveRight()
if x % 2 == 0:
    moveUp()
else:
    moveDown()
for i in range(2):
    moveLeft()
```

### Contoh Level Susah:
```python
while x < 4 and y > 0:
    moveRight()
    moveUp()
if not (x % 2 == 0 and y % 2 == 1):
    moveLeft()
    moveDown()
for i in range(3):
    if x > 2 or y == 0:
        moveRight()
    else:
        moveLeft()
```

## 👥 Akun Default Admin

Untuk mengakses admin panel, gunakan akun berikut:

- **Email**: admin@example.com
- **Password**: password
- **URL Admin**: `/admin/players`

## 🎯 Fitur Detail

### 1. Sistem Level
- **Easy Levels**: 6 pola gerakan dengan if/else/loop/for sederhana
- **Hard Levels**: 7 pola kompleks dengan while/nested/and/or/not
- **Auto-generated**: 100 level unik dengan algoritma seeder
- **Wrap Logic**: Gerakan melewati batas grid (wrapping)

### 2. Admin Panel
- **Data Pemain**: Kelola pemain dan statistik mereka
- **Data Level**: CRUD level dengan filter dan pencarian
- **Rekap Permainan**: Analisis performa dan tingkat keberhasilan
- **Level Generator**: Buat level custom dengan start position dan target

### 3. Game Engine
- **Grid 5x5**: Koordinat X (0-4) horizontal, Y (0-4) vertikal
- **Movement Logic**: moveUp mengurangi Y, moveDown menambah Y
- **Code Highlighting**: Syntax highlighting dengan Prism.js
- **Real-time Validation**: Cek posisi target secara real-time

### 4. Pola Level yang Didukung

#### Level Mudah:
- For loops sederhana
- If-else dengan 2 cabang
- Nested if ringan
- Kombinasi gerakan berurutan

#### Level Susah:
- While loops dengan kondisi kompleks
- Nested for loops
- Logical operators (and, or, not)
- If-elif-else dengan multiple cabang
- Triple nested conditions
- Complex mathematical conditions

## 🤝 Berkontribusi

Kontribusi sangat diterima untuk membuat proyek ini lebih baik. Berikut cara berkontribusi:

1. Fork proyek ini
2. Buat branch fitur (`git checkout -b feature/namafitur`)
3. Commit perubahan (`git commit -m 'Menambahkan fitur baru'`)
4. Push ke branch (`git push origin feature/namafitur`)
5. Buat Pull Request

## 📝 Lisensi

Dilisensikan di bawah [MIT License](LICENSE).

## 👨‍💻 Penulis

**Adiky Setiawan**  
📧 Email: adikysetiawan@example.com  
🔗 GitHub: [@adikysetiawan](https://github.com/adikysetiawan)  
💼 LinkedIn: [Adiky Setiawan](https://linkedin.com/in/adikysetiawan)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com/) - The PHP Framework for Web Artisans
- [Tailwind CSS](https://tailwindcss.com/) - A utility-first CSS framework
- [Prism.js](https://prismjs.com/) - Lightweight, extensible syntax highlighter
- [Heroicons](https://heroicons.com/) - Beautiful hand-crafted SVG icons

---

<div align="center">
  <p>Dibuat dengan ❤️ menggunakan Laravel</p>
  <p>© 2025 Logic Labyrinth. All rights reserved.</p>
</div>
