<?php

namespace App\Models;
use Btx\Query\Model;
use Vinkla\Hashids\Facades\Hashids;

class BaseModel extends Model
{
    protected $hidden = ['id'];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if(property_exists($this, 'appends')){
            $this->appends = array_merge($this->appends, ['created_at_formatted','updated_at_formatted','encode_id']);
        }
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('d-m-Y H:i') : null;
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('d-m-Y H:i') : null;
    }

    public function getEncodeIdAttribute(){
        return $this->id ? Hashids::encode($this->id) : null;
    }
}