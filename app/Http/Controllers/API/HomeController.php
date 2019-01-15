<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Type;
use App\User;
use App\Cart;
use App\Notification;
use App\SiteSetting;
use App\Image;
use App\Order;
use App\Orderdetail;
use Validator;
use Carbon\Carbon;

class HomeController extends Controller
{
     # all products
    public function allProducts()
    {
        $products = Product::all();
        if (count($products) > 0) {
            $data = [];
            foreach ($products as $product) {
                $data[] = [
                    'id' => $product->id,
                    'name' => $product->name_ar,
                    'provider' => $product->provider->name,
                    'price' => $product->price,
                    'offerPercentatge' => hasActiveOffer($product),
                    'stock' => $product->stock,
                    'logo' => url('public/dashboard\uploads\products' . '/' . $product->image),
                ];
            }
            return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
        } else {
            return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
        }
    }

    # all types for filter by id
    public function alltypes()
    {
        $types = Type::all();
        if (count($types) > 0) {
            $data = [];
            foreach ($types as $type) {
                $data[] = [
                    'id' => $type->id,
                    'name' => $type->name_ar,                    
                ];
            }
            return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
        } else {
            return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
        }
    }

    # products sorted (1 = high price , 2 = low price , 3 = most sale , 4 = by offer , null = all)
    public function productsSortWithFilter(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'sort_by'       => '',
            'type_id'       => '',
        ]);

        if ($validator->passes()) {
            if(request('type_id') != null){
                if(request('sort_by') == 1){
                    $products = Product::where('type_id', request('type_id'))->orderBy('price','desc')->get();
                }elseif(request('sort_by') == 2){
                    $products = Product::where('type_id', request('type_id'))->orderBy('price','asc')->get();
                }elseif(request('sort_by') == 3){
                    $products = Product::where('type_id', request('type_id'))->orderBy('sale_counter','desc')->get();
                }elseif(request('sort_by') == 4){
                    #update has_offer column
                    updateHasOffer();
                    #get data sorted
                    $products = Product::where('type_id', request('type_id'))->orderBy('has_offer','desc')->get();
                }else{
                    $products = Product::where('type_id', request('type_id'))->get();
                }

            }else{
                if(request('sort_by') == 1){
                    $products = Product::orderBy('price','desc')->get();
                }elseif(request('sort_by') == 2){
                    $products = Product::orderBy('price','asc')->get();
                }elseif(request('sort_by') == 3){
                    $products = Product::orderBy('sale_counter','desc')->get();
                }elseif(request('sort_by') == 4){
                    #update has_offer column
                    updateHasOffer();
                    #get data sorted
                    $products = Product::orderBy('has_offer','desc')->get();
                }else{
                    $products = Product::get();
                }
            }           

            if (count($products) > 0 ) {
                $data = [];
                foreach ($products as $product) {
                    $data[] = [
                        'id' => $product->id,
                        'name' => $product->name_ar,
                        'type' => $product->type->name_ar,
                        'provider' => $product->provider->name,
                        'price' => $product->price,
                        'offerPercentatge' => hasActiveOffer($product),
                        'stock' => $product->stock,
                        'logo' => url('public/dashboard\uploads\products' . '/' . $product->image),
                    ];
                }
                return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # show product details
    public function showProduct(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'product_id'       => 'required|exists:products,id',
        ]);
        if ($validator->passes()) {
            $product = Product::find(request('product_id'));
            if ($product) {
                    $images = Image::where('product_id', $product->id)->get();
                    $showImage = [];
                    $showImage[] = [
                        'url' => url('public/dashboard\uploads\products' . '/' . $product->image),
                    ];
                    foreach($images as $image){
                        $showImage[] = [
                            'url' => url('public/dashboard\uploads\products' . '/' . $image->image),
                        ];
                    }
                    $data = [
                        'id' => $product->id,
                        'name' => $product->name_ar,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'offerPercentatge' => hasActiveOffer($product),
                        'logo' => url('public/dashboard\uploads\products' . '/' . $product->image),
                        'otherImages' => $showImage

                    ];

                return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # add to cart
    public function addToCart(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'product_id'    => 'required|exists:products,id',
            'user_id'       => 'required|exists:users,id',
            'quantity'      => 'required|min:1',
        ]);
        if ($validator->passes()) {
            $product = Product::find(request('product_id'));
            $user = User::find(request('user_id'));

            if($user->is_provider == 1){
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'الحساب يخص بمندوب  ', 'code' => 401]);   
            }

            if(request('quantity') > $product->stock){
                $msg  = 'الكمية المتاحة هي ' . $product->stock;
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $msg , 'code' => 401]);
            }

            # check quantity with stock if add count to product exist in cart
            if(Cart::where('product_id', request('product_id'))->where('user_id', request('user_id'))->first() ){
                $edit = Cart::where('product_id', request('product_id'))->where('user_id', request('user_id'))->first();
                $edit->quantity = $edit->quantity + request('quantity');

                    if($edit->quantity > $product->stock){
                    $msg  = 'الكمية المتاحة هي ' . $product->stock;
                    return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $msg , 'code' => 401]);
                    }

                $edit->save();
                $msg = 'تمت الاضافة ';
                return response()->json(['key' => 'success', 'value' => 1, 'data' => $msg]);
            }
            # if product not in cart
            if ($product and $product->stock >= request('quantity')) {
                $add = new Cart();
                $add->product_id = request('product_id');
                $add->user_id = request('user_id');
                $add->quantity = request('quantity');
                $add->save();
                    
                $msg = 'تمت الاضافة ';
                return response()->json(['key' => 'success', 'value' => 1, 'data' => $msg]);
            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # delete from cart
    public function deleteFromCart(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'cart_id'    => 'required|exists:carts,id',
        ]);
        if ($validator->passes()) {
            $product = Cart::find(request('cart_id'));
            if ($product) {
                $product->delete();

                $cartItems = Cart::where('user_id',$product->user_id)->get();
                if (count($cartItems) > 0) {
                    $data = [];
                    $totalBeforeCharge = 0;
                    foreach ($cartItems as $cartItem) {

                        $disc = hasActiveOffer($cartItem->product);
                        $p = $cartItem->product->price;
                        $lastP = $disc != ""? $p - ($p * $disc / 100) : $p;
                        $q = $cartItem->quantity;

                        $totalBeforeCharge += $q * $lastP;
                        $data[] = [
                            'id'            => $cartItem->id,
                            'product_id'    => $cartItem->product->id,
                            'name'          => $cartItem->product->name_ar,
                            'price'         => $cartItem->product->price,
                            'offerPercentatge' => hasActiveOffer($cartItem->product),
                            'quantity'      => $cartItem->quantity,
                            'stock'         => $cartItem->product->stock,
                            'logo'          => url('public/dashboard\uploads\products' . '/' . $cartItem->product->image),
                        ];
                    }
                    $charge =  SiteSetting::find(1)->charge;
                    $total = $totalBeforeCharge + $charge;
                    return response()->json(['key' => 'success', 'value' => 1, 'data' => $data,'charge' => $charge,'total' => $total]);
                } else {
                    return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
                }

            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # show my cart
    public function showCart(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
        ]);
        if ($validator->passes()) {
            $user = User::find(request('user_id'));
            if($user->is_provider == 1){
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'الحساب يخص بمندوب ', 'code' => 401]);   
            }
            $cartItems = Cart::where('user_id',request('user_id'))->get();
            if (count($cartItems) > 0) {
                $data = [];
                $totalBeforeCharge = 0;
                foreach ($cartItems as $cartItem) {
                   
                    $disc = hasActiveOffer($cartItem->product);
                    $p = $cartItem->product->price;
                    $lastP = $disc != ""? $p - ($p * $disc / 100) : $p;
                    $q = $cartItem->quantity;

                    $totalBeforeCharge += $q * $lastP;
                    $data[] = [
                        'id'            => $cartItem->id,
                        'product_id'    => $cartItem->product->id,
                        'name'          => $cartItem->product->name_ar,
                        'price'         => $cartItem->product->price,
                        'offerPercentatge' => hasActiveOffer($cartItem->product),
                        'quantity'      => $cartItem->quantity,
                        'stock'         => $cartItem->product->stock,
                        'logo'          => url('public/dashboard\uploads\products' . '/' . $cartItem->product->image),
                    ];
                }
                $charge =  SiteSetting::find(1)->charge;
                $total = $totalBeforeCharge + $charge;
                return response()->json(['key' => 'success', 'value' => 1, 'data' => $data,'charge' => $charge,'total' => $total]);
            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => []]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # order from cart or product direct
    public function makeOrder(Request $request)
    {
        $validator          = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
            'total_price'   => 'required',
            'data'          => 'required',
            'charge'        => 'required',
            'lat'           => 'required',
            'lng'           => 'required',
        ]);
        if ($validator->passes()) {
            $user = User::find(request('user_id'));
            if($user->is_provider == 1){
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'الحساب يخص مندوب  ', 'code' => 401]);   
            }
            if($user->active == 0){
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'الحساب بانتظار التفعيل  ', 'code' => 401]);   
            }

            # make new order
            $newOrder = new Order();
            $newOrder->user_id      = request('user_id');
            $newOrder->charge       = request('charge');
            $newOrder->totalPrice   = request('total_price');
            $newOrder->lat          = request('lat');
            $newOrder->lng          = request('lng');
            $newOrder->save();

            #make order details         
            foreach ( json_decode(request('data')) as $product ) {

                $productInTable = Product::find($product->product_id);

                $newOrderDetails = new Orderdetail();
                $newOrderDetails->order_id = $newOrder->id;
                $newOrderDetails->user_id = request('user_id');
                $newOrderDetails->product_id = $product->product_id;
                $newOrderDetails->quantity = $product->quantity;
                $newOrderDetails->price = $product->price;
                $newOrderDetails->provider_id= $productInTable->user_id;
                $newOrderDetails->save();

                # update sale counter and stock in product table
                $productInTable->sale_counter += $product->quantity;
                $productInTable->stock -= $product->quantity;
                $productInTable->save();
            }

            # add notify to notifications
            $notify = new Notification();
            $notify->title = ' استلام الطلب';
            $notify->body = '  تم استلام طلب الشراء الخاص بك برقم ' . $newOrder->id;
            $notify->user_id = $user->id;
            $notify->order_id = $newOrder->id;
            $notify->save();

            # empty cart
            $userCartProducts = Cart::where('user_id',request('user_id'))->get();
            foreach ($userCartProducts as $userCartProduct){
                $userCartProduct->delete();
            }

            $data = 'تمت الاضافة';
            return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
        
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # re buy same order
    public function reBuyOldOrder(Request $request)
    {
        $validator          = Validator::make($request->all(), [
            'order_id'      => 'required|exists:orders,id',
            'lat'           => 'required',
            'lng'           => 'required',
        ]);
        if ($validator->passes()) {
            $oldOrder = Order::find(request('order_id'));
            $oldOrderdetails = Orderdetail::where('order_id', request('order_id'))->get();

            # make new order
            $newOrder = new Order();
            $newOrder->user_id = $oldOrder->user_id;
            $newOrder->charge = $oldOrder->charge;
            $newOrder->totalPrice = $oldOrder->totalPrice;
            $newOrder->lat = $request->lat;
            $newOrder->lng = $request->lng;
            $newOrder->save();

            #make order details         
            foreach ( $oldOrderdetails as $oldOrderdetail) {

                $productInTable = Product::find($oldOrderdetail->product_id);

                $newOrderDetails = new Orderdetail();
                $newOrderDetails->order_id = $newOrder->id;
                $newOrderDetails->user_id = $oldOrderdetail->user_id;
                $newOrderDetails->product_id = $oldOrderdetail->product_id;
                $newOrderDetails->quantity = $oldOrderdetail->quantity;
                $newOrderDetails->price = $oldOrderdetail->price;
                $newOrderDetails->provider_id= $oldOrderdetail->provider_id;
                $newOrderDetails->save();

                # update sale counter and stock in product table
                $productInTable->sale_counter += $newOrderDetails->quantity;
                $productInTable->stock -= $newOrderDetails->quantity;
                $productInTable->save();
            }

            # add notify to notifications
            $notify = new Notification();
            $notify->title = ' استلام الطلب';
            $notify->body = '  تم استلام طلب الشراء الخاص بك برقم ' . $newOrder->id;
            $notify->user_id = $oldOrder->user_id;
            $notify->order_id = $newOrder->id;
            $notify->save();

            $data = 'تمت الاضافة';
            return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
        
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # show my bills / orders
    public function myBills(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
        ]);     
        if ($validator->passes()) {
            $user = User::find(request('user_id'));
            if($user->is_provider == 1){
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'الحساب يخص مندوب  ', 'code' => 401]);   
            }
            $orders = Order::where('user_id',request('user_id'))->where('finish',1)->where('status',0)->get();
            if (count($orders) > 0) {
            $data = [];
            foreach ($orders as $order) {
                $data[] = [
                    'billNo'        => $order->id,
                    'by'            => User::find(1)->name                    
                 ];
            }
            return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
        } else {
            return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
        }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # show order details / bill details
    public function billdetail(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'order_id'       => 'required|exists:orders,id',
        ]);
        if ($validator->passes()) {
            $orders = Orderdetail::where('order_id',request('order_id'))->get();
            if (count($orders) > 0) {
            $data = [];
            foreach ($orders as $order) {
                $data[] = [
                    'product_id'    => $order->product_id,
                    'product_name'  => $order->product->name_ar,
                    'price'         => $order->price,
                    'quantity'      => $order->quantity,
                    'logo'          => url('public/dashboard\uploads\products' . '/' . $order->product->image),
                 ];
            }
            return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
        } else {
            return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
        }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # show unfinished orders for delivery
    public function unfinishedOrders()
    {
        $orders = Order::where('finish',0)->get();
        if (count($orders) > 0) {
            $data = [];
            foreach ($orders as $order) {
                $data[] = [
                    'orderNo' => $order->id,
                    'orderDate' => Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)->format('Y-m-d'),
                    'totalPrice' => $order->totalPrice,                    
                ];
            }
            return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
        } else {
            return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
        }
    }

    # order bill
    public function orderBill(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'order_id'       => 'required|exists:orders,id',
        ]);
        if ($validator->passes()) {
            $order = Order::find(request('order_id'));
            $owner = User::find(1);
            if ($order){
                $data = [
                    'providerImage'     => url('public/dashboard/uploads/users/'.$owner->avatar),
                    'providerName'      => $owner->name,
                    'price'             => $order->totalPrice - $order->charge,
                    'charge'            => $order->charge,
                    'totalPrice'        => $order->totalPrice,
                    'lat'               => $order->lat,
                    'lng'               => $order->lng,
                    'date'              => Carbon::createFromFormat('Y-m-d H:i:s',
                                             $order->created_at)->format('Y-m-d'),
                    'time'              => Carbon::createFromFormat('Y-m-d H:i:s',
                                             $order->created_at)->format('H:i:s'),
                    
                 ];
            return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
        } else {
            return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
        }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # take order
    public function takeOrder(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'order_id'       => 'required|exists:orders,id',
            'user_id'       => 'required|exists:users,id',
        ]);
        if ($validator->passes()) {
            $user = User::find(request('user_id'));
            if($user->is_provider == 0){
                $msg  = 'الحساب خاص بعميل  ';
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $msg , 'code' => 401]);
            }
            $order = Order::find(request('order_id'));
            if ($order and $order->finish == 0) {
                $order->finish = 1;
                $order->delivery_id = request('user_id');
                $order->finish_date = Carbon::now();
                $order->save();

                # add notify to notifications
                $notify = new Notification();
                $notify->title = ' توصيل الطلب ';
                $notify->body = '  لقد استلم مندوب التوصيل طلب الشراء الخاص بكم رقم ' . $order->id;
                $notify->user_id = $order->user_id;
                $notify->order_id = $order->id;
                $notify->save();

                $data = " تم ";
                return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # show finished order for one provider
    public function myFinishOrder(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
        ]);        
        if ($validator->passes()) {
            $user = User::find(request('user_id'));
            if($user->is_provider == 0){
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'الحساب يخص عميل  ', 'code' => 401]);   
            }
            $orders = Order::where('delivery_id',request('user_id'))->where('finish',1)->get();
            if (count($orders) > 0) {
                $data = [];
                foreach ($orders as $order) {
                    $data[] = [
                        'orderNo' => $order->id,
                        'finishDate' => $order->finish_date,
                        'totalPrice' => $order->totalPrice,                    
                     ];
                }
                return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # order delete
    public function deleteOrder(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'order_id'       => 'required|exists:orders,id',
        ]);

        if ($validator->passes()) {
            $order = Order::find(request('order_id'));
            if ($order){
                $order->status = 1;
                $order->save();

                return response()->json(['key' => 'success', 'value' => 1, 'data' => 'تم حذف الطلب']);
            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # show notification for user
    public function showNotifications(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
        ]);
        if ($validator->passes()) {

            $notifications = Notification::where('user_id',request('user_id'))->get();
            if (count($notifications) > 0) {
                $data = [];
                foreach ($notifications as $notification) {
                    $data[] = [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'body' => $notification->body,
                        'user_id' => $notification->user_id,
                        'order_id' => $notification->order_id,
                    ];
                }
                return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    # notify delete
    public function deleteNotify(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'notify_id'       => 'required',
        ]);

        if ($validator->passes()) {
            $notify = Notification::find(request('notify_id'));
            if ($notify){
                $notify->delete();
                return response()->json(['key' => 'success', 'value' => 1, 'data' => 'تم حذف الاشعار']);
            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }
    
}
