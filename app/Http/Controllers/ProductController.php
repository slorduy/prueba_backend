<?php

namespace App\Http\Controllers;

use App\Product;
use App\Imports\ProductImport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;

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
            return response()->json(['status'=>true,'message'=> $validator->errors()->first()], 400);
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
            return response()->json(['status'=>true,'message'=> $validator->errors()->first()], 400);
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

    public function delete ($product_id){

    
        $product = Product::find($product_id);
        if(!$product){
            return response()->json(['status'=>true,'message'=> 'Producto no encontrado'], 400);
        }
        if($product->delete()){
            return response()->json(['status'=>false,'message'=>'Producto eliminado exitosamente']);
        }else{
            return response()->json(['status'=>false,'message'=>'No se pudo eliminar el producto'],500);

        }
    }

    public function storeFromCsv (Request $request){
  
        $validator = Validator::make($request->all(), [
            'products' => 'required|mimes:csv,txt'
        ],[
            'products.required' => 'El archivo csv es requerido',
            'products.mimes' => 'El formato del archivo no es valido',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>true,'message'=> $validator->errors()->first()], 400);
        }

        $collection = Excel::toArray(new ProductImport, request()->file('products'));
        $success = 0;
        $error = 0;
        if(count($collection[0])< 1){
            return response()->json(['status'=>true,'message'=> 'Archivo vacio. No hay productos para agregar'], 400);
        }
        foreach($collection[0] as $key => $product){
            if(!is_null($product['name']) && is_int($product['price']) && !is_null($product['description'])){
                $success += 1;
                $newProduct = new Product();
                $newProduct->name = $product['name'];
                $newProduct->description = $product['description'];
                $newProduct->price = $product['price'];
                $newProduct->save();
            }else{
                $error += 1;
            }
        }

        return  $error==0 ? response()->json(['success' => false , 'message' => 'Productos agregados exitosamente']) : response()->json(['status'=>false,'message' => $success.' de '.($success+$error).' productos agregados exitosamente. '.$error.' productos con errores']);

    }
}
