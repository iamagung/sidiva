<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $table = 'activity';

    public static function store($user,$pesan){
        $save = new Activity;
        $save->user_id = $user;
        $save->aktivitas = $pesan;
        $save->save();
		return ($save) ? $save : false;
    }
}
