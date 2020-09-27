<?php
declare(strict_types=1);

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class TaggedJsonResponse extends JsonResponse implements TaggedResponseInterface
{
    private ?array $tags;

    public function setTags(?array $tags = null): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags ?: [];
    }
}