<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSoalKonversiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ============================================================
            // BAGIAN 1: QUEUE (Array-Based Manual)
            // ============================================================
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Antrian Loket Karcis Bioskop' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static String[] q = new String[10];\n" .
                                "    static int f = 0, r = 0, s = 0;\n" .
                                "    static void enqueue(String d) { q[r++] = d; s++; }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(\"Rina\");\n" .
                                "        enqueue(\"Doni\");\n" .
                                "        enqueue(\"Yudi\");\n" .
                                "        System.out.println(q[f]);\n" .
                                "        System.out.println(s);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Rina\n3",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Antrian Pasien Klinik' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] q = new int[10];\n" .
                                "    static int f = 0, r = 0, s = 0;\n" .
                                "    static void enqueue(int d) { q[r++] = d; s++; }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(101);\n" .
                                "        enqueue(102);\n" .
                                "        enqueue(103);\n" .
                                "        System.out.println(\"FRONT : \" + q[f]);\n" .
                                "        System.out.println(\"REAR  : \" + q[r - 1]);\n" .
                                "        System.out.println(\"SIZE  : \" + s);\n" .
                                "    }\n" .
                                "}",
                'output'     => "FRONT : 101\nREAR  : 103\nSIZE  : 3",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Antrian Pengambilan Obat' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static String[] q = new String[10];\n" .
                                "    static int f = 0, r = 0, s = 0;\n" .
                                "    static void enqueue(String d) { q[r++] = d; s++; }\n" .
                                "    static String isi() {\n" .
                                "        String t = \"[\";\n" .
                                "        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += \", \"; }\n" .
                                "        return t + \"]\";\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        for (int i = 0; i < 3; i++) {\n" .
                                "            enqueue(sc.nextLine());\n" .
                                "            System.out.println(\"Antrian: \" + isi() + \" Ukuran: \" + s);\n" .
                                "        }\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Antrian: [Siti] Ukuran: 1\nAntrian: [Siti, Bagas] Ukuran: 2\nAntrian: [Siti, Bagas, Citra] Ukuran: 3",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Loket Bank Belum Buka' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] q = new int[10];\n" .
                                "    static int f = 0, r = 0, s = 0;\n" .
                                "    static void enqueue(int d) { q[r++] = d; s++; }\n" .
                                "    static boolean isEmpty() { return s == 0; }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        System.out.println(isEmpty());\n" .
                                "        enqueue(201);\n" .
                                "        enqueue(202);\n" .
                                "        System.out.println(isEmpty());\n" .
                                "        System.out.println(s);\n" .
                                "        System.out.println(q[f]);\n" .
                                "    }\n" .
                                "}",
                'output'     => "true\nfalse\n2\n201",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Antrian Wahana Taman Bermain' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static int[] q = new int[10];\n" .
                                "    static int f = 0, r = 0, s = 0;\n" .
                                "    static void enqueue(int d) { q[r++] = d; s++; }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        for (int i = 0; i < 3; i++) enqueue(sc.nextInt());\n" .
                                "        System.out.println(\"FRONT  : \" + q[f]);\n" .
                                "        System.out.println(\"REAR   : \" + q[r - 1]);\n" .
                                "        System.out.println(\"SIZE   : \" + s);\n" .
                                "        System.out.println(\"ISEMPTY: \" + (s == 0));\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "FRONT  : 7\nREAR   : 9\nSIZE   : 3\nISEMPTY: false",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Memanggil Pasien Pertama di Puskesmas' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static String[] q = new String[10];\n" .
                                "    static int f = 0, r = 0, s = 0;\n" .
                                "    static void enqueue(String d) { q[r++] = d; s++; }\n" .
                                "    static String dequeue() { s--; return q[f++]; }\n" .
                                "    static String isi() {\n" .
                                "        String t = \"[\";\n" .
                                "        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += \", \"; }\n" .
                                "        return t + \"]\";\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(\"Hendra\"); enqueue(\"Lestari\"); enqueue(\"Miko\");\n" .
                                "        String dipanggil = dequeue();\n" .
                                "        System.out.println(\"Dipanggil  : \" + dipanggil);\n" .
                                "        System.out.println(\"Sisa       : \" + isi());\n" .
                                "        System.out.println(\"FRONT baru : \" + q[f]);\n" .
                                "        System.out.println(\"SIZE baru  : \" + s);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Dipanggil  : Hendra\nSisa       : [Lestari, Miko]\nFRONT baru : Lestari\nSIZE baru  : 2",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Melayani Seluruh Antrian Kasir Supermarket' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] q = new int[10];\n" .
                                "    static int f = 0, r = 0, s = 0;\n" .
                                "    static void enqueue(int d) { q[r++] = d; s++; }\n" .
                                "    static int dequeue() { s--; return q[f++]; }\n" .
                                "    static boolean isEmpty() { return s == 0; }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        for (int i = 1; i <= 5; i++) enqueue(i);\n" .
                                "        while (!isEmpty()) {\n" .
                                "            int pembeli = dequeue();\n" .
                                "            System.out.println(\"Dilayani: \" + pembeli + \" Sisa: \" + s);\n" .
                                "        }\n" .
                                "        System.out.println(\"Antrian telah kosong\");\n" .
                                "    }\n" .
                                "}",
                'output'     => "Dilayani: 1 Sisa: 4\nDilayani: 2 Sisa: 3\nDilayani: 3 Sisa: 2\nDilayani: 4 Sisa: 1\nDilayani: 5 Sisa: 0\nAntrian telah kosong",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Antrian Pendaftaran Lomba Bergantian' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static String[] q = new String[10];\n" .
                                "    static int f = 0, r = 0, s = 0;\n" .
                                "    static void enqueue(String d) { q[r++] = d; s++; }\n" .
                                "    static String dequeue() { s--; return q[f++]; }\n" .
                                "    static String isi() {\n" .
                                "        String t = \"[\";\n" .
                                "        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += \", \"; }\n" .
                                "        return t + \"]\";\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        enqueue(sc.nextLine()); enqueue(sc.nextLine());\n" .
                                "        String a = dequeue();\n" .
                                "        enqueue(sc.nextLine());\n" .
                                "        String b = dequeue();\n" .
                                "        enqueue(sc.nextLine());\n" .
                                "        System.out.println(\"a         : \" + a);\n" .
                                "        System.out.println(\"b         : \" + b);\n" .
                                "        System.out.println(\"Isi akhir : \" + isi());\n" .
                                "        System.out.println(\"SIZE      : \" + s);\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "a         : A\nb         : B\nIsi akhir : [C, D]\nSIZE      : 2",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Membalik Urutan Antrian Peserta Ujian' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] q = new int[20]; static int f = 0, r = 0, s = 0;\n" .
                                "    static int[] st = new int[20]; static int top = -1;\n" .
                                "    static void enqueue(int d) { q[r++] = d; s++; }\n" .
                                "    static int dequeue() { s--; return q[f++]; }\n" .
                                "    static void push(int d) { st[++top] = d; }\n" .
                                "    static int pop() { return st[top--]; }\n" .
                                "    static String isi() {\n" .
                                "        String t = \"[\";\n" .
                                "        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += \", \"; }\n" .
                                "        return t + \"]\";\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        for (int i = 1; i <= 5; i++) enqueue(i);\n" .
                                "        System.out.println(\"Sebelum: \" + isi());\n" .
                                "        while (s > 0) push(dequeue());\n" .
                                "        while (top >= 0) enqueue(pop());\n" .
                                "        System.out.println(\"Sesudah: \" + isi());\n" .
                                "    }\n" .
                                "}",
                'output'     => "Sebelum: [1, 2, 3, 4, 5]\nSesudah: [5, 4, 3, 2, 1]",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Cek Palindrom Plat Nomor Kendaraan' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static char[] q = new char[20]; static int f = 0, r = 0, s = 0;\n" .
                                "    static char[] st = new char[20]; static int top = -1;\n" .
                                "    static void enqueue(char d) { q[r++] = d; s++; }\n" .
                                "    static char dequeue() { s--; return q[f++]; }\n" .
                                "    static void push(char d) { st[++top] = d; }\n" .
                                "    static char pop() { return st[top--]; }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        String kata = sc.nextLine();\n" .
                                "        for (char c : kata.toCharArray()) { enqueue(c); push(c); }\n" .
                                "        boolean isPalindrom = true;\n" .
                                "        while (s > 0) if (dequeue() != pop()) isPalindrom = false;\n" .
                                "        System.out.println(\"Palindrom: \" + isPalindrom);\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Palindrom: true",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Pencarian Nomor Antrian di Rumah Sakit' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] q = new int[20]; static int qf = 0, qr = 0, qs = 0;\n" .
                                "    static int[] tmp = new int[20]; static int tf = 0, tr = 0, ts = 0;\n" .
                                "    static void enqQ(int d) { q[qr++] = d; qs++; }\n" .
                                "    static int deqQ() { qs--; return q[qf++]; }\n" .
                                "    static void enqT(int d) { tmp[tr++] = d; ts++; }\n" .
                                "    static int deqT() { ts--; return tmp[tf++]; }\n" .
                                "    static String isi() {\n" .
                                "        String t = \"[\";\n" .
                                "        for (int i = qf; i < qr; i++) { t += q[i]; if (i < qr - 1) t += \", \"; }\n" .
                                "        return t + \"]\";\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        for (int d : new int[]{11, 22, 33, 44, 55}) enqQ(d);\n" .
                                "        int cari = 33; boolean ditemukan = false;\n" .
                                "        while (qs > 0) { int e = deqQ(); if (e == cari) ditemukan = true; enqT(e); }\n" .
                                "        while (ts > 0) enqQ(deqT());\n" .
                                "        System.out.println(\"Ditemukan: \" + ditemukan);\n" .
                                "        System.out.println(\"Antrian  : \" + isi());\n" .
                                "    }\n" .
                                "}",
                'output'     => "Ditemukan: true\nAntrian  : [11, 22, 33, 44, 55]",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Mencari Stok Minimum di Gudang' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static int[] q = new int[20]; static int qf = 0, qr = 0, qs = 0;\n" .
                                "    static int[] tmp = new int[20]; static int tf = 0, tr = 0, ts = 0;\n" .
                                "    static void enqQ(int d) { q[qr++] = d; qs++; }\n" .
                                "    static int deqQ() { qs--; return q[qf++]; }\n" .
                                "    static void enqT(int d) { tmp[tr++] = d; ts++; }\n" .
                                "    static int deqT() { ts--; return tmp[tf++]; }\n" .
                                "    static String isi() {\n" .
                                "        String t = \"[\";\n" .
                                "        for (int i = qf; i < qr; i++) { t += q[i]; if (i < qr - 1) t += \", \"; }\n" .
                                "        return t + \"]\";\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        for (int i = 0; i < 5; i++) enqQ(sc.nextInt());\n" .
                                "        int minimum = q[qf];\n" .
                                "        while (qs > 0) { int e = deqQ(); if (e < minimum) minimum = e; enqT(e); }\n" .
                                "        while (ts > 0) enqQ(deqT());\n" .
                                "        System.out.println(\"Minimum: \" + minimum);\n" .
                                "        System.out.println(\"Antrian: \" + isi());\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Minimum: 10\nAntrian: [50, 20, 80, 10, 60]",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Menghitung Frekuensi Kehadiran Siswa' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static int[] q = new int[20]; static int qf = 0, qr = 0, qs = 0;\n" .
                                "    static int[] tmp = new int[20]; static int tf = 0, tr = 0, ts = 0;\n" .
                                "    static void enqQ(int d) { q[qr++] = d; qs++; }\n" .
                                "    static int deqQ() { qs--; return q[qf++]; }\n" .
                                "    static void enqT(int d) { tmp[tr++] = d; ts++; }\n" .
                                "    static int deqT() { ts--; return tmp[tf++]; }\n" .
                                "    static String isi() {\n" .
                                "        String t = \"[\";\n" .
                                "        for (int i = qf; i < qr; i++) { t += q[i]; if (i < qr - 1) t += \", \"; }\n" .
                                "        return t + \"]\";\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        int cari = sc.nextInt();\n" .
                                "        for (int d : new int[]{2, 5, 2, 3, 2, 5, 4}) enqQ(d);\n" .
                                "        int frekuensi = 0;\n" .
                                "        while (qs > 0) { int e = deqQ(); if (e == cari) frekuensi++; enqT(e); }\n" .
                                "        while (ts > 0) enqQ(deqT());\n" .
                                "        System.out.println(\"Frekuensi \" + cari + \" : \" + frekuensi);\n" .
                                "        System.out.println(\"Antrian     : \" + isi());\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Frekuensi 2 : 3\nAntrian     : [2, 5, 2, 3, 2, 5, 4]",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Cari Nomor Paket lalu Balik Antrian Pengiriman' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] q = new int[20]; static int f = 0, r = 0, s = 0;\n" .
                                "    static int[] st = new int[20]; static int top = -1;\n" .
                                "    static void enqueue(int d) { q[r++] = d; s++; }\n" .
                                "    static int dequeue() { s--; return q[f++]; }\n" .
                                "    static void push(int d) { st[++top] = d; }\n" .
                                "    static int pop() { return st[top--]; }\n" .
                                "    static String isi() {\n" .
                                "        String t = \"[\";\n" .
                                "        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += \", \"; }\n" .
                                "        return t + \"]\";\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        for (int d : new int[]{301, 302, 303, 304, 305}) enqueue(d);\n" .
                                "        int cari = 303; boolean ditemukan = false;\n" .
                                "        while (s > 0) { int e = dequeue(); if (e == cari) ditemukan = true; push(e); }\n" .
                                "        while (top >= 0) enqueue(pop());\n" .
                                "        System.out.println(\"Ditemukan: \" + ditemukan);\n" .
                                "        System.out.println(\"Antrian  : \" + isi());\n" .
                                "    }\n" .
                                "}",
                'output'     => "Ditemukan: true\nAntrian  : [305, 304, 303, 302, 301]",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Mencari Skor Tertinggi di Antrian Turnamen' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static int[] q = new int[20]; static int f = 0, r = 0, s = 0;\n" .
                                "    static int[] st = new int[20]; static int top = -1;\n" .
                                "    static void enqueue(int d) { q[r++] = d; s++; }\n" .
                                "    static int dequeue() { s--; return q[f++]; }\n" .
                                "    static void push(int d) { st[++top] = d; }\n" .
                                "    static int pop() { return st[top--]; }\n" .
                                "    static String isi() {\n" .
                                "        String t = \"[\";\n" .
                                "        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += \", \"; }\n" .
                                "        return t + \"]\";\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        for (int i = 0; i < 5; i++) enqueue(sc.nextInt());\n" .
                                "        int maksimum = q[f], posisi = 1, index = 1;\n" .
                                "        while (s > 0) { int e = dequeue(); if (e > maksimum) { maksimum = e; posisi = index; } push(e); index++; }\n" .
                                "        while (top >= 0) enqueue(pop());\n" .
                                "        System.out.println(\"Maksimum : \" + maksimum);\n" .
                                "        System.out.println(\"Posisi   : \" + posisi);\n" .
                                "        System.out.println(\"Antrian  : \" + isi());\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Maksimum : 95\nPosisi   : 4\nAntrian  : [80, 95, 60, 90, 75]",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'hard',
            ],
            // ============================================================
            // BAGIAN 2: LINKED LIST
            // ============================================================
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Menyambung Tiga Gerbong Kereta' LIMIT 1)"),
                'jawaban'    => "class Node { int d; Node n; Node(int d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node head = new Node(10);\n" .
                                "        head.n = new Node(20);\n" .
                                "        head.n.n = new Node(30);\n" .
                                "        \n" .
                                "        System.out.println(\"Depan: \" + head.d);\n" .
                                "        System.out.println(\"Belakang: \" + head.n.n.d);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Depan: 10\nBelakang: 30",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Menghitung Total Gerbong Kereta' LIMIT 1)"),
                'jawaban'    => "class Node { String d; Node n; Node(String d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(\"A\");\n" .
                                "        h.n = new Node(\"B\");\n" .
                                "        h.n.n = new Node(\"C\");\n" .
                                "        \n" .
                                "        int count = 0;\n" .
                                "        Node tmp = h;\n" .
                                "        while(tmp != null) {\n" .
                                "            count++;\n" .
                                "            tmp = tmp.n;\n" .
                                "        }\n" .
                                "        System.out.println(\"Jumlah: \" + count);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Jumlah: 3",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Menambah Gerbong di Paling Depan' LIMIT 1)"),
                'jawaban'    => "class Node { String d; Node n; Node(String d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(\"B\");\n" .
                                "        h.n = new Node(\"C\");\n" .
                                "        \n" .
                                "        Node baru = new Node(\"A\");\n" .
                                "        baru.n = h;\n" .
                                "        h = baru;\n" .
                                "        \n" .
                                "        while(h != null) {\n" .
                                "            System.out.print(h.d + \" \");\n" .
                                "            h = h.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "A B C ",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Navigasi Playlist Lagu Maju dan Mundur' LIMIT 1)"),
                'jawaban'    => "class DNode { String d; DNode p, n; DNode(String d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        DNode l1 = new DNode(\"Lagu1\");\n" .
                                "        DNode l2 = new DNode(\"Lagu2\");\n" .
                                "        \n" .
                                "        l1.n = l2;\n" .
                                "        l2.p = l1;\n" .
                                "        \n" .
                                "        System.out.println(\"Maju: \" + l1.d + \", \" + l1.n.d);\n" .
                                "        System.out.println(\"Mundur: \" + l2.d + \", \" + l2.p.d);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Maju: Lagu1, Lagu2\nMundur: Lagu2, Lagu1",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Menampilkan Seluruh Daftar Pesanan Makanan' LIMIT 1)"),
                'jawaban'    => "class Node { String d; Node n; Node(String d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(\"Sate\");\n" .
                                "        h.n = new Node(\"Soto\");\n" .
                                "        h.n.n = new Node(\"Bakso\");\n" .
                                "        \n" .
                                "        Node tmp = h;\n" .
                                "        while(tmp != null) {\n" .
                                "            System.out.println(tmp.d);\n" .
                                "            tmp = tmp.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "Sate\nSoto\nBakso",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Mencari Buku di Rak Perpustakaan' LIMIT 1)"),
                'jawaban'    => "class Node { int d; Node n; Node(int d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(101);\n" .
                                "        h.n = new Node(102);\n" .
                                "        h.n.n = new Node(103);\n" .
                                "        \n" .
                                "        int cari = 102;\n" .
                                "        boolean ada = false;\n" .
                                "        Node tmp = h;\n" .
                                "        while(tmp != null) {\n" .
                                "            if(tmp.d == cari) ada = true;\n" .
                                "            tmp = tmp.n;\n" .
                                "        }\n" .
                                "        System.out.println(\"Buku \" + cari + \" Ditemukan: \" + ada);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Buku 102 Ditemukan: true",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Menghapus Pasien Pertama dari Antrian' LIMIT 1)"),
                'jawaban'    => "class Node { int d; Node n; Node(int d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(1);\n" .
                                "        h.n = new Node(2);\n" .
                                "        h.n.n = new Node(3);\n" .
                                "        \n" .
                                "        if(h != null) h = h.n;\n" .
                                "        \n" .
                                "        while(h != null) {\n" .
                                "            System.out.print(h.d + \" \");\n" .
                                "            h = h.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "2 3 ",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Melepas Gerbong Terakhir Kereta' LIMIT 1)"),
                'jawaban'    => "class DNode { int d; DNode p, n; DNode(int d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        DNode n1=new DNode(10), n2=new DNode(20), n3=new DNode(30);\n" .
                                "        n1.n=n2; n2.p=n1; n2.n=n3; n3.p=n2;\n" .
                                "        \n" .
                                "        DNode t = n1;\n" .
                                "        while(t.n != null) t = t.n;\n" .
                                "        if(t.p != null) t.p.n = null; // hapus ekor\n" .
                                "        \n" .
                                "        t = n1;\n" .
                                "        while(t != null) {\n" .
                                "            System.out.print(t.d + \" \");\n" .
                                "            t = t.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "10 20 ",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Menyisipkan Peserta di Tengah Barisan' LIMIT 1)"),
                'jawaban'    => "class Node { String d; Node n; Node(String d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(\"Budi\");\n" .
                                "        h.n = new Node(\"Doni\");\n" .
                                "        \n" .
                                "        Node c = new Node(\"Caca\");\n" .
                                "        c.n = h.n;\n" .
                                "        h.n = c;\n" .
                                "        \n" .
                                "        while(h != null) {\n" .
                                "            System.out.print(h.d + \" \");\n" .
                                "            h = h.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "Budi Caca Doni ",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Menghitung Total Belanjaan di Kasir' LIMIT 1)"),
                'jawaban'    => "class Node { int d; Node n; Node(int d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(5000);\n" .
                                "        h.n = new Node(10000);\n" .
                                "        h.n.n = new Node(15000);\n" .
                                "        \n" .
                                "        int total = 0;\n" .
                                "        Node tmp = h;\n" .
                                "        while(tmp != null) {\n" .
                                "            total += tmp.d;\n" .
                                "            tmp = tmp.n;\n" .
                                "        }\n" .
                                "        System.out.println(\"Total: \" + total);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Total: 30000",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Mencari Skor Peserta Tertinggi' LIMIT 1)"),
                'jawaban'    => "class Node { int d; Node n; Node(int d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(80);\n" .
                                "        h.n = new Node(95);\n" .
                                "        h.n.n = new Node(75);\n" .
                                "        \n" .
                                "        int maksimum = h.d;\n" .
                                "        Node tmp = h.n;\n" .
                                "        while(tmp != null) {\n" .
                                "            if(tmp.d > maksimum) maksimum = tmp.d;\n" .
                                "            tmp = tmp.n;\n" .
                                "        }\n" .
                                "        System.out.println(\"Maksimum: \" + maksimum);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Maksimum: 95",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Membalik Urutan Antrian Pemain' LIMIT 1)"),
                'jawaban'    => "class Node { int d; Node n; Node(int d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(1);\n" .
                                "        h.n = new Node(2);\n" .
                                "        h.n.n = new Node(3);\n" .
                                "        \n" .
                                "        Node prev = null, curr = h, next = null;\n" .
                                "        while(curr != null) {\n" .
                                "            next = curr.n;\n" .
                                "            curr.n = prev;\n" .
                                "            prev = curr;\n" .
                                "            curr = next;\n" .
                                "        }\n" .
                                "        h = prev;\n" .
                                "        \n" .
                                "        while(h != null) { System.out.print(h.d + \" \"); h = h.n; }\n" .
                                "    }\n" .
                                "}",
                'output'     => "3 2 1 ",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Menyisipkan Data di Tengah Double Linked List' LIMIT 1)"),
                'jawaban'    => "class DNode { int d; DNode p, n; DNode(int d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        DNode n1 = new DNode(10), n3 = new DNode(30);\n" .
                                "        n1.n = n3; n3.p = n1;\n" .
                                "        \n" .
                                "        DNode n2 = new DNode(20);\n" .
                                "        n2.n = n1.n; // n2.next ke 30\n" .
                                "        n2.p = n1;   // n2.prev ke 10\n" .
                                "        n1.n.p = n2; // prev dari 30 ke 20\n" .
                                "        n1.n = n2;   // next dari 10 ke 20\n" .
                                "        \n" .
                                "        while(n1 != null) {\n" .
                                "            System.out.print(n1.d + \" \");\n" .
                                "            n1 = n1.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "10 20 30 ",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Mencabut Berkas Rusak di Tengah Urutan' LIMIT 1)"),
                'jawaban'    => "class Node { int d; Node n; Node(int d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(5); h.n = new Node(10); h.n.n = new Node(15);\n" .
                                "        \n" .
                                "        Node tmp = h, prev = null;\n" .
                                "        int hapus = 10;\n" .
                                "        \n" .
                                "        while(tmp != null && tmp.d != hapus) {\n" .
                                "            prev = tmp;\n" .
                                "            tmp = tmp.n;\n" .
                                "        }\n" .
                                "        if(tmp != null && prev != null) prev.n = tmp.n;\n" .
                                "        \n" .
                                "        while(h != null) {\n" .
                                "            System.out.print(h.d + \" \");\n" .
                                "            h = h.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "5 15 ",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Memeriksa Struktur Palindrom Sederhana' LIMIT 1)"),
                'jawaban'    => "class DNode { char d; DNode p, n; DNode(char d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        DNode n1=new DNode('A'), n2=new DNode('B'), n3=new DNode('A');\n" .
                                "        n1.n=n2; n2.p=n1; n2.n=n3; n3.p=n2;\n" .
                                "        \n" .
                                "        DNode head = n1, tail = n3;\n" .
                                "        boolean isPal = true;\n" .
                                "        \n" .
                                "        while(head != tail && head.p != tail) {\n" .
                                "            if(head.d != tail.d) isPal = false;\n" .
                                "            head = head.n;\n" .
                                "            tail = tail.p;\n" .
                                "        }\n" .
                                "        System.out.println(\"Palindrom: \" + isPal);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Palindrom: true",
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'difficulty' => 'hard',
            ],
        ];

        DB::table('bank_soal_konversi')->insert($data);
    }
}
