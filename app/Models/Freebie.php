<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freebie extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional if table name follows Laravel convention)
    protected $table = 'freebies';

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'id';

    // Set timestamps to false if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Define the attributes that are mass assignable
    protected $fillable = [
        'name',
    ];

    // Define any relationships if applicable
    // For example, if Freebie belongs to a certain category:
    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }
}

