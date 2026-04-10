<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model {
    protected $fillable = ['slug','name','icon','description','is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function reports(): HasMany { return $this->hasMany(Report::class,'office_id','slug'); }
    public function admins(): HasMany  { return $this->hasMany(User::class,'office_id','slug'); }

    public static function routeCategory(string $category): ?self {
        return static::where('slug',$category)->where('is_active',true)->first()
            ?? static::where('is_active',true)->first();
    }
}
