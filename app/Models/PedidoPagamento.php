<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoPagamento extends Model
{
    protected $table = 'pedidos_pagamentos';

    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'id_formapagto',
        'qtd_parcelas',
        'retorno_intermediador',
        'data_processamento',
        'num_cartao',
        'nome_portador',
        'codigo_verificacao',
        'vencimento'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}
