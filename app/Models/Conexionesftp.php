<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Conexionesftp
 *
 * @property $id
 * @property $Host
 * @property $Puerto
 * @property $Cifrado
 * @property $User
 * @property $Password
 * @property $Ruta
 * @property $Activo
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Conexionesftp extends Model
{
    
    static $rules = [
		'Host' => 'required',
		'Puerto' => 'required',
		'Cifrado' => 'required',
		'User' => 'required',
		'Password' => 'required',
		'Ruta' => 'required',
		'Activo' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['Host','Puerto','Cifrado','User','Password','Ruta','Activo'];



}
