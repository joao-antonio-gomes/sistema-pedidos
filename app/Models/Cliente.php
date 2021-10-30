<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['cpf', 'nome', 'email', 'telefone'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
