<?php

// LOCAL DEVELOPMENT CONFIG
// Productionda bu faylni .gitignore ga qo'shib, haqiqiy ma'lumotlar bilan to'ldiring.

return [
    // MySQL server manzili va porti (jony ulanishi)
    'db_host' => '127.127.126.4',
    'db_port' => 3306,

    'db_name' => 'task4_php',
    'db_user' => 'root',
    'db_pass' => '', // Workbench dagi paroling

    // Bazaviy URL, oxirida "/" bo'lmaydi. OSPanel uchun odatda: http://task4
    'base_url'  => 'http://task4',
    'mail_from' => 'no-reply@example.com',
    // Rivojlantirish uchun: true bo'lsa, tasdiqlash linki sahifada ham ko'rsatiladi
    'debug_show_confirmation_link' => true,
];

