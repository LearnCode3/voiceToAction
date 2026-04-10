<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable {
    use Notifiable;

    protected $fillable = ['name','email','password','role','office_id','is_active','email_verified_at'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['password'=>'hashed','is_active'=>'boolean','email_verified_at'=>'datetime'];

    public function reports(): HasMany  { return $this->hasMany(Report::class); }
    public function office(): BelongsTo { return $this->belongsTo(Office::class,'office_id','slug'); }

    public function isOfficeStaff(): bool  { return $this->role === 'office_staff'; }
    public function isSuperAdmin(): bool   { return $this->role === 'super_admin'; }
    public function canAccessPanel(): bool { return in_array($this->role,['office_staff','super_admin']); }
    public function isRegularUser(): bool  { return $this->role === 'user'; }
    public function isBanned(): bool       { return !$this->is_active; }
}
