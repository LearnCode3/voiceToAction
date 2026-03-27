<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportTimeline extends Model
{
    protected $fillable = ['report_id', 'status', 'note'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
