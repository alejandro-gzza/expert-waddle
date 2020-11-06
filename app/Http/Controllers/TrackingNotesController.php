<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrackingNotesController extends Controller
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
    public function edit($id_client)
    {
    
        $branch='1';
        $type_menu='5';
        $user_system =  \Session::get('vc_user_system');
        $id_team_member=  \Session::get('id_team_member');
        
        $today = Carbon::now();
        $dt_current_day=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');



        //Carga de Catalogos

                //Canal de medios de alta
     

     $tracking_note_type = DB::select('SELECT id_tracking_note_type, vc_tracking_note_type from cat_tracking_note_type  where b_status = 1 order by 1 asc');

     $type_team_member = DB::select('SELECT id_team_member, vc_team_member, vc_type_team_member FROM cat_team_member CTM
     LEFT JOIN cat_type_team_member CTTM ON CTM.i_type_team_member=CTTM.i_type_team_member  WHERE  CTM.id_team_member= ?',[ $id_team_member] );


     $get_data_membership=DB::statement('CALL sp_get_membership ( ? ,?, @v_vc_name,  @v_vc_last_name,  @v_vc_sur_name, @v_i_period,  @v_i_status,  @v_vc_status,  @v_i_pay_status,  @v_vc_pay_status,  @v_dt_registration, @v_dt_start_payment_period,  @v_dt_next_payment, @v_is_debtor, @v_id_last_membership_model, @v_vc_last_membership_model )',[ $branch, $id_client  ]);
     $membership_result=DB::select('select  @v_vc_name as vc_name, @v_vc_last_name as vc_last_name, @v_vc_sur_name as vc_sur_name, @v_i_status as  i_status, @v_vc_status as vc_status,   @v_i_pay_status as i_pay_status , @v_vc_pay_status as vc_pay_status,    @v_dt_registration as dt_registration,  @v_dt_start_payment_period as dt_start_payment_period, @v_dt_next_payment as dt_next_payment,  @v_vc_last_membership_model as vc_last_membership_model, @v_i_period as i_period');
        


         return  view('tracking_notes.index',compact('tracking_note_type', 'membership_result','dt_current_day','type_menu','user_system','type_team_member','id_client'));
     
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       
        $branch='1';
        $type_menu='5';
        $user_system =  \Session::get('vc_user_system');
    

        $validatedData = $request->validate([
            'vc_tracking_note_title' => 'nullable|max:255|regex:/^([:,.;!¡¿?"0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;!¡¿?"0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_tracking_note' => 'nullable|max:512|regex:/^([:,.;!¡¿?"0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;!¡¿?"0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            ]);


        $vc_name=$request->input('vc_name');
        $id_client=$request->input('id_client');
        $id_team_member=$request->input('id_team_member');
        $id_tracking_note_type=$request->input('id_tracking_note_type');
        $vc_tracking_note_title=$request->input('vc_tracking_note_title');
        $vc_tracking_note=$request->input('vc_tracking_note');
        $is_visible=$request->input('is_visible');
       

        if(empty($vc_tracking_note_title)){$vc_tracking_note_title='';}
        if(empty($vc_tracking_note)){$vc_tracking_note='';}

         //Insercion de datos de seguimiento
        $statment_tracking_note=DB::statement('CALL sp_create_new_tracking_note (?,?,?,?,?,?,?, @id_tracking_note)',[$branch, $id_client,  $id_tracking_note_type , $id_team_member ,  utf8_decode($vc_tracking_note_title), utf8_decode($vc_tracking_note),   $is_visible ]);
        $tracking_note_result=DB::select('select @id_tracking_note as id_tracking_note');
        $id_tracking_note= $tracking_note_result[0]->id_tracking_note;


        //Insercion de datos de la experiencia del servicio , se obtiene primero el contacto generado 

    
    return redirect()->route('client.index')-> with('status','La nota de seguimiento con titulo '.utf8_decode($vc_tracking_note_title).' para el cliente '.utf8_decode($vc_name).' fue guardada correctamente');

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
