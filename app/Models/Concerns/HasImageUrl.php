<?php

namespace App\Models\Concerns;

use App\Support\Media;

trait HasImageUrl
{
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Media::url($this->image) : null;
    }
}
