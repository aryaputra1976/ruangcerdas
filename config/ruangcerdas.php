<?php

return [

    'payment' => [
        'bank_name' => env('RC_BANK_NAME', 'Bank Mandiri'),
        'bank_account_number' => env('RC_BANK_ACCOUNT_NUMBER', '1234567890'),
        'bank_account_holder' => env('RC_BANK_ACCOUNT_HOLDER', 'Ruang Cerdas'),

        /*
        |--------------------------------------------------------------------------
        | QRIS Image
        |--------------------------------------------------------------------------
        | Simpan gambar QRIS di:
        | public/images/payment/qris-ruangcerdas.png
        */
        'qris_image' => env('RC_PAYMENT_QRIS_IMAGE', 'images/payment/qris-ruangcerdas.png'),

        'payment_note' => env('RC_PAYMENT_NOTE', 'Transfer sesuai nominal invoice agar verifikasi lebih cepat.'),
    ],

];