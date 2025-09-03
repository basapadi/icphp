<?php

namespace App\Traits;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasOptionalRelation
{
    public function getRelationIfExist(string $relation)
    {
        // pastikan method relasi ada
        if (!method_exists($this, $relation)) {
            return null;
        }

        $relationObj = $this->$relation(); // instance relasi

        // cek tipe relasi
        if ($relationObj instanceof HasOne || $relationObj instanceof BelongsTo) {
            return $relationObj->first(); // model tunggal atau null
        }

        if ($relationObj instanceof HasMany || $relationObj instanceof BelongsToMany) {
            return $relationObj->get(); // collection, bisa kosong
        }

        // fallback, bisa jadi relasi custom
        return $this->relationLoaded($relation) ? $this->$relation : $relationObj->get();
    }
}
