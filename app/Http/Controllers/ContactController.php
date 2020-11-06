<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Propiedades de Navegacion
        $branch='1';
        $type_menu='3';
        $user_system =  \Session::get('vc_user_system');
        

        //Carga de Catalogos

                //Canal de medios de alta

                

                $media_adds = DB::select('SELECT id_media_add, vc_media_add from cat_media_adds  where b_status = 1 order by 1 asc');


         return view('contact.index',compact('media_adds','type_menu','user_system'));
     
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $branch='1';
        $type_menu='3';
        $user_system =  \Session::get('vc_user_system');


         //Validaciones 

        $validatedData = $request->validate([
            'vc_name' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'i_age' => 'required|numeric|between:0,100',
            'i_gender' => 'required|numeric|between:1,2',
            'id_media_add' => 'required|numeric',
            'i_promotion' => 'required|numeric',
            'vc_comments' => 'nullable|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/'
             ]);
    


         //Obteniendo variables post

        $vc_name=$request->input('vc_name');
        $i_age=$request->input('i_age');
        $i_gender=$request->input('i_gender');
        $id_media_add=$request->input('id_media_add');
        $i_promotion=$request->input('i_promotion');
        $vc_comments=$request->input('vc_comments');
       

        if(empty($vc_comments)){$vc_comments='';}

         //Insercion de datos del contacto
        
        $statment_contact=DB::statement('CALL sp_create_new_contact (?,?,?,?,?,?,?, @id_contact)',[$branch, utf8_decode($vc_name),   $i_age ,$i_gender,   $id_media_add, $i_promotion,  $vc_comments ]);
        $contact_result=DB::select('select @id_contact as new_contact');
        $id_contact= $contact_result[0]->new_contact;


        //Insercion de datos de la experiencia del servicio , se obtiene primero el contacto generado 

    
    return redirect()->route('contact.index')-> with('status','Datos del contacto '.utf8_decode($vc_name).' guardados correctamente');


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
        //
    }
}
