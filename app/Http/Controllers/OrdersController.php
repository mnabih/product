<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Order;
use App\Orderdetail;
use App\Product;
use Image;
use File;
use Session;

class OrdersController extends Controller
{
    #orders page
    public function orders($id = null)
    {
        if($id == 1){
            $orders = Order::where('finish',1)->where('owner_delete',0)->get();
        }elseif($id == 0 && $id != null){
            $orders = Order::where('finish',0)->where('owner_delete',0)->get();
        }else{
            $orders = Order::where('owner_delete',0)->get();
        }

        $allOrders = Order::get();
        $finishedOrders = Order::where('finish',1)->get();
        $unfinishedOrders = Order::where('finish',0)->get();

        return view('dashboard.orders.orders',compact('orders','finishedOrders','unfinishedOrders','id','allOrders'));
    }

    #show order details
    public function showOrder($id)
    {
        $orderDetails = Orderdetail::where('order_id', $id)->get();
        return view('dashboard.orders.orderDetails',compact('orderDetails','id'));
    }

    # soft delete order and order details
    public function deleteOrder(Request $request)
    {

        $order = Order::findOrFail($request->id);
        if($order and $order->finish == 1){
            $order->owner_delete =1;
            $order->save();
        }else{
            $orderDetails = Orderdetail::where('order_id',$order->id)->get();

            foreach ($orderDetails as $orderDetail){

                # update stock and sales in product table
                $product = Product::findOrFail($orderDetail->product_id);
                $product->stock += $orderDetail->quantity;
                $product->sale_counter -= $orderDetail->quantity;
                $product->save();

                $orderDetail->delete();
            }
            $order->delete();
        }

        

        Report(Auth::user()->id,'بحذف الطلب رقم  '.$order->id);
        Session::flash('success','تم الحذف');
        return back();

    }




}
