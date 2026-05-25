<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSoalKonversiSeeder extends Seeder
{
    public function run(): void
    {
        // Queue 2
        $data = [
            // ============================================================
            // BAGIAN 1: QUEUE (EASY)
            // ============================================================

            // Easy 1: enqueue + print
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Operasi Enqueue dan Print pada Antrian' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Main q = new Main(10);\n" .
                                "        q.enqueue(15);\n" .
                                "        q.enqueue(30);\n" .
                                "        q.print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "15 30 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
            ],

            // Easy 2: enqueue + dequeue + print
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Operasi Dequeue dan Print pada Antrian' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public int dequeue() {\n" .
                                "        int dt = data[front];\n" .
                                "        front++;\n" .
                                "        size--;\n" .
                                "        return dt;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Main q = new Main(10);\n" .
                                "        q.enqueue(10); q.enqueue(20); q.enqueue(30);\n" .
                                "        q.dequeue();\n" .
                                "        q.print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "20 30 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
            ],

            // Easy 3: enqueue + print (variasi data berbeda)
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Antrian Barang Gudang (Enqueue & Print)' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Main q = new Main(10);\n" .
                                "        q.enqueue(100); q.enqueue(200); q.enqueue(300);\n" .
                                "        q.print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "100 200 300 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
            ],

            // Easy 4: enqueue + dequeue + print (2x dequeue)
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Pengurangan Antrian Loket (Dequeue & Print)' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public int dequeue() {\n" .
                                "        int dt = data[front];\n" .
                                "        front++;\n" .
                                "        size--;\n" .
                                "        return dt;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Main q = new Main(10);\n" .
                                "        q.enqueue(1); q.enqueue(2); q.enqueue(3); q.enqueue(4);\n" .
                                "        q.dequeue(); q.dequeue();\n" .
                                "        q.print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "3 4 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
            ],

            // Easy 5: enqueue + print (2 kali)
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Pencatatan Cepat Antrian (Enqueue & Print)' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Main q = new Main(10);\n" .
                                "        q.enqueue(88); q.print();\n" .
                                "        q.enqueue(99); q.print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "88 \n88 99 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
            ],

            // ============================================================
            // BAGIAN 2: QUEUE (MEDIUM)
            // ============================================================

            // Medium 1: Scanner + IsFull + enqueue + peek
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Cek Kapasitas dan Peek Antrian Layanan' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsFull() {\n" .
                                "        if (size == max) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void peek() {\n" .
                                "        if (!IsEmpty()) {\n" .
                                "            System.out.println(\"Elemen terdepan: \" + data[front]);\n" .
                                "        } else {\n" .
                                "            System.out.println(\"Queue masih kosong\");\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        int n = sc.nextInt();\n" .
                                "        Main q = new Main(n);\n" .
                                "        System.out.println(\"Penuh: \" + q.IsFull());\n" .
                                "        q.enqueue(101);\n" .
                                "        q.peek();\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Penuh: false\nElemen terdepan: 101",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
            ],

            // Medium 2: IsEmpty + enqueue + dequeue + clear
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Mengosongkan Sisa Antrian' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public int dequeue() {\n" .
                                "        int dt = data[front];\n" .
                                "        front++;\n" .
                                "        size--;\n" .
                                "        return dt;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void clear() {\n" .
                                "        front = rear = -1;\n" .
                                "        size = 0;\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Main q = new Main(10);\n" .
                                "        q.enqueue(55); q.enqueue(66);\n" .
                                "        q.dequeue();\n" .
                                "        q.clear();\n" .
                                "        System.out.println(\"Kosong: \" + q.IsEmpty());\n" .
                                "    }\n" .
                                "}",
                'output'     => "Kosong: true",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
            ],

            // Medium 3: Scanner + enqueue + dequeue + print
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Simulasi Operasi Layanan Queue' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public int dequeue() {\n" .
                                "        int dt = data[front];\n" .
                                "        front++;\n" .
                                "        size--;\n" .
                                "        return dt;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        Main q = new Main(10);\n" .
                                "        for (int i = 0; i < 3; i++) q.enqueue(sc.nextInt());\n" .
                                "        q.print();\n" .
                                "        q.dequeue();\n" .
                                "        q.print();\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "5 15 25 \n15 25 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
            ],

            // Medium 4: Scanner + IsFull + enqueue + peek
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Batas Kapasitas Antrian' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsFull() {\n" .
                                "        if (size == max) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void peek() {\n" .
                                "        if (!IsEmpty()) {\n" .
                                "            System.out.println(\"Elemen terdepan: \" + data[front]);\n" .
                                "        } else {\n" .
                                "            System.out.println(\"Queue masih kosong\");\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        int n = sc.nextInt();\n" .
                                "        Main q = new Main(n);\n" .
                                "        q.enqueue(10); q.enqueue(20); q.enqueue(30);\n" .
                                "        System.out.println(\"Penuh: \" + q.IsFull());\n" .
                                "        q.peek();\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Penuh: true\nElemen terdepan: 10",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
            ],

            // Medium 5: IsEmpty + enqueue + clear + print
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Reset Ulang Antrian Pelanggan' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void clear() {\n" .
                                "        front = rear = -1;\n" .
                                "        size = 0;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Main q = new Main(10);\n" .
                                "        q.enqueue(5); q.enqueue(6); q.enqueue(7);\n" .
                                "        q.print();\n" .
                                "        q.clear();\n" .
                                "        System.out.println(\"Kosong: \" + q.IsEmpty());\n" .
                                "    }\n" .
                                "}",
                'output'     => "5 6 7 \nKosong: true",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
            ],

            // ============================================================
            // BAGIAN 3: QUEUE (HARD)
            // mirip konsep soal PDF pretest-posttest (8,10,12,16,23,24,26,28)
            // ============================================================

            // Hard 1 (mirip soal 12 PDF): Scanner + IsFull + enqueue + loop
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Batas Kapasitas Antrean Wahana Bermain' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsFull() {\n" .
                                "        if (size == max) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        int n = sc.nextInt();\n" .
                                "        Main q = new Main(n);\n" .
                                "        int[] anak = {11, 22, 33, 44, 55};\n" .
                                "        for (int a : anak) {\n" .
                                "            if (q.IsFull()) { System.out.println(\"Penuh\"); break; }\n" .
                                "            q.enqueue(a);\n" .
                                "        }\n" .
                                "        System.out.println(\"Size: \" + q.size);\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Penuh\nSize: 3",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],

            // Hard 2 (mirip soal 16 PDF): enqueue + dequeue + reset front/rear
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Simulasi Reset Antrean Otomatis di Klinik' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public int dequeue() {\n" .
                                "        int dt = data[front];\n" .
                                "        front++;\n" .
                                "        size--;\n" .
                                "        if (front > rear) { front = -1; rear = -1; }\n" .
                                "        return dt;\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Main q = new Main(10);\n" .
                                "        q.enqueue(101); q.enqueue(102); q.enqueue(103);\n" .
                                "        q.dequeue(); q.dequeue(); q.dequeue();\n" .
                                "        System.out.println(\"Front: \" + q.front);\n" .
                                "        System.out.println(\"Rear: \" + q.rear);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Front: -1\nRear: -1",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],

            // Hard 3 (mirip soal 24 PDF): Scanner + enqueue + dequeue + push ke Stack
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Pengarsipan Digital Antrean ke Stack' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "    int[] st = new int[10];\n" .
                                "    int top = -1;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public int dequeue() {\n" .
                                "        int dt = data[front];\n" .
                                "        front++;\n" .
                                "        size--;\n" .
                                "        return dt;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void push(int dt) { st[++top] = dt; }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        Main q = new Main(10);\n" .
                                "        for (int i = 0; i < 3; i++) q.enqueue(sc.nextInt());\n" .
                                "        while (!q.IsEmpty()) q.push(q.dequeue());\n" .
                                "        System.out.println(\"Top arsip: \" + q.st[q.top]);\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Top arsip: 3",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],

            // Hard 4 (mirip soal 26 PDF): enqueue + dequeue + push + pop + print (balik urutan)
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Pembalikan Urutan Mobil Keluar Gang' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "    int[] st = new int[10];\n" .
                                "    int top = -1;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public int dequeue() {\n" .
                                "        int dt = data[front];\n" .
                                "        front++;\n" .
                                "        size--;\n" .
                                "        return dt;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void push(int dt) { st[++top] = dt; }\n" .
                                "    public int pop() { return st[top--]; }\n" .
                                "\n" .
                                "    public void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Main q = new Main(10);\n" .
                                "        q.enqueue(1); q.enqueue(2); q.enqueue(3);\n" .
                                "        while (!q.IsEmpty()) q.push(q.dequeue());\n" .
                                "        q.front = q.rear = -1; q.size = 0;\n" .
                                "        while (q.top >= 0) q.enqueue(q.pop());\n" .
                                "        q.print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "3 2 1 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],

            // Hard 5 (mirip soal 28 PDF): Scanner + enqueue + push + dequeue + pop + cek palindrom
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Validasi Antrean Palindrom' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    int[] data;\n" .
                                "    int front;\n" .
                                "    int rear;\n" .
                                "    int size;\n" .
                                "    int max;\n" .
                                "    int[] st = new int[10];\n" .
                                "    int top = -1;\n" .
                                "\n" .
                                "    public Main(int n) {\n" .
                                "        max = n;\n" .
                                "        data = new int[max];\n" .
                                "        size = 0;\n" .
                                "        front = rear = -1;\n" .
                                "    }\n" .
                                "\n" .
                                "    public boolean IsEmpty() {\n" .
                                "        if (size == 0) {\n" .
                                "            return true;\n" .
                                "        } else {\n" .
                                "            return false;\n" .
                                "        }\n" .
                                "    }\n" .
                                "\n" .
                                "    public void enqueue(int dt) {\n" .
                                "        if (IsEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt;\n" .
                                "        size++;\n" .
                                "    }\n" .
                                "\n" .
                                "    public int dequeue() {\n" .
                                "        int dt = data[front];\n" .
                                "        front++;\n" .
                                "        size--;\n" .
                                "        return dt;\n" .
                                "    }\n" .
                                "\n" .
                                "    public void push(int dt) { st[++top] = dt; }\n" .
                                "    public int pop() { return st[top--]; }\n" .
                                "\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        Main q = new Main(10);\n" .
                                "        for (int i = 0; i < 3; i++) {\n" .
                                "            int dt = sc.nextInt();\n" .
                                "            q.enqueue(dt);\n" .
                                "            q.push(dt);\n" .
                                "        }\n" .
                                "        boolean isPalin = true;\n" .
                                "        while (!q.IsEmpty()) {\n" .
                                "            if (q.dequeue() != q.pop()) isPalin = false;\n" .
                                "        }\n" .
                                "        System.out.println(\"Palindrom: \" + isPalin);\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Palindrom: true",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],
        ];

        // Linked List
        $linkedListData = [
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
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
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
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
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
                                "            System.out.print(h.d + \" \" );\n" .
                                "            h = h.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "A B C ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
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
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
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
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
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
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
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
                                "            System.out.print(h.d + \" \" );\n" .
                                "            h = h.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "2 3 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
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
                                "        if(t.p != null) t.p.n = null;\n" .
                                "        \n" .
                                "        t = n1;\n" .
                                "        while(t != null) {\n" .
                                "            System.out.print(t.d + \" \" );\n" .
                                "            t = t.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "10 20 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
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
                                "            System.out.print(h.d + \" \" );\n" .
                                "            h = h.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "Budi Caca Doni ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
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
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
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
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
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
                                "        while(h != null) {\n" .
                                "            System.out.print(h.d + \" \" );\n" .
                                "            h = h.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "3 2 1 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
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
                                "        n2.n = n1.n;\n" .
                                "        n2.p = n1;\n" .
                                "        n1.n.p = n2;\n" .
                                "        n1.n = n2;\n" .
                                "        \n" .
                                "        while(n1 != null) {\n" .
                                "            System.out.print(n1.d + \" \" );\n" .
                                "            n1 = n1.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "10 20 30 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019de356-abfa-717d-958c-e9311c2712f3',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Mencabut Berkas Rusak di Tengah Urutan' LIMIT 1)"),
                'jawaban'    => "class Node { int d; Node n; Node(int d){this.d=d;} }\n" .
                                "public class Main {\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Node h = new Node(5);\n" .
                                "        h.n = new Node(10);\n" .
                                "        h.n.n = new Node(15);\n" .
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
                                "            System.out.print(h.d + \" \" );\n" .
                                "            h = h.n;\n" .
                                "        }\n" .
                                "    }\n" .
                                "}",
                'output'     => "5 15 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
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
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],
        ];

        // Queue Lama
        $queueExtraData = [
            // ============================================================
            // QUEUE EXTRA (EASY)
            // ============================================================
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Operasi Enqueue dan Print pada Antrian' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] data = new int[10];\n" .
                                "    static int front = -1, rear = -1, size = 0, max = 10;\n" .
                                "    static void enqueue(int dt) {\n" .
                                "        if (size == 0) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt; size++;\n" .
                                "    }\n" .
                                "    static void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(15);\n" .
                                "        enqueue(30);\n" .
                                "        print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "15 30 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Operasi Dequeue dan Print pada Antrian' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] data = new int[10];\n" .
                                "    static int front = -1, rear = -1, size = 0, max = 10;\n" .
                                "    static void enqueue(int dt) {\n" .
                                "        if (size == 0) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt; size++;\n" .
                                "    }\n" .
                                "    static int dequeue() {\n" .
                                "        int dt = data[front]; front++; size--; return dt;\n" .
                                "    }\n" .
                                "    static void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(10); enqueue(20); enqueue(30);\n" .
                                "        dequeue();\n" .
                                "        print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "20 30 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Antrian Barang Gudang (Enqueue & Print)' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] data = new int[10];\n" .
                                "    static int front = -1, rear = -1, size = 0, max = 10;\n" .
                                "    static void enqueue(int dt) { if (size == 0) { front = rear = 0; } else { rear++; } data[rear] = dt; size++; }\n" .
                                "    static void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(100); enqueue(200); enqueue(300);\n" .
                                "        print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "100 200 300 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Pengurangan Antrian Loket (Dequeue & Print)' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] data = new int[10];\n" .
                                "    static int front = -1, rear = -1, size = 0, max = 10;\n" .
                                "    static void enqueue(int dt) { if (size == 0) { front = rear = 0; } else { rear++; } data[rear] = dt; size++; }\n" .
                                "    static int dequeue() { int dt = data[front]; front++; size--; return dt; }\n" .
                                "    static void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(1); enqueue(2); enqueue(3); enqueue(4);\n" .
                                "        dequeue(); dequeue();\n" .
                                "        print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "3 4 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Pencatatan Cepat Antrian (Enqueue & Print)' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] data = new int[10];\n" .
                                "    static int front = -1, rear = -1, size = 0, max = 10;\n" .
                                "    static void enqueue(int dt) { if (size == 0) { front = rear = 0; } else { rear++; } data[rear] = dt; size++; }\n" .
                                "    static void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(88); print();\n" .
                                "        enqueue(99); print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "88 \n88 99 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'easy',
            ],

            // ============================================================
            // QUEUE EXTRA (MEDIUM)
            // ============================================================
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Cek Kapasitas dan Peek Antrian Layanan' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static int[] data; static int front = -1, rear = -1, size = 0, max;\n" .
                                "    static boolean isFull() { return size == max; }\n" .
                                "    static boolean isEmpty() { return size == 0; }\n" .
                                "    static void enqueue(int dt) {\n" .
                                "        if (isEmpty()) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt; size++;\n" .
                                "    }\n" .
                                "    static void peek() {\n" .
                                "        if (!isEmpty()) System.out.println(\"Elemen terdepan: \" + data[front]);\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        max = sc.nextInt();\n" .
                                "        data = new int[max];\n" .
                                "        System.out.println(\"Penuh: \" + isFull());\n" .
                                "        enqueue(101);\n" .
                                "        peek();\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Penuh: false\nElemen terdepan: 101",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Mengosongkan Sisa Antrian' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] data = new int[10];\n" .
                                "    static int front = -1, rear = -1, size = 0, max = 10;\n" .
                                "    static boolean isEmpty() { return size == 0; }\n" .
                                "    static void enqueue(int dt) { if (isEmpty()) { front = rear = 0; } else { rear++; } data[rear] = dt; size++; }\n" .
                                "    static int dequeue() { int dt = data[front]; front++; size--; return dt; }\n" .
                                "    static void clear() { front = rear = -1; size = 0; }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(55); enqueue(66);\n" .
                                "        dequeue();\n" .
                                "        clear();\n" .
                                "        System.out.println(\"Kosong: \" + isEmpty());\n" .
                                "    }\n" .
                                "}",
                'output'     => "Kosong: true",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Simulasi Operasi Layanan Queue' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static int[] data = new int[10];\n" .
                                "    static int front = -1, rear = -1, size = 0, max = 10;\n" .
                                "    static void enqueue(int dt) { if (size == 0) { front = rear = 0; } else { rear++; } data[rear] = dt; size++; }\n" .
                                "    static int dequeue() { int dt = data[front]; front++; size--; return dt; }\n" .
                                "    static void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        for(int i=0; i<3; i++) enqueue(sc.nextInt());\n" .
                                "        print();\n" .
                                "        dequeue();\n" .
                                "        print();\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "5 15 25 \n15 25 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Batas Kapasitas Antrian' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static int[] data; static int front = -1, rear = -1, size = 0, max;\n" .
                                "    static boolean isFull() { return size == max; }\n" .
                                "    static void enqueue(int dt) { if (size == 0) { front = rear = 0; } else { rear++; } data[rear] = dt; size++; }\n" .
                                "    static void peek() { if (size != 0) System.out.println(\"Terdepan: \" + data[front]); }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        max = sc.nextInt();\n" .
                                "        data = new int[max];\n" .
                                "        enqueue(10); enqueue(20); enqueue(30);\n" .
                                "        System.out.println(\"Penuh: \" + isFull());\n" .
                                "        peek();\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Penuh: true\nTerdepan: 10",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Reset Ulang Antrian Pelanggan' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] data = new int[10];\n" .
                                "    static int front = -1, rear = -1, size = 0, max = 10;\n" .
                                "    static boolean isEmpty() { return size == 0; }\n" .
                                "    static void enqueue(int dt) { if (size == 0) { front = rear = 0; } else { rear++; } data[rear] = dt; size++; }\n" .
                                "    static void clear() { front = rear = -1; size = 0; }\n" .
                                "    static void print() {\n" .
                                "        for (int i = front; i <= rear; i++) System.out.print(data[i] + \" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(5); enqueue(6); enqueue(7);\n" .
                                "        print();\n" .
                                "        clear();\n" .
                                "        System.out.println(\"Kosong: \" + isEmpty());\n" .
                                "    }\n" .
                                "}",
                'output'     => "5 6 7 \nKosong: true",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'medium',
            ],

            // ============================================================
            // QUEUE EXTRA (HARD)
            // ============================================================
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Batas Kapasitas Antrean Wahana Bermain' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static int[] data; static int front = -1, rear = -1, size = 0, max;\n" .
                                "    static boolean isFull() { return size == max; }\n" .
                                "    static void enqueue(int dt) {\n" .
                                "        if (isFull()) return;\n" .
                                "        if (size == 0) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt; size++;\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        max = sc.nextInt();\n" .
                                "        data = new int[max];\n" .
                                "        int[] anak = {11, 22, 33, 44, 55};\n" .
                                "        for (int a : anak) {\n" .
                                "            if (isFull()) { System.out.println(\"Penuh\"); break; }\n" .
                                "            enqueue(a);\n" .
                                "        }\n" .
                                "        System.out.println(\"Size: \" + size);\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Penuh\nSize: 3",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Simulasi Reset Antrean Otomatis di Klinik' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] data = new int[10];\n" .
                                "    static int front = -1, rear = -1, size = 0, max = 10;\n" .
                                "    static void enqueue(int dt) {\n" .
                                "        if (size == 0) { front = rear = 0; } else { rear++; }\n" .
                                "        data[rear] = dt; size++;\n" .
                                "    }\n" .
                                "    static int dequeue() {\n" .
                                "        int dt = data[front]; front++; size--;\n" .
                                "        if (front > rear) { front = -1; rear = -1; }\n" .
                                "        return dt;\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(101); enqueue(102); enqueue(103);\n" .
                                "        dequeue(); dequeue(); dequeue();\n" .
                                "        System.out.println(\"Front: \" + front);\n" .
                                "        System.out.println(\"Rear: \" + rear);\n" .
                                "    }\n" .
                                "}",
                'output'     => "Front: -1\nRear: -1",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Pengarsipan Digital Antrean ke Stack' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static int[] q = new int[10]; static int qf = -1, qr = -1, qs = 0;\n" .
                                "    static int[] st = new int[10]; static int top = -1;\n" .
                                "    static void enqueue(int dt) { if(qs==0){qf=qr=0;}else{qr++;} q[qr]=dt; qs++; }\n" .
                                "    static int dequeue() { int dt=q[qf]; qf++; qs--; return dt; }\n" .
                                "    static void push(int dt) { st[++top] = dt; }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        for(int i=0; i<3; i++) enqueue(sc.nextInt());\n" .
                                "        while(qs > 0) push(dequeue());\n" .
                                "        System.out.println(\"Top arsip: \" + st[top]);\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Top arsip: 3",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Pembalikan Urutan Mobil Keluar Gang' LIMIT 1)"),
                'jawaban'    => "public class Main {\n" .
                                "    static int[] q = new int[10]; static int qf = -1, qr = -1, qs = 0;\n" .
                                "    static int[] st = new int[10]; static int top = -1;\n" .
                                "    static void enqueue(int dt) { if(qs==0){qf=qr=0;}else{qr++;} q[qr]=dt; qs++; }\n" .
                                "    static int dequeue() { int dt=q[qf]; qf++; qs--; return dt; }\n" .
                                "    static void push(int dt) { st[++top] = dt; }\n" .
                                "    static int pop() { return st[top--]; }\n" .
                                "    static void print() {\n" .
                                "        for(int i=qf; i<=qr; i++) System.out.print(q[i]+\" \");\n" .
                                "        System.out.println();\n" .
                                "    }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        enqueue(1); enqueue(2); enqueue(3);\n" .
                                "        while(qs > 0) push(dequeue());\n" .
                                "        qf = qr = -1; qs = 0;\n" .
                                "        while(top >= 0) enqueue(pop());\n" .
                                "        print();\n" .
                                "    }\n" .
                                "}",
                'output'     => "3 2 1 ",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],
            [
                'id'         => DB::raw('UUID()'),
                'id_level'   => '019863c4-59f9-7319-9104-08267fc3c551',
                'id_soal'    => DB::raw("(SELECT id FROM soal WHERE judul = 'Validasi Antrean Palindrom' LIMIT 1)"),
                'jawaban'    => "import java.util.Scanner;\n" .
                                "public class Main {\n" .
                                "    static int[] q = new int[10]; static int qf = -1, qr = -1, qs = 0;\n" .
                                "    static int[] st = new int[10]; static int top = -1;\n" .
                                "    static void enqueue(int dt) { if(qs==0){qf=qr=0;}else{qr++;} q[qr]=dt; qs++; }\n" .
                                "    static int dequeue() { int dt=q[qf]; qf++; qs--; return dt; }\n" .
                                "    static void push(int dt) { st[++top] = dt; }\n" .
                                "    static int pop() { return st[top--]; }\n" .
                                "    public static void main(String[] args) {\n" .
                                "        Scanner sc = new Scanner(System.in);\n" .
                                "        for(int i=0; i<3; i++) {\n" .
                                "            int dt = sc.nextInt();\n" .
                                "            enqueue(dt); push(dt);\n" .
                                "        }\n" .
                                "        boolean isPalin = true;\n" .
                                "        while(qs > 0) {\n" .
                                "            if(dequeue() != pop()) isPalin = false;\n" .
                                "        }\n" .
                                "        System.out.println(\"Palindrom: \" + isPalin);\n" .
                                "        sc.close();\n" .
                                "    }\n" .
                                "}",
                'output'     => "Palindrom: true",
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null, 'difficulty' => 'hard',
            ],
        ];

        $data = array_merge($data, $linkedListData, $queueExtraData);

        DB::table('bank_soal_konversi')->insert($data);

        DB::table('bank_soal_konversi')
            ->where('id_soal', function ($query) {
                $query->select('id')
                    ->from('soal')
                    ->where('judul', 'Menghapus Lagu Terakhir dari Playlist')
                    ->limit(1);
            })
            ->update([
                'jawaban' => 'class Node { String d; Node n; Node(String d){this.d=d;} }\npublic class Main {\n    public static void main(String[] args) {\n        Node h = new Node("LaguA");\n        h.n = new Node("LaguB");\n        h.n.n = new Node("LaguC");\n        \n        Node t = h;\n        while(t.n.n != null) {\n            t = t.n;\n        }\n        t.n = null;\n        \n        while(h != null) {\n            System.out.print(h.d + " ");\n            h = h.n;\n        }\n    }\n}',
                'output'     => 'LaguA LaguB ',
                'updated_at' => now(),
            ]);

        DB::table('bank_soal_konversi')
            ->where('id_soal', function ($query) {
                $query->select('id')
                    ->from('soal')
                    ->where('judul', 'Menampilkan Riwayat Lagu Secara Mundur')
                    ->limit(1);
            })
            ->update([
                'jawaban' => 'class DNode { String d; DNode p,n; DNode(String d){this.d=d;} }\npublic class Main {\n    public static void main(String[] args) {\n        DNode n1 = new DNode("Rock");\n        DNode n2 = new DNode("Jazz");\n        DNode n3 = new DNode("Pop");\n        \n        n1.n = n2; n2.p = n1;\n        n2.n = n3; n3.p = n2;\n        \n        DNode tail = n3;\n        while(tail != null) {\n            System.out.print(tail.d + " ");\n            tail = tail.p;\n        }\n    }\n}',
                'output'     => 'Pop Jazz Rock ',
                'updated_at' => now(),
            ]);
    }
}
