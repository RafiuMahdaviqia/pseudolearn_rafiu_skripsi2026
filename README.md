# Penyimpanan Database Chatbot Adaptif

## Gambaran Umum
Chatbot adaptif di PseudoLearn menggunakan database untuk menyimpan dan mengelola data yang berkaitan dengan interaksi pengguna, metrik performa, dan respons adaptif. Hal ini memastikan bahwa chatbot dapat memberikan bimbingan yang personal dan sesuai dengan konteks pengguna.

## Tabel-Tabel Database
Berikut adalah contoh tabel-tabel database yang digunakan untuk menyimpan data terkait chatbot:

### 1. **user_interactions**
   - **Tujuan**: Menyimpan data tentang interaksi pengguna dengan chatbot.
   - **Kolom-Kolom**:
     - `id`: Kunci utama (Primary Key).
     - `user_id`: Kunci asing (Foreign Key) yang merujuk ke tabel pengguna.
     - `interaction_type`: Jenis interaksi (contoh: pertanyaan, jawaban).
     - `timestamp`: Waktu terjadinya interaksi.

### 2. **performance_metrics**
   - **Tujuan**: Melacak metrik performa pengguna untuk menentukan perilaku belajar mereka.
   - **Kolom-Kolom**:
     - `id`: Kunci utama (Primary Key).
     - `user_id`: Kunci asing (Foreign Key) yang merujuk ke tabel pengguna.
     - `total_drag`: Jumlah aksi drag-and-drop yang dilakukan.
     - `total_time`: Total waktu yang dihabiskan untuk menyelesaikan tugas.
     - `label`: Klasifikasi perilaku (contoh: Struggling, Gaming the System).

### 3. **adaptive_responses**
   - **Tujuan**: Menyimpan respons adaptif yang dihasilkan oleh chatbot.
   - **Kolom-Kolom**:
     - `id`: Kunci utama (Primary Key).
     - `user_id`: Kunci asing (Foreign Key) yang merujuk ke tabel pengguna.
     - `response_text`: Teks respons yang ditampilkan kepada pengguna.
     - `response_type`: Jenis respons (contoh: suportif, menantang).
     - `timestamp`: Waktu respons dihasilkan.

## Alur Data
1. **Interaksi Pengguna**: Ketika pengguna berinteraksi dengan chatbot, detail interaksi dicatat di tabel `user_interactions`.
2. **Analisis Performa**: Chatbot menganalisis metrik performa pengguna yang tersimpan di tabel `performance_metrics` untuk mengklasifikasikan perilaku mereka.
3. **Respons Adaptif**: Berdasarkan klasifikasi, respons adaptif dihasilkan dan disimpan di tabel `adaptive_responses`.

## Manfaat Penyimpanan Database
- **Persistensi**: Memastikan data pengguna tidak hilang di antara sesi.
- **Analisis**: Memungkinkan analisis mendalam terhadap perilaku pengguna dari waktu ke waktu.
- **Adaptabilitas**: Mendukung pembuatan respons yang personal berdasarkan data historis.

## Pengembangan di Masa Depan
- **Enkripsi Data**: Untuk memastikan privasi dan keamanan data pengguna.
- **Analitik Real-Time**: Untuk adaptasi yang lebih dinamis dan langsung.
- **Integrasi dengan Learning Analytics**: Untuk memberikan wawasan yang lebih mendalam tentang pola belajar pengguna.

Dengan struktur database ini, chatbot adaptif di PseudoLearn dapat memberikan pengalaman belajar yang lebih efektif dan personal bagi setiap pengguna.

---

## 🖥️ Panduan Menjalankan Project di VSCode

Ikuti langkah-langkah berikut untuk menjalankan project PseudoLearn secara lokal menggunakan VSCode.

### Prasyarat
- PHP >= 8.1
- Composer
- Node.js >= 18 & npm
- MySQL / MariaDB
- [VSCode](https://code.visualstudio.com/) dengan ekstensi PHP Intelephense (opsional)

### 1. Install Dependencies

Buka terminal di VSCode (`Ctrl+` ` `) lalu jalankan:

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Setup Environment & Application Key

```bash
# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database serta Gemini API:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pseudolearn
DB_USERNAME=root
DB_PASSWORD=

# Gemini API (untuk fitur chatbot)
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-2.5-flash
GEMINI_URL=https://generativelanguage.googleapis.com/v1beta/models/
```

### 3. Jalankan Migrasi Database

```bash
# Buat database terlebih dahulu di MySQL, lalu:
php artisan migrate

# (Opsional) Seed data awal
php artisan db:seed
```

Jika ingin menjalankan backfill konteks chatbot (untuk data lama):

```bash
php artisan chatbot:backfill-context
```

### 4. Jalankan Development Server

```bash
php artisan serve
```

Server akan berjalan di `http://127.0.0.1:8000`.

### 5. Build Frontend Assets

Di terminal terpisah (split terminal VSCode dengan `Ctrl+Shift+5`):

```bash
# Mode development (watch untuk hot reload)
npm run dev

# Atau build untuk production
npm run build
```

### 6. Bersihkan Cache (jika diperlukan)

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### 7. Test Endpoint Chatbot

Setelah server berjalan, uji endpoint berikut melalui browser atau tool seperti Postman:

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `POST /chatbot/open` | POST | Buka sesi chatbot, terima `access_id` |
| `POST /chatbot/send` | POST | Kirim pesan ke chatbot |
| `POST /chatbot/close` | POST | Tutup sesi chatbot |
| `POST /chatbot/check-performance` | POST | Cek performa real-time mahasiswa |
| `POST /chatbot/adaptive-guide` | POST | Dapatkan panduan adaptif |
| `GET /log-chatbot` | GET | Halaman log chatbot detail |
| `GET /log-data-chatbot` | GET | Halaman log data chatbot (admin) |
| `GET /log-chatbot-adaptive` | GET | Halaman log chatbot adaptive (admin) |

**Contoh payload `/chatbot/send`:**
```json
{
  "message": "Apa itu variabel?",
  "access_id": "uuid-dari-chatbot-open",
  "id_soal": "soal-uuid",
  "id_level": "level-uuid"
}
```

### Tips VSCode
- Gunakan ekstensi **PHP Intelephense** untuk autocomplete PHP.
- Gunakan ekstensi **Laravel Blade Snippets** untuk syntax highlight Blade.
- Gunakan **Thunder Client** atau **REST Client** untuk test API langsung di VSCode.
- Pastikan `.env` tidak di-commit ke git (sudah ada di `.gitignore`).

