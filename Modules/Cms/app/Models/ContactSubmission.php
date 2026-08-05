<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\BaseModel;

class ContactSubmission extends BaseModel
{
    use HasFactory;

    protected $fillable = ['full_name', 'email', 'phone', 'subject', 'message', 'status', 'admin_notes'];
}
