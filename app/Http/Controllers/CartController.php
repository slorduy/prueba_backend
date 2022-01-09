<?php

namespace App\Http\Controllers;

use App\Product;
use App\User;
use App\Cart;
use App\CartDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CartController extends Controller
{
    public function add(Request $request){
        $Validation = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|integer',
            'user_id' => 'required|integer'
        ],[
            'product_id.required' => 'el producto es requerido',
            'user_id.required' => 'el usuario es requerido',
            'product_id.integer' => 'el producto no es valido',
            'user_id.integer' => 'el usuario no es valido',
            'quantity.integer' => 'la cantidad no es valida',
            'quantity.required' => 'la cantidad es requerida',
        ]);

        if ($Validation->fails()) {
            return response()->json([
                'status' => true,
                'message' => $Validation->errors()->first()
            ], 400);
        }
        $user = User::find($request->user_id);

        $product = Product::find($request->product_id);

        if(!$user){
            return response()->json([
                'status' => true,
                'message' => 'El usuario no existe'
            ], 400);
        }    

        if(!$product){
            return response()->json([
                'status' => true,
                'message' => 'El producto no existe'
            ], 400);
        }    

        $cart = Cart::where('user_id',$request->user_id)->first();
        if (!$cart){
            $newcart = new Cart();
            $newcart->user_id = $user->id;
            if ($newcart->save()){
                $newdetail = new CartDetail();
                $newdetail->cart_id = $newcart->id;
                $newdetail->product_id = $product->id;
                $newdetail->product_price = $product->price;
                $newdetail->quantity = $request->quantity;
                $newdetail->save();
            }
        }else{
            $c = Cart::whereHas('getDetails',function ($q) use ($product){
                $q->where('cart_detail.product_id', $product->id);
            })->where('user_id',$user->id)->first();
            if (is_null($c)){
                $newdetail = new CartDetail();
                $newdetail->cart_id = $cart->id;
                $newdetail->product_id = $product->id;
                $newdetail->product_price = $product->price;
                $newdetail->quantity = $request->quantity;
                $newdetail->save();
            }else{
                $cd = CartDetail::where('cart_id',$cart->id)->where('product_id',$product->id)->first();
                $cd->quantity = $cd->quantity+$request->quantity;
                $cd->save();
            }
        }
        $cart_list= Cart::where('user_id',$request->user_id)->with('getDetails.getProduct')->first();
        return response()->json(['status' => false,'data'=>$cart_list]);
    }

    public function update(Request $request){
        $Validation = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|integer',
            'user_id' => 'required|integer'
        ],[
            'product_id.required' => 'el producto es requerido',
            'user_id.required' => 'el usuario es requerido',
            'product_id.integer' => 'el producto no es valido',
            'user_id.integer' => 'el usuario no es valido',
            'quantity.integer' => 'la cantidad no es valida',
            'quantity.required' => 'la cantidad es requerida',
        ]);

        if ($Validation->fails()) {
            return response()->json([
                'status' => true,
                'message' => $Validation->errors()->first()
            ], 400);
        }

        $user = User::find($request->user_id);

        if(!$user){
            return response()->json([
                'status' => true,
                'message' => 'El usuario no existe'
            ], 400);
        }    

        $product = Product::find($request->product_id);

        if(!$product){
            return response()->json([
                'status' => true,
                'message' => 'El producto no existe'
            ], 400);
        }    

        $cart = Cart::where('user_id',$request->user_id)->first();
        if ($cart){
            $cd = CartDetail::where('product_id',$product->id)->where('cart_id',$cart->id)->first();
            $cd->quantity =  $request->quantity;
            if ($cd->save()){
                $cart_list= Cart::where('user_id',$user->id)->with('getDetails.getProduct')->first();
                return response()->json(['status'=>false,'data'=>$cart_list]);
            }
        }else{
            return response()->json(['status'=> true,'Carrito no encontrado'],400);
        }

    }

    public function myCar(Request $request){
        $Validation = Validator::make($request->all(), [
            'user_id' => 'required|integer'
        ],[
            'user_id.required' => 'el usuario es requerido',
            'user_id.integer' => 'el usuario no es valido'
        ]);

        if ($Validation->fails()) {
            return response()->json([
                'status' => true,
                'message' => $Validation->errors()->first()
            ], 400);
        }
        
        $user = User::find($request->user_id);
        if(!$user){
            return response()->json([
                'status' => true,
                'message' => 'El usuario no existe'
            ], 400);
        } 


        $cart_list= Cart::where('user_id',$user->id)->with('getDetails.getProduct')->first();
        if (!$cart_list){
            return response()->json(['status'=>false,'data'=>[]]);
        }else{
            return response()->json(['status'=>false,'data'=>$cart_list]);
        }

    }

    public function remove(Request $request){
        $Validation = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'user_id' => 'required|integer'
        ],[
            'product_id.required' => 'el producto es requerido',
            'user_id.required' => 'el usuario es requerido',
            'product_id.integer' => 'el producto no es valido',
            'user_id.integer' => 'el usuario no es valido',
        ]);

        if ($Validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $Validation->errors()->first()
            ], 400);
        }

        $user = User::find($request->user_id);
        if(!$user){
            return response()->json([
                'status' => true,
                'message' => 'El usuario no existe'
            ], 400);
        } 
        $product = Product::find($request->product_id);

        if(!$product){
            return response()->json([
                'status' => true,
                'message' => 'El producto no existe'
            ], 400);
        } 
        $cart = Cart::where('user_id',$user->id)->first();
        if ($cart){
            $cd = CartDetail::where('product_id',$product->id)->where('cart_id',$cart->id)->first();
            if($cd){
                $cd->delete();
            }else{
                return response()->json([
                    'status' => true,
                    'message' => 'El producto no existe en el carrito'
                ], 400);
            }
            $cart_list= Cart::where('user_id',$user->id)->with('getDetails.getProduct')->first();
            return response()->json(['status'=>false ,'data'=>$cart_list]);
        }else{
            return response()->json(['status'=> true,'El producto no fue encontrado'],400);
        }

    }
    
    public function delete(Request $request){
        $Validation = Validator::make($request->all(), [
            'user_id' => 'required|integer'
        ],[
            'user_id.required' => 'el usuario es requerido',
            'user_id.integer' => 'el usuario no es valido',
        ]);

        if ($Validation->fails()) {
            return response()->json([
                'status' => true,
                'message' => $Validation->errors()->first()
            ], 400);
        }

        $user = User::find($request->user_id);
        if(!$user){
            return response()->json([
                'status' => true,
                'message' => 'El usuario no existe'
            ], 400);
        } 
        $cart= Cart::where('user_id',$user->id)->with('getDetails')->first();
        if ($cart){
            $cd = CartDetail::where('cart_id',$cart->id)->get();
            if($cd){
                foreach ($cd as $c){
                    $c->delete();
                }
            }
            $cart_list= Cart::where('user_id',$request->user_id)->with('getDetails')->first();
            return response()->json(['status'=> false , 'data'=>$cart_list]);
        }else{
            return response()->json(['status'=> true ,'Carrito no encontrado']);
        }


    }
}

