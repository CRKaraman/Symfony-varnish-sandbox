<?php
declare(strict_types=1);

namespace App\Response;

interface TaggedResponseInterface
{
    public function getTags(): array;
}
