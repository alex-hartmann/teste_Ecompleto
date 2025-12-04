<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'cpf_cnpj',
        'email',
        'tipo_pessoa',
        'data_nasc',
        'id_loja'
    ];
}
