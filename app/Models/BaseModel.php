<?php

namespace App\Models;

use App\Traits\HasOptionalRelation;
use Btx\Query\Model;
use Vinkla\Hashids\Facades\Hashids;
use Exception;
use Carbon\Carbon;

class BaseModel extends Model
{
    use HasOptionalRelation;

    // protected $hidden = ['id'];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (property_exists($this, 'appends')) {
            $this->appends = array_merge($this->appends, [
                'created_at_formatted',
                'updated_at_formatted',
                'encode_id',
                'status_label',
                'status_type',
                'active_type',
                'color_status_label'
            ]);
        }
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? Carbon::parse($this->created_at)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at ? Carbon::parse($this->updated_at)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    public function getEncodeIdAttribute()
    {
        return $this->id ? Hashids::encode($this->id) : null;
    }

    public function getStatusLabelAttribute()
    {
        if (isset($this->status)) {
            return $this->status ? 'Aktif' : 'Tidak Aktif';
        } else return null;
    }

    public function getStatusTypeAttribute()
    {
        if (isset($this->status)) {
            return $this->status ? 'success' : 'error';
        } else return null;
    }

    public function getActiveTypeAttribute()
    {
        if (isset($this->status)) {
            return $this->active ? 'success' : 'error';
        } else return null;
    }

    public function getColorStatusLabelAttribute()
    {
        $statuses = config('ihandcashier.status');
        if (isset($this->active)) {
            return $statuses[$this->active]['color'];
        } else if (isset($this->status)) {
            return $statuses[$this->status]['color'];
        } else return '';
    }

    public function auditLogs()
    {
        return AuditLog::query()
            ->with(['user'])
            ->forModel($this)
            ->orderByDesc('created_at');
    }
}
