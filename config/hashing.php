<?php

return [
    'driver' => 'bcrypt', // o 'argon2id' si tu PHP lo soporta
    'bcrypt' => ['rounds' => env('BCRYPT_ROUNDS', 12)],
    'argon'  => ['memory' => 65536, 'threads' => 1, 'time' => 4],
];
