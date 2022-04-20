<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;

class AdminBannerController extends Controller
{    
    public function __construct()
    {
        $this->middleware('auth:admin');
        //$this->middleware('guest:admin');
    }
   
    public function banner(Request $request)
    {
        $banner = Banner::with('userInfo')
                ->orderBy('id','desc')
                ->get();  
        
       
        
        $result["banner"] = $banner;
        
        return view("admin.banner.list",$result);
    }    
    public function add()
    {
        $comapny = User::with('userInfo')
                ->where('rolle_id',2)
                ->where('is_deleted',0)
                ->where('is_verified',1)
                ->get();
        
        $data['comapny'] = $comapny;
       
        return view("admin.banner.add",$data);
    }

    public function addNewbanner(Request $req)
    {
        $messages = [
            'required' => 'This field is required',          
            'dimensions'  => 'The image has invalid image dimensions. file size must be 540 * 196',                 
            'mimes'  => 'The image has invalid file extesion must be png,jpg,jpeg,gif',                 
        ];
                
        $validator = $this->validate($req, [
            'app_banner' => 'required|dimensions:width=512,height=196|mimes:png,jpg,jpeg,gif',            
            'comapny' => 'required',            
        ],$messages);


        $extensions = array('gif','jpg','jpeg','png');  
        if($req->hasFile('app_banner') and in_array($req->app_banner->extension(), $extensions))
        {                
            $image = $req->app_banner;
            $fileName = time().'-'.$image->getClientOriginalName();
            $image->move('public/uploads/images/banner/', $fileName);
            $uploadImage = 'public/uploads/images/banner/'.$fileName;                
        }
        else
        {
            $uploadImage = 'public/uploads/default.png';
        }

        $banner= new Banner;
        $banner->app_banner = $uploadImage; 
        $banner->comapny_id = $req->comapny; 
        $banner->status = 1;            

        if($banner->save())
        {
            return redirect('admin/manage-banner')->with('success','New banner added successfully.');
        }
        else
        {
            return redirect('admin/manage-banner')->with('error','something went wrong.');
        }
    }
    public function edit($id)
    {
        $banner_data = Banner::where('id',$id)->first();
        $data["banner"] = $banner_data;
        $comapny = User::with('userInfo')
                ->where('rolle_id',2)
                ->where('is_deleted',0)
                ->where('is_verified',1)
                ->get();
        
        $data['comapny'] = $comapny;
        return view("admin.banner.edit",$data);
    }
    public function updatebanner(Request $req)
    {
        
        $messages = [
            'required' => 'This field is required',          
            'dimensions'  => 'The image has invalid image dimensions. file size must be 540 * 196',                 
            'mimes'  => 'The image has invalid file extesion must be png,jpg,jpeg,gif',                 
        ];
                
        $validator = $this->validate($req, [
            'app_banner' => 'required|dimensions:width=512,height=196|mimes:png,jpg,jpeg,gif',            
            'comapny' => 'required',            
        ],$messages);
            
        $extensions = array('gif','jpg','jpeg','png');  
        
        if($req->hasFile('app_banner') and in_array($req->app_banner->extension(), $extensions))
        {                
            $image = $req->app_banner;
            $fileName = time().'-'.$image->getClientOriginalName();
            $image->move('public/uploads/images/banner/', $fileName);
            $uploadImage = 'public/uploads/images/banner/'.$fileName;
        }
        else
        {
            $uploadImage = $req->old_image;
        }
            
        $banner_data = Banner::where('id',$req->id)->first();
        
        $banner_data->app_banner = $uploadImage;
        $banner_data->comapny_id = $req->comapny; 
        $banner_data->status = $req->status;

        if($banner_data->save())
        {
            return redirect('admin/manage-banner')->with('success',' Banner updated successfully.');
        }
        else
        {
            return redirect('admin/manage-banner')->with('success',' Banner updated successfully.');
        }
    }
    public function delete($id)
    {
        if(Banner::where('id',$id)->delete())
        {
             return redirect('admin/manage-banner')->with('success',' banner deleted successfully.');
        }
        else
        {
            return redirect('admin/manage-banner')->with('success','page deleted successfully.');
        }
        
    }
}
