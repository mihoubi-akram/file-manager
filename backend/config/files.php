<?php

return [

    'disk' => env('FILE_STORAGE_DISK', 'local'),

    'upload_path' => env('FILE_UPLOAD_PATH', 'files'),

    'max_upload_count' => 5,

    'max_size_kb' => 10240, // 10 MB per file

    'allowed_mimes' => ['pdf', 'docx', 'png', 'jpg', 'jpeg', 'odt'],

    'per_page' => 15,

    'max_per_page' => 100,

];
