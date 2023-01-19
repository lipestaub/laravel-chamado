<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChamadoTemp extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'empresa',
        'email',
        'telefone',
        'titulo',
        'mensagem',
        'anexo',
        'chave_acesso',
        'datahora',
        'ip',
    ];
}
