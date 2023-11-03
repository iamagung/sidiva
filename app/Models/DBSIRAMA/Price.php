<?php

namespace App\Models\DBSIRAMA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class Price extends Model
{
    use HasFactory;
    public function __construct() {
        // $this->setConnection(Config::get('database.connections.dbsirama_admin'))
        $this->setConnection('dbsirama_admin');
        // $this->connection='dbsirama_admin';
        $this->table = Config::get('database.connections.dbsirama_admin.database').'.farmasi.price';
    }

    public function ms_item()
    {
        return $this->belongsTo(User::class, 'item_id', 'item_id');
    }
}
