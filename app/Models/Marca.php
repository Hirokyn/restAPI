<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

   protected $fillable = ['nome','imagem']; 

   public function rules()
   {
        $regras = [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mime:png,jpg,jpeg'
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

   public function modelos()
   {
      return $this->hasMany('App\Models\Modelo');
   }

}
