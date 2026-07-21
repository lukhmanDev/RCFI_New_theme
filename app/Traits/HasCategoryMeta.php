<?php

namespace App\Traits;

trait HasCategoryMeta
{
    public $pendingAddressData = [];

    /**
     * Initialize the trait by appending 'meta' to the model's serialized attributes.
     */
    public function initializeHasCategoryMeta()
    {
        $this->appends[] = 'meta';
    }

    /**
     * Intercept address attributes and store them in pendingAddressData.
     */
    public function setAttribute($key, $value)
    {
        $addressFields = ['house_name', 'place', 'post_office', 'post', 'village', 'panchayat', 'panchayath', 'district', 'state', 'pin_code', 'pin', 'pincode', 'location', 'contact_number_1', 'contact_number_2', 'mobile', 'mobile_1', 'mobile_2'];
        if (in_array($key, $addressFields) && !str_starts_with($key, 'locality_')) {
            $normalizedKey = match ($key) {
                'post' => 'post_office',
                'panchayath' => 'panchayat',
                'pin', 'pincode' => 'pin_code',
                'mobile', 'mobile_1' => 'contact_number_1',
                'mobile_2' => 'contact_number_2',
                default => $key,
            };
            $this->pendingAddressData[$normalizedKey] = $value;
            return $this;
        }

        return parent::setAttribute($key, $value);
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
                if ($key !== 'category') {
                    $this->setAttribute($key, $val);
                }
            }
        }
    }

    protected function setAddressField($key, $value)
    {
        $data = $this->pendingAddressData ?? [];
        $data[$key] = $value;
        $this->pendingAddressData = $data;
    }

    public static function bootHasCategoryMeta()
    {
        static::saved(function ($model) {
            if (isset($model->pendingAddressData) && !empty(array_filter($model->pendingAddressData))) {
                $model->address()->updateOrCreate([], $model->pendingAddressData);
                unset($model->pendingAddressData);
            }
        });
    }

    protected function getApplicantAddressObject()
    {
        $relation = $this->relationLoaded('address') ? $this->getRelationValue('address') : null;
        if (!$relation && method_exists($this, 'address')) {
            try {
                $relation = $this->address()->first();
            } catch (\Exception $e) {
                $relation = null;
            }
        }
        return ($relation instanceof \App\Models\ApplicantAddress) ? $relation : null;
    }

    /**
     * MorphOne relationship to ApplicantAddress.
     */
    public function address()
    {
        return $this->morphOne(\App\Models\ApplicantAddress::class, 'addressable');
    }

    public function getHouseNameAttribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['house_name'] ?? null) ?? ($addr ? $addr->house_name : ($this->attributes['house_name'] ?? null));
    }

    public function setHouseNameAttribute($value)
    {
        $this->setAddressField('house_name', $value);
    }

    public function getPlaceAttribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['place'] ?? null) ?? ($addr ? $addr->place : ($this->attributes['place'] ?? null));
    }

    public function setPlaceAttribute($value)
    {
        $this->setAddressField('place', $value);
    }

    public function getPostOfficeAttribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['post_office'] ?? null) ?? ($addr ? $addr->post_office : ($this->attributes['post_office'] ?? ($this->attributes['post'] ?? null)));
    }

    public function setPostOfficeAttribute($value)
    {
        $this->setAddressField('post_office', $value);
    }

    public function setPostAttribute($value)
    {
        $this->setAddressField('post_office', $value);
    }

    public function getVillageAttribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['village'] ?? null) ?? ($addr ? $addr->village : ($this->attributes['village'] ?? null));
    }

    public function setVillageAttribute($value)
    {
        $this->setAddressField('village', $value);
    }

    public function getPanchayatAttribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['panchayat'] ?? null) ?? ($addr ? $addr->panchayat : ($this->attributes['panchayat'] ?? ($this->attributes['panchayath'] ?? null)));
    }

    public function setPanchayatAttribute($value)
    {
        $this->setAddressField('panchayat', $value);
    }

    public function setPanchayathAttribute($value)
    {
        $this->setAddressField('panchayat', $value);
    }

    public function getDistrictAttribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['district'] ?? null) ?? ($addr ? $addr->district : ($this->attributes['district'] ?? null));
    }

    public function setDistrictAttribute($value)
    {
        $this->setAddressField('district', $value);
    }

    public function getStateAttribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['state'] ?? null) ?? ($addr ? $addr->state : ($this->attributes['state'] ?? null));
    }

    public function setStateAttribute($value)
    {
        $this->setAddressField('state', $value);
    }

    public function getPinCodeAttribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['pin_code'] ?? null) ?? ($addr ? $addr->pin_code : ($this->attributes['pin_code'] ?? ($this->attributes['pin'] ?? null)));
    }

    public function setPinCodeAttribute($value)
    {
        $this->setAddressField('pin_code', $value);
    }

    public function setPinAttribute($value)
    {
        $this->setAddressField('pin_code', $value);
    }

    public function getLocationAttribute()
    {
        return $this->place;
    }

    public function setLocationAttribute($value)
    {
        $this->setAddressField('place', $value);
    }

    public function getContactNumber1Attribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['contact_number_1'] ?? null) ?? ($addr ? $addr->contact_number_1 : ($this->attributes['contact_number_1'] ?? ($this->attributes['mobile_1'] ?? ($this->attributes['mobile'] ?? null))));
    }

    public function setContactNumber1Attribute($value)
    {
        $this->setAddressField('contact_number_1', $value);
    }

    public function setMobile1Attribute($value)
    {
        $this->setAddressField('contact_number_1', $value);
    }

    public function setMobileAttribute($value)
    {
        $this->setAddressField('contact_number_1', $value);
    }

    public function getContactNumber2Attribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['contact_number_2'] ?? null) ?? ($addr ? $addr->contact_number_2 : ($this->attributes['contact_number_2'] ?? ($this->attributes['mobile_2'] ?? null)));
    }

    public function setContactNumber2Attribute($value)
    {
        $this->setAddressField('contact_number_2', $value);
    }

    public function setMobile2Attribute($value)
    {
        $this->setAddressField('contact_number_2', $value);
    }

    public function getAdditionalNoteAttribute()
    {
        return $this->attributes['additional_note'] ?? ($this->attributes['details'] ?? null);
    }

    public function setAdditionalNoteAttribute($value)
    {
        $this->attributes['additional_note'] = $value;
    }

    public function getDetailsAttribute()
    {
        return $this->getAdditionalNoteAttribute();
    }

    public function setDetailsAttribute($value)
    {
        $this->setAdditionalNoteAttribute($value);
    }

    public function getEstimatedAmountAttribute()
    {
        return $this->attributes['amount_requested'] ?? null;
    }

    public function setEstimatedAmountAttribute($value)
    {
        $this->attributes['amount_requested'] = is_numeric($value) ? (int)$value : (int) preg_replace('/[^0-9]/', '', (string)$value);
    }

    public function getExpectedAmountAttribute()
    {
        return $this->attributes['amount_requested'] ?? null;
    }

    public function setExpectedAmountAttribute($value)
    {
        $this->attributes['amount_requested'] = is_numeric($value) ? (int)$value : (int) preg_replace('/[^0-9]/', '', (string)$value);
    }

    /**
     * Ignore setting category column on database tables.
     */
    public function setCategoryAttribute($value)
    {
        // Category column has been removed from application tables; ignore attribute.
    }
}
