<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    public static function bootHasSlug()
    {
        static::creating(function (Model $model) {
            $model->slug = $model->generateUniqueSlug();
        });
    }

    private function generateUniqueSlug(): string
    {
        $slug = Str::slug($this->title);
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = Str::slug($this->title) . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
