<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Type;
use App\Product;
use App\Cart;
use App\Offer;
use Image;
use File;
use Session;

class ProductsController extends Controller
{

    public function __construct()
    {
        updateHasOffer();
    }


    #cities page
    public function products()
    {
        $types = Type::get();
        $products = Product::get();
        return view('dashboard.products.products',compact('products','types'));
    }

    #add
    public function addProduct(Request $request)
    {
        $this->validate($request,[
            'type_id'                           =>'required|exists:types,id',
            'name_ar'                           =>'required',
            'price'                             =>'required|numeric',
            'stock'                             =>'required|numeric',
            'image'                             =>'required|image|mimes:jpeg,png,jpg,gif,svg',
        ],[
            'type_id.required'                  => 'النوع مطلوب',
            'name_ar.required'                  => 'الاسم مطلوب',
            'price.required'                    => 'السعر مطلوب',
            'price.numeric'                     => 'السعر يجب ان يكون رقم',
            'stock.numeric'                     => 'الكمية يجب ان تكون رقم',
            'stock.required'                    => 'الكمية مطلوبة',
            'image.required'                    => 'الصورة مطلوبة',
            'image.image'                       => 'النوع صورة',
            'image.mimes:jpeg,png,jpg,gif,svg'  => 'jpeg,png,jpg,gif,svg الصورة مطلوبة نوع ',
        ]);

        $add=new Product;
        $add->type_id       =$request->type_id;
        $add->name_ar       =$request->name_ar;
        $add->price         =$request->price;
        $add->stock         =$request->stock;
        $add->user_id       =1;

        if(request('image'))
        {
            $image = request('image');
            $name=date('d-m-y').time().rand()  .'.'.$image->getClientOriginalExtension();
            $image->move('public/dashboard/uploads/products/', $name);

            $add->image = $name;
        }

        $add->save();

        Report(Auth::user()->id,'  بأضافة منتج جديد' .$add->name_ar);
        Session::flash('success','تم اضافة المنتج');
        return back();
    }

    #update
    public function updateProduct(Request $request)
    {
        $this->validate($request,[
            'edit_type_id'       =>'required|exists:types,id',
            'edit_name_ar'       =>'required',
            'edit_price'         =>'required|numeric',
            'edit_stock'         =>'required|numeric',
            'edit_image'         =>'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ],[
            'edit_type_id.required'=> 'النوع مطلوب',
            'edit_name_ar.required'=> 'الاسم مطلوب',
            'edit_price.required'=> 'السعر مطلوب',
            'edit_stock.required'=> 'الكمية مطلوبة',
            'edit_price.numeric'=> 'السعر يجب ان يكون رقم',
            'edit_stock.numeric'=> 'الكمية يجب ان تكون رقم',
            'image.image'=> 'الصورة يجب ان تكون من نوع صورة',
            'image.mimes:jpeg,png,jpg,gif,svg'=> 'jpeg,png,jpg,gif,svg الصورة مطلوبة نوع ',
        ]);

        $update =Product::findOrFail($request->id);
        $update->type_id    =$request->edit_type_id;
        $update->name_ar    =$request->edit_name_ar;
        $update->price      =$request->edit_price;
        $update->stock      =$request->edit_stock;

        if(request('edit_image'))
        {
            if($update->image != null)
            {
                File::delete('public/dashboard/uploads/products/'.$update->image);
            }

            $image = request('edit_image');
            $name=date('d-m-y').time().rand()  .'.'.$image->getClientOriginalExtension();
            $image->move('public/dashboard/uploads/products/', $name);

            $update->image = $name;

        }
        $update->save();


        Report(Auth::user()->id,'بتحديث بيانات المنتج  '.$update->name_ar);
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }

    #delete
    public function deleteProduct(Request $request)
    {

        $product = Product::findOrFail($request->id);

            // delete this product from carts
            $productInCarts = Cart::where('product_id', $product->id)->get(); 
            if(count($productInCarts) > 0){
                foreach ($productInCarts as $productInCart) {
                    $productInCart->delete();
                }
            }

         # stop delete image coz of soft delete
        //File::delete('public/dashboard/uploads/products/'.$product->image);
        $product->delete();

        Report(Auth::user()->id,'بحذف المنتج '.$product->name_ar);
        Session::flash('success','تم الحذف');
        return back();

    }



    #add offer and delete old
    public function addOffer(Request $request)
    {
        $this->validate($request,[
            'product_id'                           =>'required',
            'percentage'                           =>'required|numeric',
            'start_date'                           =>'required|after:yesterday',
            'end_date'                             =>'required|after_or_equal:start_date',
        ],[
            'product_id.required'                  => 'المنتج مطلوب',
            'percentage.required'                  => 'نسبة الخصم مطلوبة',
            'percentage.numeric'                   => 'نسبة الخصم يجب ان تكون رقم فقط',
            'start_date.required'                  => ' تاريخ بداية الخصم مطلوب',
            'start_date.after'                     => ' التاريخ قديم',
            'end_date.required'                    => 'تاريخ نهاية الخصم مطلوب',
            'end_date.after_or_equal'              => 'تاريخ نهاية الخصم يجب ان يلى او يساوى تاريخ بداية الخصم',
        ]);

        # if old offers - delete then add new (relation has many for future)
        $oldOffers = Offer::where('product_id',request('product_id'))->get();
        if(count($oldOffers) > 0){
            foreach ($oldOffers as $oldOffer){
                $oldOffer->delete();
            }
        }

        $add=new Offer;
        $add->product_id       =$request->product_id;
        $add->percentage       =$request->percentage;
        $add->start_date       =$request->start_date;
        $add->end_date         =$request->end_date;
        $add->save();

        $product = Product::findOrFail(request('product_id'));
        Report(Auth::user()->id,'  بأضافة عرض جديد للمنتج '. $product->name_ar);
        Session::flash('success','تم اضافة العرض ');
        return back();
    }


}
