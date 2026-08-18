<?php

namespace App\Traits;

trait HasCategoryMeta
{
    public $pendingAddressData = [];
    public array $virtualMeta = [];

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
        if ($key === 'pendingAddressData') {
            unset($this->attributes['pendingAddressData']);
            return $this;
        }
        if ($key === 'virtualMeta') {
            $this->virtualMeta = $value;
            unset($this->attributes['virtualMeta']);
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
        foreach ($this->attributes as $key => $value) {
            if (!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at', 'pendingAddressData', 'virtualMeta'])) {
                $meta[$key] = $this->getAttribute($key);
            }
        }
        $fields = $this->metaFields ?? [];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $meta)) {
                if ($field === 'address' && !\Illuminate\Support\Facades\Schema::hasTable('applicant_addresses')) {
                    $meta['address'] = null;
                    continue;
                }
                $meta[$field] = $this->getAttribute($field);
            }
        }
        $meta['rejected_reason'] = $this->getAttribute('rejected_reason');

                $aliases = [
            'locality_location' => 'locality_place',
            'locality_place' => 'locality_location',
            'post' => 'post_office',
            'post_office' => 'post',
            'panchayath' => 'panchayat',
            'panchayat' => 'panchayath',
            'location' => 'place',
            'place' => 'location',
            'mobile' => 'contact_number_1',
            'mobile_1' => 'contact_number_1',
            'mobile_2' => 'contact_number_2',
            'contact_number_1' => 'mobile_1',
            'contact_number_2' => 'mobile_2',
            'whatsapp' => 'whatsapp_number',
            'whatsapp_number' => 'whatsapp',
            'pin' => 'pin_code',
            'pin_code' => 'pin',
            'recommender_name' => 'recommendation_name',
            'recommendation_name' => 'recommender_name',
            'recommender_org' => 'recommendation_organization',
            'recommendation_organization' => 'recommender_org',
            'recommender_org_other' => 'recommendation_organization_other',
            'recommendation_organization_other' => 'recommender_org_other',
            'recommender_phone' => 'recommendation_phone',
            'recommendation_phone' => 'recommender_phone',
            'recommender_position' => 'recommendation_position',
            'recommendation_position' => 'recommender_position',
        ];
        foreach ($aliases as $aliasKey => $targetKey) {
            if (!isset($meta[$aliasKey]) || $meta[$aliasKey] === '' || $meta[$aliasKey] === null) {
                $meta[$aliasKey] = $meta[$targetKey] ?? ($this->getAttribute($targetKey) ?? ($this->getAttribute($aliasKey) ?? ($this->attributes[$aliasKey] ?? null)));
            }
        }

        if (is_array($this->virtualMeta)) {
            $meta = array_merge($meta, $this->virtualMeta);
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
            $table = $this->getTable();
            foreach ($value as $key => $val) {
                if ($key !== 'category' && $key !== 'sponsor_status' && $key !== 'status') {
                    if ($val === '') {
                        $val = null;
                    }
                    if (\Illuminate\Support\Facades\Schema::hasColumn($table, $key)) {
                        $this->setAttribute($key, $val);
                    } else {
                        $aliasMap = [
                            'locality_location' => 'locality_place',
                            'locality_place' => 'locality_location',
                            'recommendation_name' => 'recommender_name',
                            'recommendation_organization' => 'recommender_org',
                            'recommendation_organization_other' => 'recommender_org_other',
                            'recommendation_phone' => 'recommender_phone',
                            'recommendation_position' => 'recommender_position',
                            'recommender_name' => 'recommendation_name',
                            'recommender_org' => 'recommendation_organization',
                            'recommender_org_other' => 'recommendation_organization_other',
                            'recommender_phone' => 'recommendation_phone',
                            'recommender_position' => 'recommendation_position',
                        ];
                        if (isset($aliasMap[$key]) && \Illuminate\Support\Facades\Schema::hasColumn($table, $aliasMap[$key])) {
                            $this->setAttribute($aliasMap[$key], $val);
                        } else {
                            $addressFields = ['house_name', 'place', 'post_office', 'post', 'village', 'panchayat', 'panchayath', 'district', 'state', 'pin_code', 'pin', 'pincode', 'location', 'contact_number_1', 'contact_number_2', 'mobile', 'mobile_1', 'mobile_2'];
                            if (in_array($key, $addressFields)) {
                                $this->setAttribute($key, $val);
                            } else {
                                $vMeta = is_array($this->virtualMeta) ? $this->virtualMeta : [];
                                $vMeta[$key] = $val;
                                $this->virtualMeta = $vMeta;
                                unset($this->attributes['virtualMeta']);
                            }
                        }
                    }
                }
            }
        }
    }

    protected function setAddressField($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public static function bootHasCategoryMeta()
    {
        static::saving(function ($model) {
            if (array_key_exists('pendingAddressData', $model->attributes)) {
                unset($model->attributes['pendingAddressData']);
            }
            if (array_key_exists('virtualMeta', $model->attributes)) {
                unset($model->attributes['virtualMeta']);
            }
        });
    }

    protected function getApplicantAddressObject()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('applicant_addresses')) {
            return null;
        }
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

    public function getAddressAttribute()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('applicant_addresses')) {
            return null;
        }
        return $this->getApplicantAddressObject();
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

    public function getPostAttribute()
    {
        return $this->getPostOfficeAttribute();
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

    public function getPanchayathAttribute()
    {
        return $this->getPanchayatAttribute();
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

    public function getPinAttribute()
    {
        return $this->getPinCodeAttribute();
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

    public function getMobile1Attribute()
    {
        return $this->getContactNumber1Attribute();
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

    public function getMobileAttribute()
    {
        return $this->getContactNumber1Attribute();
    }

    public function getMobile2Attribute()
    {
        return $this->getContactNumber2Attribute();
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

    public function getWhatsappNumberAttribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['whatsapp_number'] ?? null) ?? ($addr ? ($addr->whatsapp_number ?? null) : ($this->attributes['whatsapp_number'] ?? null));
    }

    public function setWhatsappNumberAttribute($value)
    {
        $this->setAddressField('whatsapp_number', $value);
    }

    public function getTownAttribute()
    {
        $addr = $this->getApplicantAddressObject();
        return ($this->pendingAddressData['place'] ?? $this->pendingAddressData['town'] ?? null) ?? ($addr ? ($addr->place ?? null) : ($this->attributes['place'] ?? $this->attributes['town'] ?? null));
    }

    public function setTownAttribute($value)
    {
        $this->setAddressField('place', $value);
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
