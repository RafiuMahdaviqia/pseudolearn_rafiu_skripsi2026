<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Soal;
use App\Models\BankSoalKonversi;
use Illuminate\Support\Str;

class PatchDelaSeeder extends Seeder
{
    public function run(): void
    {
        $idLevelQueue = '019863c4-59f9-7319-9104-08267fc3c551'; // ID Level Queue

        $dataKonversi = [
            // ============================================================
            // EASY
            // ============================================================
            [
                'judul_soal' => 'Antrian Loket Karcis Bioskop',
                'difficulty' => 'easy',
                'jawaban' => 'public class Main {
    static String[] q = new String[10];
    static int f = 0, r = 0, s = 0;
    static void enqueue(String d) { q[r++] = d; s++; }
    public static void main(String[] args) {
        enqueue("Rina");
        enqueue("Doni");
        enqueue("Yudi");
        System.out.println(q[f]);
        System.out.println(s);
    }
}',
                'output' => "Rina\n3"
            ],
            [
                'judul_soal' => 'Antrian Pasien Klinik',
                'difficulty' => 'easy',
                'jawaban' => 'public class Main {
    static int[] q = new int[10];
    static int f = 0, r = 0, s = 0;
    static void enqueue(int d) { q[r++] = d; s++; }
    public static void main(String[] args) {
        enqueue(101);
        enqueue(102);
        enqueue(103);
        System.out.println("FRONT : " + q[f]);
        System.out.println("REAR  : " + q[r - 1]);
        System.out.println("SIZE  : " + s);
    }
}',
                'output' => "FRONT : 101\nREAR  : 103\nSIZE  : 3"
            ],
            [
                'judul_soal' => 'Antrian Pengambilan Obat',
                'difficulty' => 'easy',
                'jawaban' => 'import java.util.Scanner;
public class Main {
    static String[] q = new String[10];
    static int f = 0, r = 0, s = 0;
    static void enqueue(String d) { q[r++] = d; s++; }
    static String isi() {
        String t = "[";
        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += ", "; }
        return t + "]";
    }
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        for (int i = 0; i < 3; i++) {
            enqueue(sc.nextLine());
            System.out.println("Antrian: " + isi() + " Ukuran: " + s);
        }
        sc.close();
    }
}',
                'output' => "Antrian: [Siti] Ukuran: 1\nAntrian: [Siti, Bagas] Ukuran: 2\nAntrian: [Siti, Bagas, Citra] Ukuran: 3"
            ],
            [
                'judul_soal' => 'Loket Bank Belum Buka',
                'difficulty' => 'easy',
                'jawaban' => 'public class Main {
    static int[] q = new int[10];
    static int f = 0, r = 0, s = 0;
    static void enqueue(int d) { q[r++] = d; s++; }
    static boolean isEmpty() { return s == 0; }
    public static void main(String[] args) {
        System.out.println(isEmpty());
        enqueue(201);
        enqueue(202);
        System.out.println(isEmpty());
        System.out.println(s);
        System.out.println(q[f]);
    }
}',
                'output' => "true\nfalse\n2\n201"
            ],
            [
                'judul_soal' => 'Antrian Wahana Taman Bermain',
                'difficulty' => 'easy',
                'jawaban' => 'import java.util.Scanner;
public class Main {
    static int[] q = new int[10];
    static int f = 0, r = 0, s = 0;
    static void enqueue(int d) { q[r++] = d; s++; }
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        for (int i = 0; i < 3; i++) enqueue(sc.nextInt());
        System.out.println("FRONT  : " + q[f]);
        System.out.println("REAR   : " + q[r - 1]);
        System.out.println("SIZE   : " + s);
        System.out.println("ISEMPTY: " + (s == 0));
        sc.close();
    }
}',
                'output' => "FRONT  : 7\nREAR   : 9\nSIZE   : 3\nISEMPTY: false"
            ],

            // ============================================================
            // MEDIUM
            // ============================================================
            [
                'judul_soal' => 'Memanggil Pasien Pertama di Puskesmas',
                'difficulty' => 'medium',
                'jawaban' => 'public class Main {
    static String[] q = new String[10];
    static int f = 0, r = 0, s = 0;
    static void enqueue(String d) { q[r++] = d; s++; }
    static String dequeue() { s--; return q[f++]; }
    static String isi() {
        String t = "[";
        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += ", "; }
        return t + "]";
    }
    public static void main(String[] args) {
        enqueue("Hendra"); enqueue("Lestari"); enqueue("Miko");
        String dipanggil = dequeue();
        System.out.println("Dipanggil  : " + dipanggil);
        System.out.println("Sisa       : " + isi());
        System.out.println("FRONT baru : " + q[f]);
        System.out.println("SIZE baru  : " + s);
    }
}',
                'output' => "Dipanggil  : Hendra\nSisa       : [Lestari, Miko]\nFRONT baru : Lestari\nSIZE baru  : 2"
            ],
            [
                'judul_soal' => 'Melayani Seluruh Antrian Kasir Supermarket',
                'difficulty' => 'medium',
                'jawaban' => 'public class Main {
    static int[] q = new int[10];
    static int f = 0, r = 0, s = 0;
    static void enqueue(int d) { q[r++] = d; s++; }
    static int dequeue() { s--; return q[f++]; }
    static boolean isEmpty() { return s == 0; }
    public static void main(String[] args) {
        for (int i = 1; i <= 5; i++) enqueue(i);
        while (!isEmpty()) {
            int pembeli = dequeue();
            System.out.println("Dilayani: " + pembeli + " Sisa: " + s);
        }
        System.out.println("Antrian telah kosong");
    }
}',
                'output' => "Dilayani: 1 Sisa: 4\nDilayani: 2 Sisa: 3\nDilayani: 3 Sisa: 2\nDilayani: 4 Sisa: 1\nDilayani: 5 Sisa: 0\nAntrian telah kosong"
            ],
            [
                'judul_soal' => 'Antrian Pendaftaran Lomba Bergantian',
                'difficulty' => 'medium',
                'jawaban' => 'import java.util.Scanner;
public class Main {
    static String[] q = new String[10];
    static int f = 0, r = 0, s = 0;
    static void enqueue(String d) { q[r++] = d; s++; }
    static String dequeue() { s--; return q[f++]; }
    static String isi() {
        String t = "[";
        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += ", "; }
        return t + "]";
    }
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        enqueue(sc.nextLine()); enqueue(sc.nextLine());
        String a = dequeue();
        enqueue(sc.nextLine());
        String b = dequeue();
        enqueue(sc.nextLine());
        System.out.println("a         : " + a);
        System.out.println("b         : " + b);
        System.out.println("Isi akhir : " + isi());
        System.out.println("SIZE      : " + s);
        sc.close();
    }
}',
                'output' => "a         : A\nb         : B\nIsi akhir : [C, D]\nSIZE      : 2"
            ],
            [
                'judul_soal' => 'Membalik Urutan Antrian Peserta Ujian',
                'difficulty' => 'medium',
                'jawaban' => 'public class Main {
    static int[] q = new int[20]; static int f = 0, r = 0, s = 0;
    static int[] st = new int[20]; static int top = -1;
    static void enqueue(int d) { q[r++] = d; s++; }
    static int dequeue() { s--; return q[f++]; }
    static void push(int d) { st[++top] = d; }
    static int pop() { return st[top--]; }
    static String isi() {
        String t = "[";
        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += ", "; }
        return t + "]";
    }
    public static void main(String[] args) {
        for (int i = 1; i <= 5; i++) enqueue(i);
        System.out.println("Sebelum: " + isi());
        while (s > 0) push(dequeue());
        while (top >= 0) enqueue(pop());
        System.out.println("Sesudah: " + isi());
    }
}',
                'output' => "Sebelum: [1, 2, 3, 4, 5]\nSesudah: [5, 4, 3, 2, 1]"
            ],
            [
                'judul_soal' => 'Cek Palindrom Plat Nomor Kendaraan',
                'difficulty' => 'medium',
                'jawaban' => 'import java.util.Scanner;
public class Main {
    static char[] q = new char[20]; static int f = 0, r = 0, s = 0;
    static char[] st = new char[20]; static int top = -1;
    static void enqueue(char d) { q[r++] = d; s++; }
    static char dequeue() { s--; return q[f++]; }
    static void push(char d) { st[++top] = d; }
    static char pop() { return st[top--]; }
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        String kata = sc.nextLine();
        for (char c : kata.toCharArray()) { enqueue(c); push(c); }
        boolean isPalindrom = true;
        while (s > 0) if (dequeue() != pop()) isPalindrom = false;
        System.out.println("Palindrom: " + isPalindrom);
        sc.close();
    }
}',
                'output' => "Palindrom: true"
            ],

            // ============================================================
            // HARD
            // ============================================================
            [
                'judul_soal' => 'Pencarian Nomor Antrian di Rumah Sakit',
                'difficulty' => 'hard',
                'jawaban' => 'public class Main {
    static int[] q = new int[20]; static int qf = 0, qr = 0, qs = 0;
    static int[] tmp = new int[20]; static int tf = 0, tr = 0, ts = 0;
    static void enqQ(int d) { q[qr++] = d; qs++; }
    static int deqQ() { qs--; return q[qf++]; }
    static void enqT(int d) { tmp[tr++] = d; ts++; }
    static int deqT() { ts--; return tmp[tf++]; }
    static String isi() {
        String t = "[";
        for (int i = qf; i < qr; i++) { t += q[i]; if (i < qr - 1) t += ", "; }
        return t + "]";
    }
    public static void main(String[] args) {
        for (int d : new int[]{11, 22, 33, 44, 55}) enqQ(d);
        int cari = 33; boolean ditemukan = false;
        while (qs > 0) { int e = deqQ(); if (e == cari) ditemukan = true; enqT(e); }
        while (ts > 0) enqQ(deqT());
        System.out.println("Ditemukan: " + ditemukan);
        System.out.println("Antrian  : " + isi());
    }
}',
                'output' => "Ditemukan: true\nAntrian  : [11, 22, 33, 44, 55]"
            ],
            [
                'judul_soal' => 'Mencari Stok Minimum di Gudang',
                'difficulty' => 'hard',
                'jawaban' => 'import java.util.Scanner;
public class Main {
    static int[] q = new int[20]; static int qf = 0, qr = 0, qs = 0;
    static int[] tmp = new int[20]; static int tf = 0, tr = 0, ts = 0;
    static void enqQ(int d) { q[qr++] = d; qs++; }
    static int deqQ() { qs--; return q[qf++]; }
    static void enqT(int d) { tmp[tr++] = d; ts++; }
    static int deqT() { ts--; return tmp[tf++]; }
    static String isi() {
        String t = "[";
        for (int i = qf; i < qr; i++) { t += q[i]; if (i < qr - 1) t += ", "; }
        return t + "]";
    }
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        for (int i = 0; i < 5; i++) enqQ(sc.nextInt());
        int minimum = q[qf];
        while (qs > 0) { int e = deqQ(); if (e < minimum) minimum = e; enqT(e); }
        while (ts > 0) enqQ(deqT());
        System.out.println("Minimum: " + minimum);
        System.out.println("Antrian: " + isi());
        sc.close();
    }
}',
                'output' => "Minimum: 10\nAntrian: [50, 20, 80, 10, 60]"
            ],
            [
                'judul_soal' => 'Menghitung Frekuensi Kehadiran Siswa',
                'difficulty' => 'hard',
                'jawaban' => 'import java.util.Scanner;
public class Main {
    static int[] q = new int[20]; static int qf = 0, qr = 0, qs = 0;
    static int[] tmp = new int[20]; static int tf = 0, tr = 0, ts = 0;
    static void enqQ(int d) { q[qr++] = d; qs++; }
    static int deqQ() { qs--; return q[qf++]; }
    static void enqT(int d) { tmp[tr++] = d; ts++; }
    static int deqT() { ts--; return tmp[tf++]; }
    static String isi() {
        String t = "[";
        for (int i = qf; i < qr; i++) { t += q[i]; if (i < qr - 1) t += ", "; }
        return t + "]";
    }
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        int cari = sc.nextInt();
        for (int d : new int[]{2, 5, 2, 3, 2, 5, 4}) enqQ(d);
        int frekuensi = 0;
        while (qs > 0) { int e = deqQ(); if (e == cari) frekuensi++; enqT(e); }
        while (ts > 0) enqQ(deqT());
        System.out.println("Frekuensi " + cari + " : " + frekuensi);
        System.out.println("Antrian     : " + isi());
        sc.close();
    }
}',
                'output' => "Frekuensi 2 : 3\nAntrian     : [2, 5, 2, 3, 2, 5, 4]"
            ],
            [
                'judul_soal' => 'Cari Nomor Paket lalu Balik Antrian Pengiriman',
                'difficulty' => 'hard',
                'jawaban' => 'public class Main {
    static int[] q = new int[20]; static int f = 0, r = 0, s = 0;
    static int[] st = new int[20]; static int top = -1;
    static void enqueue(int d) { q[r++] = d; s++; }
    static int dequeue() { s--; return q[f++]; }
    static void push(int d) { st[++top] = d; }
    static int pop() { return st[top--]; }
    static String isi() {
        String t = "[";
        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += ", "; }
        return t + "]";
    }
    public static void main(String[] args) {
        for (int d : new int[]{301, 302, 303, 304, 305}) enqueue(d);
        int cari = 303; boolean ditemukan = false;
        while (s > 0) { int e = dequeue(); if (e == cari) ditemukan = true; push(e); }
        while (top >= 0) enqueue(pop());
        System.out.println("Ditemukan: " + ditemukan);
        System.out.println("Antrian  : " + isi());
    }
}',
                'output' => "Ditemukan: true\nAntrian  : [305, 304, 303, 302, 301]"
            ],
            [
                'judul_soal' => 'Mencari Skor Tertinggi di Antrian Turnamen',
                'difficulty' => 'hard',
                'jawaban' => 'import java.util.Scanner;
public class Main {
    static int[] q = new int[20]; static int f = 0, r = 0, s = 0;
    static int[] st = new int[20]; static int top = -1;
    static void enqueue(int d) { q[r++] = d; s++; }
    static int dequeue() { s--; return q[f++]; }
    static void push(int d) { st[++top] = d; }
    static int pop() { return st[top--]; }
    static String isi() {
        String t = "[";
        for (int i = f; i < r; i++) { t += q[i]; if (i < r - 1) t += ", "; }
        return t + "]";
    }
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        for (int i = 0; i < 5; i++) enqueue(sc.nextInt());
        int maksimum = q[f], posisi = 1, index = 1;
        while (s > 0) { int e = dequeue(); if (e > maksimum) { maksimum = e; posisi = index; } push(e); index++; }
        while (top >= 0) enqueue(pop());
        System.out.println("Maksimum : " + maksimum);
        System.out.println("Posisi   : " + posisi);
        System.out.println("Antrian  : " + isi());
        sc.close();
    }
}',
                'output' => "Maksimum : 95\nPosisi   : 4\nAntrian  : [80, 95, 60, 90, 75]"
            ]
        ];

        foreach ($dataKonversi as $data) {
            // Cari id_soal berdasarkan judul
            $soal = Soal::where('judul', $data['judul_soal'])->first();

            if ($soal) {
                BankSoalKonversi::updateOrCreate(
                    [
                        'id_soal' => $soal->id, // Kunci pencarian agar tidak duplikat
                        'id_level' => $idLevelQueue
                    ],
                    [
                        'id' => Str::uuid(), // Buat UUID baru jika insert
                        'difficulty' => $data['difficulty'],
                        'jawaban' => $data['jawaban'],
                        'output' => $data['output'],
                    ]
                );
            } else {
                // Log jika judul soal induk tidak ditemukan (opsional)
                \Log::warning("Soal Induk tidak ditemukan untuk konversi: " . $data['judul_soal']);
            }
        }
    }
}