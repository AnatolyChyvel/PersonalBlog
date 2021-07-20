<?php 
return [
    '~^list/articles$~' => [\MyProject\Controllers\Api\ArticlesApiController::class, 'view'],
    '~^comments/(\d+)$~' => [\MyProject\Controllers\Api\CommentsApiController::class, 'view'],
    '~admins/comments~' => [\MyProject\Controllers\Api\CommentsApiController::class, 'viewAllComments'],
];