<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ReportImage extends Model {
    protected $fillable = ['report_id','path','original_name','mime_type','size'];

    public function report(): BelongsTo { return $this->belongsTo(Report::class); }

    /** Public URL for the image */
    public function getUrlAttribute(): string {
        return Storage::url($this->path);
    }

    /** Human-readable file size */
    public function getHumanSizeAttribute(): string {
        $bytes = $this->size ?? 0;
        if ($bytes >= 1048576) return round($bytes/1048576,1).' MB';
        if ($bytes >= 1024)    return round($bytes/1024,1).' KB';
        return $bytes.' B';
    }

    /** True if this is an image that can be shown inline */
    public function getIsImageAttribute(): bool {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }
}
