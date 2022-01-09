<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderDetail;
use App\Cart;
use App\User;
use App\CartDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create (Request $request){

        $Validation = Validator::make($request->all(), [
            'cart_id' => 'required|integer',
        ],[
            'cart_id.required' => 'el carrito es requerido',
            'cart_id.integer' => 'el carrito no es valido',
        ]);
        if ($Validation->fails()) {
            return response()->json([
                'status' => true,
                'message' => $Validation->errors()->first()
            ], 400);
        }
        $cart = Cart::find($request->cart_id);
        $cartDetail = CartDetail::where('cart_id',$cart->id)->get();
        if($cart && $cartDetail){
            $total=0;
            $order = new Order();
            $order->user_id = $cart->user_id;
            $order->date = date("Y-m-d");
            if($order->save()){
                foreach($cartDetail as $detail){
                    $newOrderDetail = new OrderDetail();
                    $newOrderDetail->order_id = $order->id;
                    $newOrderDetail->product_id = $detail->product_id;
                    $newOrderDetail->quantity = $detail->quantity;
                    $newOrderDetail->product_price = $detail->product_price;
                    $newOrderDetail->save();
                    $total += ($detail->quantity*$detail->product_price);
                }
                $order->total_value = $total;
                $order->reference = '#' . str_pad($order->id, 8, "0", STR_PAD_LEFT);
                $order->save();
                return response()->json(['status'=>false,'message'=>'Orden generada exitosamente con numero de referencia '. $order->reference]);

            } 
        }else{
            return response()->json(['status' => true,'message' => 'El carrito no existe'],400);
        }

    }

    public function myOrders (Request $request){

        $Validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
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

        $orders = Order::where('user_id',$user->id)->with('getDetail.getProduct')->get();

        if($orders){
            return response()->json(['status' => false , 'data' => $orders]);
        }else{
            return response()->json(['status' => false, 'message' => 'No tienes ordenes registradas']);
        }
    }


}
