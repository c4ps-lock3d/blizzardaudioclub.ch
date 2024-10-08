<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artiste extends Model
{
    use HasFactory;
    protected $guarded = []; //banlist

    public function products(){
        return $this->belongsToMany(\Webkul\Product\Models\Product::class);
    }
}

