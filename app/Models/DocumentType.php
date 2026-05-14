<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model {
    protected $fillable = [
        'name', 'category', 'description', 'requirements', 'fee', 'processing_days', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'fee'             => 'decimal:2',
        'is_active'       => 'boolean',
        'processing_days' => 'integer',
    ];

    // ─── Relationships ───

    public function documentRequests() {
        return $this->hasMany(DocumentRequest::class);
    }

    // ─── Query Scopes ───

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query) {
        return $query->where('is_active', false);
    }

    // ─── Computed Helpers ───

    public function getCategoryBadgeAttribute(): string {
        return match($this->category) {
            'Clearance'   => 'bg-blue-100 text-blue-800',
            'Certificate' => 'bg-emerald-100 text-emerald-800',
            'Permit'      => 'bg-purple-100 text-purple-800',
            'ID'          => 'bg-amber-100 text-amber-800',
            default       => 'bg-gray-100 text-gray-800',
        };
    }

    /** Total number of requests for this document type */
    public function getTotalRequestsAttribute(): int {
        return $this->documentRequests()->count();
    }

    /** Total revenue from released requests for this document type */
    public function getTotalRevenueAttribute(): float {
        return $this->documentRequests()
            ->where('status', 'released')
            ->count() * $this->fee;
    }

    /** Timestamp of the most recent request for this document type */
    public function getLastRequestedAtAttribute() {
        return $this->documentRequests()->latest()->value('created_at');
    }
}

