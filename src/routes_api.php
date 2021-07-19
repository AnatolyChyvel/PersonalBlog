<?php 
return [
    '~^articles/(\d+)$~' => [\MyProject\Controllers\Api\ArticlesApiController::class, 'view'],
    '~^comments/(\d+)$~' => [\MyProject\Controllers\Api\CommentsApiController::class, 'view'],
];