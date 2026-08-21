<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbstractSubmission extends Model
{
    use SoftDeletes;

    protected $table = 'abstract_submissions';

    protected $fillable = [
        'title', 'authors', 'affiliation', 'presenting_author', 'email', 'designation',
        'category', 'pres_type', 'research_type', 'pres_category',
        'abstract_body', 'reference_list', 'file_name', 'file_path', 'status',
    ];

    protected $dates = ['deleted_at'];

    public function file_url()
    {
        return $this->file_path ? url($this->file_path) : null;
    }
}
