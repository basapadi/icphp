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
        // cek nested relation (ada tanda titik)
        if (str_contains($relation, '.')) {
            $parts = explode('.', $relation);
            $first = array_shift($parts);

            // relasi pertama harus ada
            if (!method_exists($this, $first)) {
                return null;
            }

            $relationData = $this->getRelationIfExist($first);

            // kalau collection (HasMany/BelongsToMany)
            if ($relationData instanceof \Illuminate\Support\Collection) {
                return $relationData->map(function ($item) use ($parts) {
                    if (method_exists($item, 'getRelationIfExist')) {
                        return $item->getRelationIfExist(implode('.', $parts));
                    }
                    return null;
                });
            }

            // kalau single relation
            if ($relationData && method_exists($relationData, 'getRelationIfExist')) {
                return $relationData->getRelationIfExist(implode('.', $parts));
            }

            return null;
        }

        // --- relasi single-level ---
        if (!method_exists($this, $relation)) {
            return null;
        }

        $relationObj = $this->$relation();

        if ($relationObj instanceof HasOne || $relationObj instanceof BelongsTo) {
            return $relationObj->first();
        }

        if ($relationObj instanceof HasMany || $relationObj instanceof BelongsToMany) {
            return $relationObj->get();
        }

        return $this->relationLoaded($relation) ? $this->$relation : $relationObj->get();
    }

    public function loadRelationsWithNested(array $relations)
    {
        foreach ($relations as $relation) {
            if (str_contains($relation, '.')) {
                $parts = explode('.', $relation);
                $first = array_shift($parts);
                $nested = implode('.', $parts);

                // pastikan relasi pertama sudah ada di model
                if (!$this->relationLoaded($first)) {
                    $this->setRelation($first, $this->getRelationIfExist($first));
                }

                $relData = $this->getRelation($first);

                if ($relData instanceof \Illuminate\Support\Collection) {
                    $relData->each(function ($item) use ($nested) {
                        if (method_exists($item, 'loadRelationsWithNested')) {
                            $item->loadRelationsWithNested([$nested]);
                        }
                    });
                } elseif ($relData) {
                    if (method_exists($relData, 'loadRelationsWithNested')) {
                        $relData->loadRelationsWithNested([$nested]);
                    }
                }
            } else {
                // relasi level 1
                if (!$this->relationLoaded($relation)) {
                    $this->setRelation($relation, $this->getRelationIfExist($relation));
                }
            }
        }

        return $this;
    }



}
