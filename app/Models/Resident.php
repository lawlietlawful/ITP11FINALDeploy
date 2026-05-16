<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model {
    protected $fillable = [
        'first_name', 'middle_name', 'last_name',
        'address', 'contact_number', 'email', 'birthdate',
        'gender', 'civil_status',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    /**
     * Auto-generate a unique Resident ID on creation.
     * Format: RES-YYYY-NNNN (e.g., RES-2026-0012)
     */
    protected static function boot() {
        parent::boot();

        static::creating(function ($resident) {
            if (empty($resident->resident_id)) {
                $year = date('Y');
                $lastResident = static::where('resident_id', 'like', "RES-{$year}-%")
                    ->orderByRaw('CAST(SUBSTRING(resident_id, 10) AS UNSIGNED) DESC')
                    ->first();

                $nextSeq = 1;
                if ($lastResident && preg_match('/RES-\d{4}-(\d+)/', $lastResident->resident_id, $matches)) {
                    $nextSeq = (int) $matches[1] + 1;
                }

                $resident->resident_id = "RES-{$year}-" . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Full name accessor including middle initial.
     * Examples: "Juan D. Dela Cruz" or "Juan Dela Cruz" (if no middle name)
     */
    public function getFullNameAttribute(): string {
        $middleInitial = $this->middle_name
            ? ' ' . strtoupper(substr($this->middle_name, 0, 1)) . '.'
            : '';
        return $this->first_name . $middleInitial . ' ' . $this->last_name;
    }

    /**
     * Age accessor — auto-computes from birthdate.
     * Returns null if birthdate is not set.
     */
    public function getAgeAttribute(): ?int {
        return $this->birthdate ? $this->birthdate->age : null;
    }

    /**
     * Check if today is this resident's birthday.
     */
    public function getIsBirthdayTodayAttribute(): bool {
        if (!$this->birthdate) return false;
        return $this->birthdate->isBirthday();
    }

    /**
     * Check if this resident's birthday is within the next 7 days.
     */
    public function getIsBirthdayThisWeekAttribute(): bool {
        if (!$this->birthdate) return false;
        $today = now();
        $birthdayThisYear = $this->birthdate->copy()->year($today->year);
        // Handle year boundary (e.g., Dec 28 checking for Jan 2)
        if ($birthdayThisYear->isPast() && !$birthdayThisYear->isToday()) {
            $birthdayThisYear->addYear();
        }
        return $birthdayThisYear->diffInDays($today) <= 7;
    }

    /**
     * Scope: residents with birthdays this month.
     */
    public function scopeBirthdayThisMonth($query) {
        return $query->whereMonth('birthdate', now()->month)
                     ->whereNotNull('birthdate');
    }

    /**
     * Status accessor — determines resident engagement level.
     * Priority: Approved/Released Request in 6 months > New (24 hours) > Inactive
     */
    public function getStatusAttribute(): string {
        // "Active" — has at least one approved/processing/released request in the last 6 months
        $hasApprovedRequest = $this->documentRequests()
            ->whereIn('status', ['processing', 'released'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->exists();

        if ($hasApprovedRequest) {
            return 'Active';
        }

        // "New" — registered within the last 24 hours and pending/no approved requests
        if ($this->created_at && $this->created_at->greaterThanOrEqualTo(now()->subDay())) {
            return 'New';
        }

        return 'Inactive';
    }

    /**
     * Returns CSS classes for the status badge dot + label.
     */
    public function getStatusColorAttribute(): array {
        return match ($this->status) {
            'New'      => ['dot' => 'bg-amber-400', 'text' => 'text-amber-700', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200'],
            'Active'   => ['dot' => 'bg-emerald-400', 'text' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200'],
            'Inactive' => ['dot' => 'bg-gray-300', 'text' => 'text-gray-500', 'bg' => 'bg-gray-50', 'border' => 'border-gray-200'],
        };
    }

    public function documentRequests() {
        return $this->hasMany(DocumentRequest::class);
    }

    /**
     * Get other residents living at the same address (household members).
     */
    public function householdMembers() {
        return static::where('address', $this->address)
            ->where('last_name', $this->last_name)
            ->where('id', '!=', $this->id)
            ->get();
    }

    /**
     * Count of household members at the same address (excluding self).
     */
    public function getHouseholdCountAttribute(): int {
        return static::where('address', $this->address)
            ->where('last_name', $this->last_name)
            ->where('id', '!=', $this->id)
            ->count();
    }
}
