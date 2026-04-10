<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model {
    protected $fillable = [
        'report_number','user_id','submitter_name','submitter_email',
        'category','office_id','office_name','location','description',
        'priority','status','feedback','feedback_rating',
    ];

    const STATUS_FLOW = ['Submitted','Assigned','In Progress','Resolved'];

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function office(): BelongsTo  { return $this->belongsTo(Office::class,'office_id','slug'); }
    public function timelines(): HasMany { return $this->hasMany(ReportTimeline::class)->orderBy('created_at'); }
    public function images(): HasMany    { return $this->hasMany(ReportImage::class)->latest(); }

    public static function generateNumber(): string {
        return 'RPT-'.strtoupper(substr(uniqid(),-6));
    }
    public static function routeToOffice(string $category): Office {
        return Office::routeCategory($category) ?? Office::where('is_active',true)->firstOrFail();
    }

    public function getOfficeIconAttribute(): string {
        return optional($this->office)->icon ?? '📋';
    }
    public function getStatusBadgeClassAttribute(): string {
        return match($this->status) {
            'Submitted'   => 'bg-stone-100 text-stone-600',
            'Assigned'    => 'bg-blue-100 text-blue-700',
            'In Progress' => 'bg-amber-100 text-amber-700',
            'Resolved'    => 'bg-green-100 text-green-700',
            default       => 'bg-stone-100 text-stone-600',
        };
    }
}
