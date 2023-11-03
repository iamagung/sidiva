<?php

namespace App\Models\DBSIRAMA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class MsItem extends Model
{
    use HasFactory;
    public function __construct() {
        // $this->setConnection(Config::get('database.connections.dbsirama_admin'))
        $this->setConnection('dbsirama_admin');
        // $this->connection='dbsirama_admin';
        $this->table = Config::get('database.connections.dbsirama_admin.database').'.admin.ms_item';
        // $db = Config::get('database.connections.dbsirama_admin.database');
        // $this->table = "$db.ms_item";
    }

    public function price()
    {
        return $this->hasMany(Price::class, 'item_id', 'item_id');
    }
}
