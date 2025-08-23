<?php

// -------------------------------------------------------------------------
// Archivo de configuración para el hashing de contraseñas en Laravel.
// Define el algoritmo predeterminado y sus parámetros.
// -------------------------------------------------------------------------

return [
    // ---------------------------------------------------------------------
    // 'driver': Algoritmo de hashing por defecto.
    // Opciones comunes:
    // - 'bcrypt'  (predeterminado, soportado en casi cualquier PHP >=5.5)
    // - 'argon2id' (más moderno, requiere PHP >=7.3 y soporte Argon2)
    // ---------------------------------------------------------------------
    'driver' => 'bcrypt', // Cambiar a 'argon2id' si el entorno lo soporta.

    // ---------------------------------------------------------------------
    // Configuración específica para bcrypt.
    // - 'rounds': costo computacional (cuanto más alto, más seguro pero más lento).
    // - Se puede sobrescribir mediante la variable de entorno BCRYPT_ROUNDS.
    // ---------------------------------------------------------------------
    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12), // Por defecto, 12 rondas de hashing.
    ],

    // ---------------------------------------------------------------------
    // Configuración específica para Argon2.
    // - 'memory': memoria usada en KB (65536 = 64MB).
    // - 'threads': número de hilos paralelos.
    // - 'time': número de iteraciones.
    // ---------------------------------------------------------------------
    'argon'  => [
        'memory'  => 65536, // 64MB de memoria para el algoritmo.
        'threads' => 1,     // 1 hilo de procesamiento.
        'time'    => 4,     // 4 iteraciones.
    ],
];
