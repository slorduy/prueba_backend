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
    //add cart function 

    public function add(Request $request){

       //validate request 
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

        //Valid existing user and product ids

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

            // Create a new cart and cart Details for the selected products 
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
            //Validate product in the cart
            $c = Cart::whereHas('getDetails',function ($q) use ($product){
                $q->where('cart_detail.product_id', $product->id);
            })->where('user_id',$user->id)->first();
            if (is_null($c)){
                //If the product didnt add in the cart, create a new cartDetail with the product informatio
                $newdetail = new CartDetail();
                $newdetail->cart_id = $cart->id;
                $newdetail->product_id = $product->id;
                $newdetail->product_price = $product->price;
                $newdetail->quantity = $request->quantity;
                $newdetail->save();
            }else{
               //If the product is added in the cart, modify the  cartDetail with the new product quantity
                $cd = CartDetail::where('cart_id',$cart->id)->where('product_id',$product->id)->first();
                $cd->quantity = $cd->quantity+$request->quantity;
                $cd->save();
            }
        }

        $cart_list= Cart::where('user_id',$request->user_id)->with('getDetails.getProduct')->first();
        return response()->json(['status' => false,'data'=>$cart_list]);
    }


    //update a existing product in the cart
    public function update(Request $request){

        //validate request

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

        //Validate existing product and user id
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
            //get the detail with the product in the cart and then update de quantity with the information
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

    //Get the current cart of specifid user
    public function myCar(Request $request){

        //validate request

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
        
        //validate existing user id
        $user = User::find($request->user_id);
        if(!$user){
            return response()->json([
                'status' => true,
                'message' => 'El usuario no existe'
            ], 400);
        } 

        //get the cart of the user
        $cart_list= Cart::where('user_id',$user->id)->with('getDetails.getProduct')->first();
        if (!$cart_list){
            return response()->json(['status'=>false,'data'=>[]]);
        }else{
            return response()->json(['status'=>false,'data'=>$cart_list]);
        }

    }

    //remove a product in the cart

    public function remove(Request $request){

        //validate request 

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
            //get the detail associated with the product 
            $cd = CartDetail::where('product_id',$product->id)->where('cart_id',$cart->id)->first();
            if($cd){
                //delete the detail
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

    //Remove all cart items
    
    public function delete(Request $request){

        //validate request

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

        //Validate existing user id
        $user = User::find($request->user_id);
        if(!$user){
            return response()->json([
                'status' => true,
                'message' => 'El usuario no existe'
            ], 400);
        } 
        $cart= Cart::where('user_id',$user->id)->with('getDetails')->first();
        if ($cart){
            //Get and delete all associated product in the cart
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

