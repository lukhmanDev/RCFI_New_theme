<?php

namespace App\Traits;

trait HasCategoryMeta
{
    /**
     * Initialize the trait by appending 'meta' to the model's serialized attributes.
     */
    public function initializeHasCategoryMeta()
    {
        $this->appends[] = 'meta';
    }

    /**
     * Dynamic getter for virtual 'meta' attribute.
     * Bundles all category-specific columns into an array.
     */
    public function getMetaAttribute()
    {
        $meta = [];
        $fields = $this->metaFields ?? [];
        foreach ($fields as $field) {
            $meta[$field] = $this->getAttribute($field);
        }
        return $meta;
    }

    /**
     * Dynamic setter for virtual 'meta' attribute.
     * Distributes the array elements to individual attributes.
     */
    public function setMetaAttribute($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $this->setAttribute($key, $val);
            }
        }
    }
}
