<?php

declare(strict_types=1);

return [
    'required' => ':attribute maydoni majburiy.',
    'email' => ':attribute haqiqiy email bo‘lishi kerak.',
    'min' => [
        'string' => ':attribute kamida :min ta belgi bo‘lishi kerak.',
        'numeric' => ':attribute :min dan katta bo‘lishi kerak.',
    ],
    'max' => [
        'string' => ':attribute :max ta belgidan oshmasligi kerak.',
        'numeric' => ':attribute :max dan kichik bo‘lishi kerak.',
    ],
    'unique' => ':attribute allaqachon band.',
    'confirmed' => ':attribute tasdiqlash bilan mos kelmadi.',
    'exists' => 'Tanlangan :attribute mavjud emas.',
    'image' => ':attribute rasm fayli bo‘lishi kerak.',
    'in' => 'Tanlangan :attribute noto‘g‘ri.',
    'url' => ':attribute haqiqiy URL bo‘lishi kerak.',
    'date' => ':attribute sana bo‘lishi kerak.',
    'current_password' => 'Joriy parol noto‘g‘ri.',
    'password' => 'Parol talablarga javob bermaydi.',

    'attributes' => [
        'name' => 'Ism',
        'email' => 'Email',
        'phone' => 'Telefon',
        'password' => 'Parol',
        'title' => 'Sarlavha',
        'body' => 'Matn',
        'content' => 'Kontent',
        'description' => 'Tavsif',
        'message' => 'Xabar',
    ],
];
