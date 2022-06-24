<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = ['marca_id','nome','imagem','numero_portas','lugares','air_bag','abs']; 

   public function rules()
   {
        $regras = [
            'marca_id'      => 'exists:marcas,id',
            'nome'          => 'required|unique:modelos,nome,'.$this->id.'|min:3',
            'imagem'        => 'required|file|mimes:png,jpg,jpeg',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares'       => 'required|integer|digits_between:1,20',
            'air_bag'       => 'required|boolean',
            'abs'           => 'required|boolean'
        ];
        
        return $regras;
   }

   public function feedback()
   {
        $feedback = [
        'required'      => 'O campo :attribute é obrigatório',
        'nome.unique'   => 'O nome da marca já existe',
        'nome.min'      => 'O nome deve ter pelo menos 3 caracteres',
        'imagem.mimes'  => 'O arquivo deve ser uma imagem do tipo png,jpg ou jpeg'
        ];
        
        return $feedback;
   }

   public function marca()
   {
        return $this->belongsTo('App\Models\Marca');
   }

}
