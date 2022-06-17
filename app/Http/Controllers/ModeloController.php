<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use App\Repositories\ModeloRepository;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo){
        $this->modelo = $modelo;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /*
    End points
    Metricas de retorno
    Geral              -> http://localhost:8000/api/modelo
    Especifico modelo  -> http://localhost:8000/api/modelo?atributos=id,nome,marca_id
    Especifico rel     -> http://localhost:8000/api/modelo?atributos=id,nome,marca_id&atributos_marca=nome
    Filtros            -> http://localhost:8000/api/modelo?atributos=id,nome&filtro=nome:like:Ford
    */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);

        if($request->has('atributos_marca')) {
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $modeloRepository->selectAtributos($request->atributos);
        } 

        return response()->json($modeloRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validade($this->modelo->rules(), $this->modelo->feedback());
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id'       => $request->marca_id,
            'nome'           => $request->nome,
            'imagem'         => $imagem_urn,
            'numero_portas'  => $request->numero_portas,
            'lugares'        => $request->lugares,
            'air_bag'        => $request->air_bag,
            'abs'            => $request->abs
        ]);

        return response()->json($modelo,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->modelo->with('marca')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->findOrFail($id);

        if($request->method() === 'PATCH'){

            $regrasDinamicas = array();
            foreach($modelo->rules() as $input => $regra){

                if(array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regra;
                }

            }

            $request->validate($regrasDinamicas, $modelo->feedback());

        }
        else{
            $request->validate($modelo->rules(), $modelo->feedback());
        }

        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;
        $modelo->save();
        
        return response()->json($modelo,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->findOrFail($id);
        Storage::disk('public')->delete($modelo->imagem);
        $modelo->delete();

        return response()->json(['O registro de marca foi deletado com sucesso'], 200);
    }
}
