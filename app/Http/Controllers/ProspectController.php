<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProspectController extends Controller
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


                //Catalogo de modelos de pago con promocion 
                
                $membership_promotion_models = DB::select('SELECT i_promotion_available, vc_membership_model, vc_dashboard_description, round(d_amount, 2) as d_amount , round ((i_percent_off *100),0) as i_percent_off FROM cat_membership_models WHERE b_status = 1 AND i_promotion_available > 0 ORDER BY i_promotion_available ASC');
                
                //Experiencia
                $previous_experience = DB::select('SELECT id_previous_experience, vc_previous_experience from cat_previous_experience  where b_status = 1 order by id_previous_experience asc');

                //Metas del prospecto
                $customer_goals = DB::select('SELECT id_customer_goal, vc_customer_goal from cat_customer_goals  where b_status = 1 order by 1 asc');

                //Canal de Adquisicion 
                $acquisition_channel = DB::select('SELECT id_acquisition_channel, vc_acquisition_channel from cat_acquisition_channel  where b_status = 1 order by 1 asc');

                //Quien te atendio
                $team_member = DB::select('SELECT id_team_member, vc_team_member from cat_team_member  where b_status = 1 order by 1 asc');

                //Explicamos de 
                $service_experience = DB::select('SELECT id_service_experience, vc_service_experience from cat_service_experience where b_status = 1 order by 1 asc');

                //Medio de contacto
                $media_adds = DB::select('SELECT id_media_add, vc_media_add from cat_media_adds  where b_status = 1 order by 1 asc');



         return view('prospect.index',compact('membership_promotion_models', 'previous_experience','customer_goals','acquisition_channel','team_member','service_experience','type_menu','user_system','media_adds'));
     
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
            'vc_last_name' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_sur_name' => 'nullable|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_email' => 'required|max:255|email',
            'vc_cellphone_number' => 'nullable|numeric',
            'i_age' => 'nullable|numeric',
            'i_gender' => 'required|numeric|between:1,2',
            'id_media_add' => 'required|numeric',
            'id_previous_experience' => 'required|numeric',
            'id_customer_goal' => 'required|numeric',
            'id_team_member' => 'required|numeric'
             ]);
    


         //Obteniendo variables post

        $vc_name=$request->input('vc_name');
        $vc_last_name=$request->input('vc_last_name');
        $vc_sur_name=$request->input('vc_sur_name');
        $vc_email=$request->input('vc_email');
        $vc_cellphone_number=$request->input('vc_cellphone_number');
        $i_age=$request->input('i_age');
        $i_gender=$request->input('i_gender');
        $id_media_add=$request->input('id_media_add');
        $id_previous_experience=$request->input('id_previous_experience');
        $id_customer_goal=$request->input('id_customer_goal');
        $id_acquisition_channel=$request->input('id_acquisition_channel');
        $id_team_member=$request->input('id_team_member');
        $check_service_experience=$request->input('id_service_experience');

        if(empty($vc_sur_name)){$vc_sur_name='';}
        if(empty($vc_cellphone_number)){$vc_cellphone_number='0';}
        if(empty($i_age)){$i_age='0';}

         //Insercion de datos de prospecto
        
        $statment_prospect=DB::statement('CALL sp_create_new_prospect (?,?,?,?,?,?,?,?,?,?,?,?,?, @id_prospect)',[ $branch, utf8_decode($vc_name),  utf8_decode($vc_last_name),  utf8_decode($vc_sur_name), $vc_email,  $vc_cellphone_number, $i_age ,$i_gender,  $id_media_add,   $id_previous_experience, $id_customer_goal,  $id_acquisition_channel, $id_team_member  ]);
        $prospect_result=DB::select('select @id_prospect as new_prospect');
        $id_prospect= $prospect_result[0]->new_prospect;


        //Insercion de datos de la experiencia del servicio , se obtiene primero el prospecto generado 

        if(!empty($check_service_experience)){

        foreach($check_service_experience as $id_service_experience){
                $prospect_exp_result=DB::statement('CALL sp_create_new_prospect_experience (?,?,?,@id_prospect_experience)',[$id_prospect, $branch,  $id_service_experience]);
                $id_prospect_experience=DB::select('select @id_prospect_experience as new_prospect_experience');
            }
        }
    
    
    return redirect()->route('prospect.index')-> with('status','Datos del prospecto '.utf8_decode($vc_name).' '.utf8_decode($vc_last_name).' guardados correctamente');


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
