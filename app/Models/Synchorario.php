<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Synchorario
 *
 * @property $id
 * @property $Descripcion
 * @property $Hora
 * @property $Activo
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Synchorario extends Model
{
    
    static $rules = [
		'Descripcion' => 'required|max:100',
		'Hora' => 'required|date_format:H:i:s',
		'Activo' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['Descripcion','Hora','Activo'];



}
