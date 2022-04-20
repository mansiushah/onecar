<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\UserDetails;
use App\Models\Services;
use App\Models\User;
use App\Mail\VerifyStatus;
use Mail;

class AdminCompanyController extends Controller
{
    
    public function __construct()
    {
       $this->middleware('auth:admin');
        //$this->middleware('guest:admin');
    }
   
    public function company(Request $request)
    {
        $data = User::with('userInfo')
                ->where('profile_update','=',1)
                ->where('rolle_id','=',2)
                ->where('is_deleted','=',0)
                ->orderBy('id','desc')                
                ->paginate(10);

        $result["user_list"] = $data;

        return view("admin.company_profile.list",$result);
    }

    public function edit($id, Request $req)
    {
        $data = User::with('userInfo')
            ->with('services_info')    
            ->with('opening_hours')
            ->where('id',$id)                               
            ->first();
      
        $result["row_data"] = $data;
        return view("admin.company_profile.edit",$result);
    }
    
    public function updateCompanyStatus(Request $request)
    {
        
        $service=User::find($request->id);
        $service->is_verified=$request->varification;        
        if($service->save())
        {
            return redirect('admin/manage-company')->with('success','Successfully profile update...'); 
        }
        else
        {
            return redirect('admin/manage-company')->with('success','Successfully profile update...'); 
        }
    }
    
}


