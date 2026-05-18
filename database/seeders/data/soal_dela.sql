INSERT INTO `bank_soal_konversi`
  (`id`, `id_level`, `id_soal`, `jawaban`, `output`, `created_at`, `updated_at`, `deleted_at`, `difficulty`)
VALUES

-- ============================================================
-- EASY 1 - Antrian Loket Karcis Bioskop
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Antrian Loket Karcis Bioskop' LIMIT 1),
  'public class Main {
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
  'Rina
3',
  NOW(), NOW(), NULL, 'easy'
),

-- ============================================================
-- EASY 2 - Antrian Pasien Klinik
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Antrian Pasien Klinik' LIMIT 1),
  'public class Main {
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
  'FRONT : 101
REAR  : 103
SIZE  : 3',
  NOW(), NOW(), NULL, 'easy'
),

-- ============================================================
-- EASY 3 - Antrian Pengambilan Obat
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Antrian Pengambilan Obat' LIMIT 1),
  'import java.util.Scanner;
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
  'Antrian: [Siti] Ukuran: 1
Antrian: [Siti, Bagas] Ukuran: 2
Antrian: [Siti, Bagas, Citra] Ukuran: 3',
  NOW(), NOW(), NULL, 'easy'
),

-- ============================================================
-- EASY 4 - Loket Bank Belum Buka
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Loket Bank Belum Buka' LIMIT 1),
  'public class Main {
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
  'true
false
2
201',
  NOW(), NOW(), NULL, 'easy'
),

-- ============================================================
-- EASY 5 - Antrian Wahana Taman Bermain
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Antrian Wahana Taman Bermain' LIMIT 1),
  'import java.util.Scanner;
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
  'FRONT  : 7
REAR   : 9
SIZE   : 3
ISEMPTY: false',
  NOW(), NOW(), NULL, 'easy'
),

-- ============================================================
-- MEDIUM 1 - Memanggil Pasien Pertama di Puskesmas
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Memanggil Pasien Pertama di Puskesmas' LIMIT 1),
  'public class Main {
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
  'Dipanggil  : Hendra
Sisa       : [Lestari, Miko]
FRONT baru : Lestari
SIZE baru  : 2',
  NOW(), NOW(), NULL, 'medium'
),

-- ============================================================
-- MEDIUM 2 - Melayani Seluruh Antrian Kasir Supermarket
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Melayani Seluruh Antrian Kasir Supermarket' LIMIT 1),
  'public class Main {
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
  'Dilayani: 1 Sisa: 4
Dilayani: 2 Sisa: 3
Dilayani: 3 Sisa: 2
Dilayani: 4 Sisa: 1
Dilayani: 5 Sisa: 0
Antrian telah kosong',
  NOW(), NOW(), NULL, 'medium'
),

-- ============================================================
-- MEDIUM 3 - Antrian Pendaftaran Lomba Bergantian
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Antrian Pendaftaran Lomba Bergantian' LIMIT 1),
  'import java.util.Scanner;
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
  'a         : A
b         : B
Isi akhir : [C, D]
SIZE      : 2',
  NOW(), NOW(), NULL, 'medium'
),

-- ============================================================
-- MEDIUM 4 - Membalik Urutan Antrian Peserta Ujian
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Membalik Urutan Antrian Peserta Ujian' LIMIT 1),
  'public class Main {
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
  'Sebelum: [1, 2, 3, 4, 5]
Sesudah: [5, 4, 3, 2, 1]',
  NOW(), NOW(), NULL, 'medium'
),

-- ============================================================
-- MEDIUM 5 - Cek Palindrom Plat Nomor Kendaraan
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Cek Palindrom Plat Nomor Kendaraan' LIMIT 1),
  'import java.util.Scanner;
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
  'Palindrom: true',
  NOW(), NOW(), NULL, 'medium'
),

-- ============================================================
-- HARD 1 - Pencarian Nomor Antrian di Rumah Sakit
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Pencarian Nomor Antrian di Rumah Sakit' LIMIT 1),
  'public class Main {
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
  'Ditemukan: true
Antrian  : [11, 22, 33, 44, 55]',
  NOW(), NOW(), NULL, 'hard'
),

-- ============================================================
-- HARD 2 - Mencari Stok Minimum di Gudang
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Mencari Stok Minimum di Gudang' LIMIT 1),
  'import java.util.Scanner;
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
  'Minimum: 10
Antrian: [50, 20, 80, 10, 60]',
  NOW(), NOW(), NULL, 'hard'
),

-- ============================================================
-- HARD 3 - Menghitung Frekuensi Kehadiran Siswa
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Menghitung Frekuensi Kehadiran Siswa' LIMIT 1),
  'import java.util.Scanner;
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
  'Frekuensi 2 : 3
Antrian     : [2, 5, 2, 3, 2, 5, 4]',
  NOW(), NOW(), NULL, 'hard'
),

-- ============================================================
-- HARD 4 - Cari Nomor Paket lalu Balik Antrian Pengiriman
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Cari Nomor Paket lalu Balik Antrian Pengiriman' LIMIT 1),
  'public class Main {
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
  'Ditemukan: true
Antrian  : [305, 304, 303, 302, 301]',
  NOW(), NOW(), NULL, 'hard'
),

-- ============================================================
-- HARD 5 - Mencari Skor Tertinggi di Antrian Turnamen
-- ============================================================
(
  UUID(),
  '019863c4-59f9-7319-9104-08267fc3c551',
  (SELECT id FROM soal WHERE judul = 'Mencari Skor Tertinggi di Antrian Turnamen' LIMIT 1),
  'import java.util.Scanner;
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
  'Maksimum : 95
Posisi   : 4
Antrian  : [80, 95, 60, 90, 75]',
  NOW(), NOW(), NULL, 'hard'
);