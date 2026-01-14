<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];
    protected $hidden = ['pivot', 'created_at', 'updated_at'];
    /*  a property that specifies which attributes (database columns) should not be included when the model is converted to an array or JSON */

    public function users() {
        return $this->belongsToMany(User::class);
    }
}
