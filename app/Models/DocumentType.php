<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model {
    protected $fillable = [
        'name', 'description', 'fee', 'processing_days', 'is_active',
    ];

    public function documentRequests() {
        return $this->hasMany(DocumentRequest::class);
    }
}
