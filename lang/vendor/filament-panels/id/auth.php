<?php

return [
    'auth' => [
        'title'   => 'Masuk',
        'heading' => 'Masuk',
        'form'    => [
            'actions' => [
                'authenticate' => [
                    'label' => 'Masuk',
                ],
            ],
            'email' => [
                'label' => 'Alamat Email',
            ],
            'password' => [
                'label' => 'Kata Sandi',
            ],
            'remember' => [
                'label' => 'Ingat saya',
            ],
        ],
        'messages' => [
            'failed'    => 'Email atau kata sandi salah.',
            'throttled' => 'Terlalu banyak percobaan. Coba lagi dalam :seconds detik.',
        ],
    ],
];
