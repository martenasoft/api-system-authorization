<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;

#[ApiResource(
    operations: [
        new Post(controller: \App\Controller\Authorization::class)
    ]
)]
class Authorization
{

}
