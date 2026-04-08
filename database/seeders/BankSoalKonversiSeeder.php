<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSoalKonversiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id'         => '0199651d-b77a-7329-8b9a-0e5b6b2f16d6',
                'id_level'   => '01995dec-678e-70cf-854a-b25e2c2d0d28',
                'id_soal'    => '01995df6-35ed-7363-9d42-578622c3e4f2',
                'jawaban'    => '["double pajak = 0.1;", "int harga_motor = 25000000;", "int uang_bayar;", "float pajak_jual;", "pajak_jual = pajak * harga_motor;", "uang_bayar = harga_motor + pajak_jual;", "System.out.print(uang_bayar);"]',
                'output'     => '27500000',
                'created_at' => '2025-09-20 03:14:30',
                'updated_at' => '2025-10-02 05:41:45',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996525-5cf1-7256-8f2f-184909a171ff',
                'id_level'   => '01995dec-678e-70cf-854a-b25e2c2d0d28',
                'id_soal'    => '01995e0a-74d3-73fa-895b-cfe6f4ec6ab6',
                'jawaban'    => '["int harga_dasar;", "float persentase_pajak;", "int harga_akhir;", "float nilai_pajak;", "harga_dasar = 85000;", "persentase_pajak = 0.1;", "nilai_pajak = harga_dasar * persentase_pajak;", "harga_akhir = harga_dasar + nilai_pajak;", "System.out.print(\\"Harga akhir setelah pajak: Rp\\" + harga_akhir);"]',
                'output'     => 'Harga akhir setelah pajak: Rp93500',
                'created_at' => '2025-09-20 03:22:51',
                'updated_at' => '2025-10-02 05:40:16',
                'deleted_at' => null,
            ],
            [
                'id'         => '0199652a-ab5d-7201-b71c-3e984cfd80f4',
                'id_level'   => '01995dec-678e-70cf-854a-b25e2c2d0d28',
                'id_soal'    => '01995e1e-b10a-70d3-8210-d2eee6fe2807',
                'jawaban'    => '["int panjang = 10;", "int lebar = 6;", "float tinggi = 1.5f;", "double luas_bagian;", "luas_bagian = 2 * ((panjang * lebar) + (panjang * tinggi) + (lebar * tinggi));", "System.out.println(\\"Luas Bagian: \\" +luas_bagian);"]',
                'output'     => 'Luas Bagian: 168.0',
                'created_at' => '2025-09-20 03:28:39',
                'updated_at' => '2025-10-02 05:35:45',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996533-c470-7186-8aff-58267983f830',
                'id_level'   => '01995dec-678e-70cf-854a-b25e2c2d0d28',
                'id_soal'    => '01995e24-1a61-73eb-b623-a0220a69532e',
                'jawaban'    => '["int panjang = 20;", "int lebar = 20;", "int tinggi = 7;", "long volume;", "System.out.println(\\"Panjang: \\" + panjang + \\", Lebar: \\" + lebar + \\", Tinggi: \\" + tinggi);", "volume = panjang * lebar * tinggi;", "System.out.println(\\"Volume setiap box nasi sebesar: \\" + volume);"]',
                'output'     => 'Panjang: 20, Lebar: 20, Tinggi: 7
Volume setiap box nasi sebesar: 2800',
                'created_at' => '2025-09-20 03:38:35',
                'updated_at' => '2025-09-22 07:16:45',
                'deleted_at' => null,
            ],
            [
                'id'         => '0199653d-66d4-7335-bfdc-34bd3f31837d',
                'id_level'   => '01995dec-678e-70cf-854a-b25e2c2d0d28',
                'id_soal'    => '01995e27-3c16-7365-be30-b2fed20f53a2',
                'jawaban'    => '["int r = 42;", "float phi = 3.14f;", "double taman_bunga;", "System.out.println(\\"Jari-jari: \\" + r + \\", phi: \\" + phi);", "taman_bunga = 0.5 * (phi * r * r);", "System.out.println(\\"Luas dari taman bunga sebesar: \\" + taman_bunga);"]',
                'output'     => 'Jari-jari: 42, phi: 3.14
Luas dari taman bunga sebesar: 2769.47998046875',
                'created_at' => '2025-09-20 03:49:06',
                'updated_at' => '2025-10-02 05:31:18',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996543-dbbf-73ec-b0d6-ff48905b627a',
                'id_level'   => '01995e0c-9825-73b3-b94f-2ae0542eabef',
                'id_soal'    => '01995e2b-056e-716f-a01a-2794060829e0',
                'jawaban'    => '["int age;", "age = 17;", "if(age >= 16) {", "System.out.println(\\"Anda dapat melanjutkan tes pembuatan SIM\\");", "} else {", "System.out.println(\\"Anda tidak dapat melanjutkan tes pembuatan SIM\\");", "}"]',
                'output'     => 'Anda dapat melanjutkan tes pembuatan SIM',
                'created_at' => '2025-09-20 03:56:10',
                'updated_at' => '2025-10-02 06:13:39',
                'deleted_at' => null,
            ],
            [
                'id'         => '019966b3-d61f-733a-be06-733c496cdacc',
                'id_level'   => '01995e0c-9825-73b3-b94f-2ae0542eabef',
                'id_soal'    => '0199656f-eb1f-70c7-b952-1825aa45f75c',
                'jawaban'    => '["float ipk;", "int toefl;", "int attitude;", "ipk = 3.5;", "toefl = 480;", "attitude = 75;", "if (ipk >= 3.5 && toefl >= 450) {", "if (attitude >= 60 && attitude <= 100) {", "System.out.print(\\"Pelamar dinyatakan lolos seleksi administrasi\\");", "} else {", "System.out.print(\\"Pelamar dinyatakan tidak lolos seleksi administrasi\\");", "}", "} else if (ipk >= 3.4 && toefl >= 400 && attitude >= 80 && attitude <= 100) {", "System.out.print(\\"Pelamar dinyatakan lolos bersyarat seleksi administrasi\\");", "} else {", "System.out.print(\\"Pelamar dinyatakan tidak lolos seleksi administrasi\\");", "}"]',
                'output'     => 'Pelamar dinyatakan lolos seleksi administrasi',
                'created_at' => '2025-09-20 10:38:05',
                'updated_at' => '2025-10-02 06:12:00',
                'deleted_at' => null,
            ],
            [
                'id'         => '019966d2-47f3-70f0-9024-24da44ca3981',
                'id_level'   => '01995e0c-9825-73b3-b94f-2ae0542eabef',
                'id_soal'    => '0199657d-34ff-739e-bfef-e842ea2e57d3',
                'jawaban'    => '["String member_card;", "int tot_belanja;", "float diskon;", "int bayar;", "tot_belanja = 300000;", "member_card = \\"ya\\";", "if (member_card.equalsIgnoreCase(\\"ya\\")) {", "if (tot_belanja > 500000) {", "System.out.println(\\"diskon 10%\\");", "} else if (tot_belanja >= 251000 && tot_belanja <= 500000) {", "System.out.println(\\"diskon 5%\\");", "} else if (tot_belanja >= 150000 && tot_belanja <= 250000) {", "System.out.println(\\"diskon 2%\\");", "} else {", "System.out.println(\\"diskon 0%\\");", "}", "}"]',
                'output'     => 'diskon 5%',
                'created_at' => '2025-09-20 11:11:21',
                'updated_at' => '2025-10-02 05:56:42',
                'deleted_at' => null,
            ],
            [
                'id'         => '019966e6-b2f8-724f-96d8-d179a6452519',
                'id_level'   => '01995e0c-9825-73b3-b94f-2ae0542eabef',
                'id_soal'    => '01996581-d9fc-7116-ac8c-a860cd1b79bd',
                'jawaban'    => '["int age;", "age = 18;", "if (age >= 13 && age <= 16) {", "System.out.println(\\"Anda hanya dapat menonton film dengan label Semua Umur (SU)\\");", "} else if (age >= 17 && age <= 20) {", "System.out.println(\\"Anda dapat menonton film dengan label Semua Umur (SU) dan 17+\\");", "} else if (age >= 21) {", "System.out.println(\\"Anda dapat menonton film dengan label semua jenis film\\");", "} else {", "System.out.println(\\"Anda tidak memenuhi kriteria untuk menonton film\\");", "}"]',
                'output'     => 'Anda dapat menonton film dengan label Semua Umur (SU) dan 17+',
                'created_at' => '2025-09-20 11:33:39',
                'updated_at' => '2025-10-02 05:49:41',
                'deleted_at' => null,
            ],
            [
                'id'         => '019966ee-b434-7202-b50f-08820a2db7c3',
                'id_level'   => '01995e0c-9825-73b3-b94f-2ae0542eabef',
                'id_soal'    => '01996586-e110-71b7-9507-6b306ef78d5e',
                'jawaban'    => '["int a, b, c;", "a = 10;", "b = 20;", "c = 15;", "if (a > b && a > c) {", "System.out.println(\\"angka a = \\" + a + \\" lebih besar\\");", "} else if (b > c) {", "System.out.println(\\"angka b = \\" + b + \\" lebih besar\\");", "} else {", "System.out.println(\\"angka c = \\" + c + \\" lebih besar\\");", "}"]',
                'output'     => 'angka b = 20 lebih besar',
                'created_at' => '2025-09-20 11:42:23',
                'updated_at' => '2025-10-02 05:46:50',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996701-587b-716d-8987-f42601e1a3e5',
                'id_level'   => '01985f44-f662-72f9-a85b-a7b256942492',
                'id_soal'    => '019965b3-5dc1-733a-96f3-167263ed3ac1',
                'jawaban'    => '["double[] stackSetoran = new double[100];", "double setoran;", "int top;", "String jawaban;", "setoran = 3500000;", "top = -1;", "if (setoran > 3000000) {", "System.out.println(\\"Ingin menambah setoran? (ya/tidak)\\");", "jawaban = \\"ya\\";", "if (jawaban.equalsIgnoreCase(\\"ya\\")) {", "stackSetoran[++top] = setoran;", "}", "}", "System.out.println(\\"Jumlah setoran: \\" + setoran);"]',
                'output'     => 'Ingin menambah setoran? (ya/tidak)
Jumlah setoran: 3500000.0',
                'created_at' => '2025-09-20 12:02:45',
                'updated_at' => '2025-10-03 12:14:33',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996704-a269-7143-91cb-b94885954cf0',
                'id_level'   => '01985f44-f662-72f9-a85b-a7b256942492',
                'id_soal'    => '01996594-b7ae-7084-a4b6-fef776832975',
                'jawaban'    => '["char[] stackPiring = new char[100];", "char piring;", "int top;", "piring = \'A\';", "top = -1;", "stackPiring[++top] = piring;", "piring = stackPiring[top--];", "System.out.println(\\"Piring siap dipakai: \\" + piring);"]',
                'output'     => 'Piring siap dipakai: A',
                'created_at' => '2025-09-20 12:06:21',
                'updated_at' => '2025-10-03 14:12:37',
                'deleted_at' => null,
            ],
            [
                'id'         => '0199670c-056e-727c-a34c-557719e99338',
                'id_level'   => '01985f44-f662-72f9-a85b-a7b256942492',
                'id_soal'    => '019965a5-6b5d-7016-88a8-1c825dcd3a97',
                'jawaban'    => '["String[] stackBarang = new String[100];", "float berat;", "String namaBarang;", "String jawaban;", "int top;", "berat = 35;", "top = -1;", "if (berat > 30) {", "System.out.println(\\"Ada barang tambahan? (ya/tidak)\\");", "jawaban = \\"ya\\";", "if (jawaban.equalsIgnoreCase(\\"ya\\")) {", "namaBarang = \\"Meja\\";", "stackBarang[++top] = namaBarang;", "}", "}", "System.out.println(\\"Berat barang: \\" + berat);"]',
                'output'     => 'Ada barang tambahan? (ya/tidak)
Berat barang: 35.0',
                'created_at' => '2025-09-20 12:14:25',
                'updated_at' => '2025-10-03 14:10:19',
                'deleted_at' => null,
            ],
            [
                'id'         => '0199670f-ce53-71d6-93a3-0d9f17529207',
                'id_level'   => '01985f44-f662-72f9-a85b-a7b256942492',
                'id_soal'    => '019965aa-adc2-73b8-b977-79ce500534db',
                'jawaban'    => '["String[] stackMakanan = new String[100];", "String makanan;", "int jumlahMakanan;", "int top;", "makanan = \\"Nasi Goreng\\";", "jumlahMakanan = 6;", "top = -1;", "if (jumlahMakanan > 5) {", "stackMakanan[++top] = makanan;", "System.out.println(\\"Makanan yang dihidangkan: \\" + stackMakanan[top--]);", "} else {", "stackMakanan[++top] = makanan;", "}"]',
                'output'     => 'Makanan yang dihidangkan: Nasi Goreng',
                'created_at' => '2025-09-20 12:18:33',
                'updated_at' => '2025-10-03 13:51:39',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996724-a8fc-72d5-9780-8d3002c46060',
                'id_level'   => '01985f44-f662-72f9-a85b-a7b256942492',
                'id_soal'    => '0199671f-15f6-7101-a4c4-2f9227cb03e2',
                'jawaban'    => '["String[] rakDokumen = new String[100]; int top = -1;", "String dokumen;", "dokumen = \\"Surat_Keuangan.pdf\\";", "if (top >= 0) {", "String dokumenKeluar = rakDokumen[top--];", "System.out.println(\\"Dokumen yang dikeluarkan: \\" + dokumenKeluar);", "System.out.print(\\"Sisa dokumen di rak: \\"); for (int i=0;i<=top;i++) System.out.print(rakDokumen[i]+\\" \\");", "} else {", "System.out.println(\\"Rak kosong, tidak ada dokumen yang bisa dikeluarkan\\");", "}"]',
                'output'     => 'Rak kosong, tidak ada dokumen yang bisa dikeluarkan',
                'created_at' => '2025-09-20 12:41:19',
                'updated_at' => '2025-10-03 11:43:39',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996747-6c58-73fb-8924-a720f71da756',
                'id_level'   => '01995e0d-85fe-7041-8c7e-cdfbc81bf1ed',
                'id_soal'    => '01996744-c306-7298-beec-4f93e45cfa06',
                'jawaban'    => '["int bilangan;", "int kelipatan;", "bilangan = 10;", "kelipatan = 2;", "for (int i = 1; i <= bilangan; i += kelipatan) {", "for (int j = 0; j < i; j++) System.out.print(\\"*\\"); System.out.println();", "}"]',
                'output'     => '*
***
*****
*******
*********',
                'created_at' => '2025-09-20 13:19:18',
                'updated_at' => '2025-10-02 06:55:57',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996a97-0b6a-7046-a8ef-5b29c3be2014',
                'id_level'   => '01995e0d-85fe-7041-8c7e-cdfbc81bf1ed',
                'id_soal'    => '0199674b-b207-71ea-b22e-cf9973f75db4',
                'jawaban'    => '["int nomor;", "int faktorial;", "nomor = 5;", "faktorial = 1;", "for (int i = 1; i <= nomor; i++) {", "faktorial *= i;", "}", "System.out.println(\\"Faktorial dari \\" + nomor + \\" adalah \\" + faktorial);"]',
                'output'     => 'Faktorial dari 5 adalah 120',
                'created_at' => '2025-09-21 04:45:07',
                'updated_at' => '2025-10-02 06:54:39',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996a99-5160-70fe-a8cd-72263c141e38',
                'id_level'   => '01995e0d-85fe-7041-8c7e-cdfbc81bf1ed',
                'id_soal'    => '0199674e-e634-718b-beaf-d8f0d57de3f5',
                'jawaban'    => '["String kata;", "kata = \\"Politeknik Negeri Malang\\";", "for (int i = 0; i < 5; i++) {", "System.out.println(kata);", "}"]',
                'output'     => 'Politeknik Negeri Malang
Politeknik Negeri Malang
Politeknik Negeri Malang
Politeknik Negeri Malang
Politeknik Negeri Malang',
                'created_at' => '2025-09-21 04:47:36',
                'updated_at' => '2025-10-02 06:53:42',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996a9f-8855-7201-b109-d7c5eea4128a',
                'id_level'   => '01995e0d-85fe-7041-8c7e-cdfbc81bf1ed',
                'id_soal'    => '01996753-90ea-7040-bb5c-7cd7ab5e6078',
                'jawaban'    => '["int n;", "int fib0, fib1, fib2;", "n = 50;", "fib0 = 0;", "fib1 = 1;", "System.out.print(fib0 + \\" \\" + fib1);", "while (true) {", "fib2 = fib0 + fib1;", "if (fib2 >= n) break;", "System.out.print(\\" \\" + fib2);", "fib0 = fib1;", "fib1 = fib2;", "}"]',
                'output'     => '0 1 1 2 3 5 8 13 21 34',
                'created_at' => '2025-09-21 04:54:24',
                'updated_at' => '2025-10-02 06:50:01',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996aa5-6f72-735e-90c2-3b5942c44435',
                'id_level'   => '01995e0d-85fe-7041-8c7e-cdfbc81bf1ed',
                'id_soal'    => '01996757-a7b5-72e7-ae2c-c91194f3357d',
                'jawaban'    => '["int[] deret = new int[8];", "int jumlahDeret = 0;", "deret[0] = 1;", "for (int i = 1; i < 8; i++) {", "deret[i] = deret[i-1] * 2;", "jumlahDeret += deret[i];", "}", "for (int i = 0; i < 8; i++) System.out.print(deret[i] + \\" \\");", "System.out.println(\\"\\\\nJumlah seluruh elemen dalam deret: \\" + jumlahDeret);"]',
                'output'     => '1 2 4 8 16 32 64 128
Jumlah seluruh elemen dalam deret: 254',
                'created_at' => '2025-09-21 05:00:50',
                'updated_at' => '2025-10-02 06:22:24',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996aaa-63bd-7318-8b26-d7abf296e78e',
                'id_level'   => '01995e0d-85fe-7041-8c7e-cdfbc81bf1ed',
                'id_soal'    => '0199675d-4c17-7287-8ac8-1fae60417c7e',
                'jawaban'    => '["int n;", "char c;", "n = 51;", "for (int i = 0; i <= n; i++) {", "System.out.println(\\"Nilai \\" + i + \\" memiliki keluaran karakter : \\" + (char)i);", "if (Character.isLowerCase((char)i)) {", "System.out.println(\\"Huruf kecil\\");", "}", "}"]',
                'output'     => 'Nilai 0 memiliki keluaran karakter :
Nilai 48 memiliki keluaran karakter : 0
Nilai 49 memiliki keluaran karakter : 1
Nilai 50 memiliki keluaran karakter : 2
Nilai 51 memiliki keluaran karakter : 3',
                'created_at' => '2025-09-21 05:06:15',
                'updated_at' => '2025-10-02 06:20:05',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996aad-b832-7227-8990-58efa5105310',
                'id_level'   => '01995e0d-85fe-7041-8c7e-cdfbc81bf1ed',
                'id_soal'    => '01996760-3a58-7095-bfb0-d05466e817c5',
                'jawaban'    => '["int jumlah_bilangan_asli;", "int ui;", "jumlah_bilangan_asli = 5;", "for (int i = 1; i <= jumlah_bilangan_asli; i++) {", "ui = 25 * i;", "System.out.println(\\"U\\" + i + \\" = 25 * \\" + i + \\" = \\" + ui);", "}"]',
                'output'     => 'U1 = 25 * 1 = 25
U2 = 25 * 2 = 50
U3 = 25 * 3 = 75
U4 = 25 * 4 = 100
U5 = 25 * 5 = 125',
                'created_at' => '2025-09-21 05:09:53',
                'updated_at' => '2025-10-02 06:15:18',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996abc-979d-7187-90fa-068f0f7623c0',
                'id_level'   => '01995e12-4580-7361-b0d1-379bdea0b2b6',
                'id_soal'    => '01996765-71ea-7079-8d2d-573093f98e7c',
                'jawaban'    => '["String warna;", "String tindakan;", "warna = \\"merah\\";", "switch (warna) {", "case \\"merah\\":", "tindakan = \\"berhenti\\";", "break;", "case \\"kuning\\":", "tindakan = \\"hati-hati\\";", "break;", "case \\"hijau\\":", "tindakan = \\"jalan\\";", "break;", "default:", "tindakan = \\"warna yang anda inputkan salah\\";", "break;", "}", "System.out.println(\\"Tindakan : \\" +tindakan);"]',
                'output'     => 'Tindakan : berhenti',
                'created_at' => '2025-09-21 05:26:08',
                'updated_at' => '2025-10-02 07:05:20',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996ad8-b320-7381-876b-aa1a114fd72e',
                'id_level'   => '01985f44-f662-72f9-a85b-a7b256942492',
                'id_soal'    => '01996acc-ed8f-7078-84c5-e827079e6360',
                'jawaban'    => '["int desimal;", "int sisa;", "int[] stackBiner = new int[32];", "int bit;", "int top;", "desimal = 60;", "stackBiner = new int[32];", "top = -1;", "while (desimal > 0) {", "sisa = desimal % 2;", "stackBiner[++top] = sisa;", "desimal = desimal / 2;", "}", "while (top >= 0) {", "bit = stackBiner[top--];", "System.out.print(bit);", "}"]',
                'output'     => '111100',
                'created_at' => '2025-09-21 05:56:50',
                'updated_at' => '2025-10-03 11:32:14',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996b0f-8f47-705a-9242-fc0f5c0c9b73',
                'id_level'   => '01995e13-29af-7010-8995-1a40e4504851',
                'id_soal'    => '01996ac8-52d1-7171-9a2e-5455ae83c2b1',
                'jawaban'    => '["int[] nilai;", "int total_sum;", "nilai = new int[]{20,5,25,8,3};", "for (int i = 0; i < nilai.length; i++) {", "System.out.println(\\"Elemen ke-\\" + i + \\": \\" + nilai[i]);", "}", "total_sum = 0;", "for (int num : nilai) {", "total_sum += num;", "}", "System.out.println(\\"Jumlah semua elemen pada array adalah: \\" + total_sum);"]',
                'output'     => 'Elemen ke-0: 20
Elemen ke-1: 5
Elemen ke-2: 25
Elemen ke-3: 8
Elemen ke-4: 3
Jumlah semua elemen pada array adalah: 61',
                'created_at' => '2025-09-21 06:56:45',
                'updated_at' => '2025-10-03 11:04:45',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996b11-98ee-7029-b142-4cf33b01572f',
                'id_level'   => '01995e13-29af-7010-8995-1a40e4504851',
                'id_soal'    => '01996adc-1108-70fc-b2c8-3a9836158a03',
                'jawaban'    => '["String[] artis;", "artis = new String[]{\\"Suzy\\", \\"Song Hye Kyo\\", \\"Lee Minho\\", \\"Yoona\\", \\"Junho\\"};", "for (int i = 0; i < artis.length; i++) {", "System.out.println(\\"Indeks \\" + i + \\": \\" + artis[i]);", "}"]',
                'output'     => 'Indeks 0: Suzy
Indeks 1: Song Hye Kyo
Indeks 2: Lee Minho
Indeks 3: Yoona
Indeks 4: Junho',
                'created_at' => '2025-09-21 06:58:59',
                'updated_at' => '2025-10-03 11:03:57',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996b17-97c2-73c3-a534-81fe0339d9b7',
                'id_level'   => '01995e13-29af-7010-8995-1a40e4504851',
                'id_soal'    => '01996adf-b46d-73b2-8cdc-434273bd18c8',
                'jawaban'    => '["int[] array;", "int elemen;", "array = new int[]{1, 3, 5, 7, 9, 11, 13};", "for (int i = 0; i < array.length; i++) {", "elemen = array[i];", "System.out.print(elemen);", "}"]',
                'output'     => '135791113',
                'created_at' => '2025-09-21 07:05:32',
                'updated_at' => '2025-10-03 11:02:54',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996b1d-e0cf-7010-ae19-8acf31408870',
                'id_level'   => '01995e13-29af-7010-8995-1a40e4504851',
                'id_soal'    => '01996ae3-7692-722e-b457-fff0ed70eecf',
                'jawaban'    => '["int[] bilangan;", "int jumlah_bilangan;", "int total_nilai;", "float rata_rata;", "bilangan = new int[]{10, 11, 12, 13, 14, 15};", "jumlah_bilangan = bilangan.length;", "total_nilai = 0;", "for (int n : bilangan) {", "total_nilai += n;", "}", "rata_rata = (double) total_nilai / jumlah_bilangan;", "System.out.println(\\"Bilangan:\\");", "for (int n : bilangan) {", "System.out.print(n + \\" \\");", "}", "System.out.println(\\"Jumlah bilangan: \\" + jumlah_bilangan);", "System.out.println(\\"Total nilai: \\" + total_nilai);", "System.out.println(\\"Rata-rata: \\" + rata_rata);"]',
                'output'     => 'Bilangan:
10 11 12 13 14 15 Jumlah bilangan: 6
Total nilai: 75
Rata-rata: 12.5',
                'created_at' => '2025-09-21 07:12:24',
                'updated_at' => '2025-10-03 10:48:19',
                'deleted_at' => null,
            ],
            [
                'id'         => '01996b22-3878-7083-bc02-833d42743a3e',
                'id_level'   => '01995e13-29af-7010-8995-1a40e4504851',
                'id_soal'    => '01996ae7-0dd8-723c-b978-0f3bae73aaa1',
                'jawaban'    => '["int[] bilangan;", "int nilai_max;", "int nilai_min;", "bilangan = new int[]{1,2,3,4,5,6,7,8,9,10};", "nilai_max = bilangan[0];", "nilai_min = bilangan[0];", "for(int i = 1; i < bilangan.length; i++) {", "if(bilangan[i] > nilai_max) nilai_max = bilangan[i];", "if(bilangan[i] < nilai_min) nilai_min = bilangan[i];", "}", "System.out.println(\\"Bilangan: \\" + java.util.Arrays.toString(bilangan));", "System.out.println(\\"Nilai maksimum: \\" + nilai_max);", "System.out.println(\\"Nilai minimum: \\" + nilai_min);"]',
                'output'     => 'Bilangan: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
Nilai maksimum: 10
Nilai minimum: 1',
                'created_at' => '2025-09-21 07:17:08',
                'updated_at' => '2025-10-03 02:16:41',
                'deleted_at' => null,
            ],
        ];

        DB::table('bank_soal_konversi')->insert($data);
    }
}
