<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    public $timestamps = false;
    
    protected $fillable = [
        'valor_total',
        'valor_frete',
        'data',
        'id_cliente',
        'id_loja',
        'id_situacao'
    ];

    public function pagamento()
    {
        return $this->hasOne(PedidoPagamento::class, 'id_pedido');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
}
