<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Type;
use App\Product;
use App\Cart;
use Image;
use File;
use Session;

class TypesController extends Controller
{
    #types page
    public function types()
    {
        $types = Type::get();
        return view('dashboard.types.types',compact('types'));
    }

    #add
    public function addType(Request $request)
    {
        $this->validate($request,[
            'name_ar'     =>'required',
        ],[
            'name_ar.required' => 'الاسم مطلوب'
        ]);

        $add=new Type;
        $add->name_ar       =$request->name_ar;
        $add->save();

        Report(Auth::user()->id,'بأضافة نوع جديد');
        Session::flash('success','تم اضافة النوع');
        return back();
    }

    #update
    public function updateType(Request $request)
    {
        $this->validate($request,[
            'edit_name_ar'     =>'required|max:190',
        ],[
            'edit_name_ar.required' => 'الاسم مطلوب'
        ]);

        $edit=Type::findOrFail($request->id);
        $edit->name_ar       =$request->edit_name_ar;
        $edit->save();


        Report(Auth::user()->id,'بتحديث بيانات النوع '.$edit->name_ar);
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }

    #delete
    public function deleteType(Request $request)
    {

        $type = Type::findOrFail($request->id);
        // delete products under  this type
        $typeProducts = Product::where('type_id', $type->id)->get();
        if(count($typeProducts) > 0){
            foreach ($typeProducts as $typeProduct) {
                // delete this product from carts
                $productInCarts = Cart::where('product_id', $typeProduct->id)->get(); 
                if(count($productInCarts) > 0){
                    foreach ($productInCarts as $productInCart) {
                        $productInCart->delete();
                    }
                }
                $typeProduct->delete();
            }
        }
        $type->delete();

        Report(Auth::user()->id,'بحذف النوع الرئيسي '.$type->name_ar);
        Session::flash('success','تم الحذف');
        return back();

    }


}
