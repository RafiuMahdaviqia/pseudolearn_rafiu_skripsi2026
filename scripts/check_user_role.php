<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::withTrashed()
    ->where('email', 'dosen@gmail.com')
    ->get(['id', 'name', 'email', 'is_admin', 'deleted_at', 'created_at', 'updated_at']);

if ($users->isEmpty()) {
    echo "No user found for dosen@gmail.com\n";
    exit(0);
}

foreach ($users as $u) {
    echo json_encode([
        'id' => $u->id,
        'name' => $u->name,
        'email' => $u->email,
        'is_admin' => $u->is_admin,
        'deleted_at' => $u->deleted_at,
        'created_at' => (string) $u->created_at,
        'updated_at' => (string) $u->updated_at,
    ], JSON_UNESCAPED_SLASHES) . "\n";
}
