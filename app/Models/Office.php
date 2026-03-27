<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    protected $fillable = ['slug', 'name', 'icon', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'office_id', 'slug');
    }

    public function admins(): HasMany
    {
        return $this->hasMany(User::class, 'office_id', 'slug');
    }

    /**
     * Get pending (unresolved) reports count
     */
    public function getPendingCountAttribute(): int
    {
        return $this->reports()->whereNotIn('status', ['Resolved'])->count();
    }

    /**
     * Get resolved reports count
     */
    public function getResolvedCountAttribute(): int
    {
        return $this->reports()->where('status', 'Resolved')->count();
    }

    /**
     * Static helper — returns all offices as keyed array (replaces old OFFICES const)
     */
    public static function allKeyed(): array
    {
        return static::where('is_active', true)
            ->get()
            ->keyBy('slug')
            ->map(fn($o) => ['id' => $o->slug, 'name' => $o->name, 'icon' => $o->icon])
            ->toArray();
    }

    /**
     * Find office by slug or return fallback
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Route a category slug to an office — slug == category key
     */
    public static function routeCategory(string $category): ?self
    {
        return static::where('slug', $category)->where('is_active', true)->first()
            ?? static::where('is_active', true)->first();
    }
}
