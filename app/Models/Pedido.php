<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['numero_pedido', 'produto', 'valor', 'data_pedido', 'cliente_id'];
}
