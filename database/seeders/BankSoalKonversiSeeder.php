<?php

namespace Database\Seeders;

use App\Models\BankSoalKonversi;
use App\Models\Level;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class BankSoalKonversiSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure levels exist — find by ID, or create with a placeholder name
        $levelQueue = Level::firstOrCreate(
            // ['id' => '019863c4-59f9-7319-9104-08267fc3c551'],
            ['name' => 'Queue (Array-Based Manual)', 'order' => 11]
        )->id;

        $levelLinkedList = Level::firstOrCreate(
            // ['id' => '019de356-abfa-717d-958c-e9311c2712f3'],
            ['name' => 'Linked List', 'order' => 12]
        )->id;

        // Resolve Soal ID — find by judul, or create a placeholder with that judul
        // Uses updateOrCreate so existing Soal records get their id_level/difficulty refreshed
        $resolveSoal = function (string $judul, string $levelId, string $difficulty = 'easy') {
            return Soal::updateOrCreate(
                ['judul' => $judul],
                [
                    'id_level'   => $levelId,
                    'soal'       => $judul,
                    'order'      => 0,
                    'difficulty' => $difficulty,
                ]
            )->id;
        };

        $entries = [
            // ============================================================
            // BAGIAN 1: QUEUE (Array-Based Manual)
            // ============================================================
            [
                'level'      => $levelQueue,
                'judul'      => 'Antrian Loket Karcis Bioskop',
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
                'difficulty' => 'easy',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Antrian Pasien Klinik',
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
                'difficulty' => 'easy',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Antrian Pengambilan Obat',
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
                'difficulty' => 'easy',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Loket Bank Belum Buka',
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
                'difficulty' => 'easy',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Antrian Wahana Taman Bermain',
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
                'difficulty' => 'easy',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Memanggil Pasien Pertama di Puskesmas',
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
                'difficulty' => 'medium',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Melayani Seluruh Antrian Kasir Supermarket',
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
                'difficulty' => 'medium',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Antrian Pendaftaran Lomba Bergantian',
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
                'difficulty' => 'medium',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Membalik Urutan Antrian Peserta Ujian',
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
                'difficulty' => 'medium',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Cek Palindrom Plat Nomor Kendaraan',
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
                'difficulty' => 'medium',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Pencarian Nomor Antrian di Rumah Sakit',
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
                'difficulty' => 'hard',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Mencari Stok Minimum di Gudang',
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
                'difficulty' => 'hard',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Menghitung Frekuensi Kehadiran Siswa',
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
                'difficulty' => 'hard',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Cari Nomor Paket lalu Balik Antrian Pengiriman',
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
                'difficulty' => 'hard',
            ],
            [
                'level'      => $levelQueue,
                'judul'      => 'Mencari Skor Tertinggi di Antrian Turnamen',
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
                'difficulty' => 'hard',
            ],

            // ============================================================
            // BAGIAN 2: LINKED LIST
            // ============================================================
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Menyambung Tiga Gerbong Kereta',
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
                'difficulty' => 'easy',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Menghitung Total Gerbong Kereta',
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
                'difficulty' => 'easy',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Menambah Gerbong di Paling Depan',
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
                'difficulty' => 'easy',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Navigasi Playlist Lagu Maju dan Mundur',
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
                'difficulty' => 'easy',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Menampilkan Seluruh Daftar Pesanan Makanan',
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
                'difficulty' => 'easy',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Mencari Buku di Rak Perpustakaan',
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
                'difficulty' => 'medium',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Menghapus Pasien Pertama dari Antrian',
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
                'difficulty' => 'medium',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Melepas Gerbong Terakhir Kereta',
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
                'difficulty' => 'medium',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Menyisipkan Peserta di Tengah Barisan',
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
                'difficulty' => 'medium',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Menghitung Total Belanjaan di Kasir',
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
                'difficulty' => 'medium',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Mencari Skor Peserta Tertinggi',
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
                'difficulty' => 'hard',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Membalik Urutan Antrian Pemain',
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
                'difficulty' => 'hard',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Menyisipkan Data di Tengah Double Linked List',
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
                'difficulty' => 'hard',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Mencabut Berkas Rusak di Tengah Urutan',
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
                'difficulty' => 'hard',
            ],
            [
                'level'      => $levelLinkedList,
                'judul'      => 'Memeriksa Struktur Palindrom Sederhana',
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
                'difficulty' => 'hard',
            ],
        ];

        foreach ($entries as $entry) {
            BankSoalKonversi::factory()->create([
                'id_level'   => $entry['level'],
                'id_soal'    => $resolveSoal($entry['judul'], $entry['level'], $entry['difficulty']),
                'jawaban'    => $entry['jawaban'],
                'output'     => $entry['output'],
                'difficulty' => $entry['difficulty'],
            ]);
        }
    }
}
