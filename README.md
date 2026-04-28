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

