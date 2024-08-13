<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    // Define the table name if it does not follow Laravel's convention
    protected $table = 'branches';

    // Define the primary key if it does not follow Laravel's convention
    protected $primaryKey = 'id';

    // If the primary key is not an incrementing integer, set this to false
    public $incrementing = true;

    // If the table does not have timestamps, set this to false
    public $timestamps = true;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'name',
        'location',
    ];

    // Optionally, define the hidden attributes if needed
    protected $hidden = [];
}
