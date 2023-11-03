<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmCustomer extends Model
{
    use HasFactory;
    protected $connection = 'dbrsud';
    protected $table = 'tm_customer';
    protected $primaryKey = "KodeCust";
    public $timestamps = false;
    public $incrementing = false;
}
