<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        \Session::forget('vc_user_system');
        return redirect()->route('login');
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
         //Validaciones 

        $validatedData = $request->validate([
            
            'vc_email' => 'required|max:255|email',
            'vc_password' => 'required|max:255',
          
             ]);
    
         //Obteniendo variables post
   
         $vc_email=$request->input('vc_email');
         $vc_password=$request->input('vc_password');

        //Buesqueda en BD
        
        $system_information=DB::select('SELECT vc_user, i_type , id_team_member FROM cat_user_system WHERE  vc_email = ? AND  vc_password = md5(?) AND b_status=1',[ $vc_email,$vc_password  ]);
        //$vc_user_system= $system_information[0]->vc_user;

        if(empty($system_information[0]->vc_user)){
        
            return redirect()->route('login')-> with('lg_error','Datos de usuario incorrectos');

        } else {
            $vc_user_system= $system_information[0]->vc_user;
            $i_type_system= $system_information[0]->i_type;
            $id_team_member = $system_information[0]->id_team_member;

            \Session::put('vc_user_system', $vc_user_system);
            \Session::put('i_type_system', $i_type_system);
            \Session::put('id_team_member', $id_team_member);
                
        return redirect()->route('dashboard');
        

        }
   
    // redirect()->route('login.store')-> with('status','Los datos del cliente '.utf8_decode($vc_name).' '.utf8_decode($vc_last_name).' fueron guardados correctamente');


    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
    }
}
