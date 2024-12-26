<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['user_id', 'import_type', 'file_name', 'status'];

    /**
     * Get the logs for the import.
     */
    public function logs()
    {
        return $this->hasMany(ImportLog::class);
    }

    /**
     * Get the user that owns the import.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
