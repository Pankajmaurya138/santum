<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\SendSinupInvitionMail;
use App\Models\SendOtpVerficationMail;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\InviteRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyOtpRequest;
use Hash;
use Image;

class RegisterController extends BaseController
{

    /* login*/

    public function login(LoginRequest $request){
        try{
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password,'status'=>1])){ 
                $user = Auth::user(); 
                $data['token'] =  $user->createToken('MyApp')->plainTextToken; 
                $data['user'] =  $user;
                return $this->sendResponse($data, 'User login successfully.');
            } 
            else{ 
                return $this->sendError('Unauthorized',401);
            } 
        }catch(Exception $e){
            return $this->sendError('Something Went Wrong',500);
        }
    }

    public function sendInvitionEmail(InviteRequest $request){

        try{
            if(auth()->user()->user_role=='admin'){
                $email = $request->email;
                $check_email =User::where('email',$email)->first();
                if(!empty($check_email)){
                    return $this->sendError('User Already Register',200);
                }else{    
                    $create_user =User::create($request->all());  
                    $url = route('user.create_account').'?email='.$email;       
                    $details = [
                        'title' => 'Mail from Admin',
                        'body' => 'This email is regarding to create you account in our application',
                        'email' =>$email
                    ];
                    \Mail::to($email)->send(new \App\Mail\SendSinupInvitionMail($details));
                    return $this->sendResponse(['sinup_url'=>$url], 'Invition email send successfully.');
                }
            }else{
                return $this->sendError('Unauthorized',401);
            }

        }catch(Exception $e){
            return $this->sendError('Something Went Wrong',500);
        }
    }

    public function createAccount(RegisterRequest $request){
        
        $email = $request->email;
        try {
            if(!empty($email)){
                
               $check_user = User::where(['email'=>$email,'status'=>1])->first();
               
                if(empty($check_user)){
                    $otp = random_int(100000, 999999);
                    $update_user=User::where(['email'=>$email])->update([
                        'user_name'=>$request->user_name,
                        'password'=>Hash::make($request->password),
                        'user_role'=>'user',
                        'otp'=>$otp,
                        'registered_at'=>now(),
                    ]);
                    \Mail::to($email)->send(new \App\Mail\SendOtpVerficationMail($otp));
                    return $this->sendResponse([],'Verification code send to your email successfully.');
                }else{ 
                    return $this->sendError('User Already Registered',200);
                }
            }
        } catch (\Throwable $th) {
            return $this->sendError('Something Went Wrong',500);
        }
    }

    public function verifyOtp(VerifyOtpRequest $request){
        try {
            $email = $request->email;
            $otp=$request->otp;
            
            $user =$check_user = User::where(['email' => $email])->first();
            if(($user->otp == $otp) && (!empty($user))){
                    $data['token'] =  $user->createToken('MyApp')->plainTextToken; 
                    $data['user'] =  $user;
                    $check_user->update(['status'=>1,'otp'=>'','']);
                    return $this->sendResponse($data, 'User registered successfully.');
            }else{
                return $this->sendError('Invalid Otp Or Email address',500);
            } 
        }catch (\Throwable $th) {
            return $this->sendError('Something Went Wrong',500);
        }
    }

    public function profileUpdate(Request $request) {
       try {
            $input = $request->all();
            // dd($input);
            $user=$user_details = auth()->user();
            if(!empty($user)){
                if($request->hasFile('avatar')) {
                    //get filename with extension
                    $filenamewithextension = $request->file('avatar')->getClientOriginalName();
             
                    //get filename without extension
                    $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
             
                    //get file extension
                    $extension = $request->file('avatar')->getClientOriginalExtension();
             
                    //filename to store
                    $filenametostore = $filename.'_'.time().'.'.$extension;
             
                    //Upload File
                    $request->file('avatar')->storeAs('public/avatars', $filenametostore);
             
                    if(!file_exists(public_path('storage/avatars/crop'))) {
                        mkdir(public_path('storage/avatars/crop'), 0755);
                    }
             
                    // crop image
                    $img = Image::make(public_path('storage/avatars/'.$filenametostore));
                    $croppath = public_path('storage/avatars/crop/'.$filenametostore);
             
                    $img->crop($request->input('avatar_width'), $request->input('avatar_height'));
                    $img->save($croppath);
             
                    // you can save crop image path below in database
                    $path = asset('storage/avatars/crop/'.$filenametostore);
                }
                $user = $user->update([
                    'name'=>$input['name'],
                    'user_name'=>$input['user_name'],
                    'avatar'=>$path,
                ]);
                return $this->sendResponse($user_details, 'User profile updated successfully.');
            }else{
                return $this->sendError('Unauthorized',401);
            }

        }catch (\Throwable $th) {
            return $this->sendError('Something Went Wrong',500);
        }
    }
}
