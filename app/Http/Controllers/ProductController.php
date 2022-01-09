<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store (Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer'
        ],[
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre no es valido',
            'description.required' => 'La descripcion es requerido',
            'description.string' => 'La descripcion no es valida',
            'price.required' => 'El precio es requerido',
            'price.integer' => 'El precio no es valido',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>true,'message'=> $validator->errors()->toJson()], 400);
        }


        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;

        if($product->save()){
            return response()->json(['status' => false, 'message' => 'producto creado exitosamente'],200);
        }else{
            return response()->json(['status' => true, 'message' => 'producto no pudo ser creado'],500);
        }
    }


    public function update (Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer',
            'id' => 'required|integer'
        ],[
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre no es valido',
            'description.required' => 'La descripcion es requerido',
            'description.string' => 'La descripcion no es valida',
            'price.required' => 'El precio es requerido',
            'price.integer' => 'El precio no es valido',
            'id.required' => 'El id de producto es requerido',
            'id.integer' => 'El id de producto no es valido',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>true,'message'=> $validator->errors()->toJson()], 400);
        }
        $product = Product::find($request->id);
        if(!$product){
            return response()->json(['status'=>true,'message'=> 'Producto no encontrado'], 400);
        }
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;

        if($product->save()){
            return response()->json(['status' => false, 'message' => 'producto actualizado exitosamente'],200);
        }else{
            return response()->json(['status' => true, 'message' => 'producto no pudo ser actualizado'],500);
        }
    }

    public function delete ($id){
        $product = Product::find($id);
        if(!$product){
            return response()->json(['status'=>true,'message'=> 'Producto no encontrado'], 400);
        }
        if($product->delete()){
            return response()->json(['status'=>false,'message'=>'Producto eliminado exitosamente']);
        }else{
            return response()->json(['status'=>false,'message'=>'No se pudo eliminar el producto'],500);

        }
    }
}
