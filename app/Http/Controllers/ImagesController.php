<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Product;
use App\Image;
use File;
use Session;

class ImagesController extends Controller
{
    #cities page
    public function images($id)
    {
        $product = Product::find($id);
        $images = Image::where('product_id', $id)->get();
        return view('dashboard.products.images',compact('images','product'));
    }

    #add
    public function addImage(Request $request)
    {
        $this->validate($request,[
            'image'     =>'required|image|mimes:jpeg,png,jpg,gif,svg',
            'product_id' => 'required',
        ],[
            'image.required' => 'الصورة مطلوبه',
        ]);

        $add=new Image;
        $add->product_id       =$request->product_id;

        if(request('image'))
        {
            $image = request('image');
            $name=date('d-m-y').time().rand()  .'.'.$image->getClientOriginalExtension();
            $image->move('public/dashboard/uploads/products/', $name);

            $add->image = $name;
        }

        $add->save();

        $product = Product::find($request->product_id);
        Report(Auth::user()->id,' بأضافة صورة جديدة للمنتج - ' . $product->name_ar );
        Session::flash('success','تم اضافة الصورة');
        return back();
    }


    #delete
    public function deleteImage(Request $request)
    {
        $this->validate($request,[
            'id'     =>'required',
        ],[
            'id.required' => 'يجب اختيار صورة',
        ]);

        foreach (request('id') as $id){

            $image = Image::findOrFail($id);
            File::delete('public/dashboard/uploads/products/'.$image->image);
            $image->delete();
        }



        Report(Auth::user()->id,'بحذف صور منتج');
        Session::flash('success','تم الحذف');
        return back();

    }


}
