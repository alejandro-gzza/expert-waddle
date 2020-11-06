<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MembershipController extends Controller
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
        $type_menu='2';
        $user_system =  \Session::get('vc_user_system');
        
        

            // Obtener las caracteristicas de los clientes.

            $clients = DB::select('SELECT 
            C.id_client AS id,
            REMOVE_ACCENTS(vc_name) AS vc_name,
            REMOVE_ACCENTS(vc_last_name) AS vc_last_name,
            REMOVE_ACCENTS(vc_sur_name) AS vc_sur_name,
            vc_nick_name,
            dt_last_attendance,
            DATE(dt_next_payment) AS dt_next_payment,
            DATE(dt_last_payment) AS dt_last_payment,
            C.i_status AS i_status,
            CS.vc_dashboard_status AS vc_dashboard_status,
            C.i_pay_status AS i_pay_status,
            CPS.vc_pay_status AS vc_pay_status,
            is_debtor,
            id_last_membership_model,
            CMM.vc_membership_model AS vc_last_membership_model,
            CPI.id_last_workout_plan,
        CWP.vc_workout_plan_name
        FROM
            tb_clients C
                LEFT JOIN
            tb_clients_personal_information CPI ON C.id_client = CPI.id_client
                LEFT JOIN
            cat_status CS ON C.i_status = CS.i_status
                LEFT JOIN
            cat_pay_status CPS ON C.i_pay_status = CPS.i_pay_status
                LEFT JOIN
            cat_membership_models CMM ON C.id_last_membership_model = CMM.id_membership_model
            LEFT JOIN
        cat_workout_plan CWP ON CWP.id_workout_plan=CPI.id_last_workout_plan
        ORDER BY C.id_client');
               
            // Catalogo estados


        // return  $date ;

           return view('membership.index',compact('clients', 'type_menu','user_system'));
     
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_membership_payment_transaction)
    { // Propiedades de Navegacion
        $branch='1';
        $type_menu='2';
        $user_system =  \Session::get('vc_user_system');

        $get_payment_data_membership=DB::statement('CALL sp_get_payment_transaction_membership( ? , @v_id_client, @v_d_amount_for_pay, @v_d_amount_payed, @v_id_payment_method, @v_vc_payment_method,   @v_vc_name,  @v_vc_last_name,  @v_vc_sur_name, @v_vc_email,   @v_i_status,  @v_vc_status,  @v_i_pay_status,  @v_vc_pay_status, @v_dt_next_payment, @v_is_debtor, @v_id_last_membership_model, @v_vc_last_membership_model, @v_i_promotion_period, @v_is_owner_membership, @v_id_visit_model,  @v_vc_dashboard_description_vist, @v_is_registration_transaction, @v_id_registration_model, @v_vc_registration_model, @v_vc_dashboard_description_reg, @v_i_transaction_type)',[ $id_membership_payment_transaction ]);
        $payment_data_membership_result=DB::select('SELECT  @v_id_client as id_client,  @v_d_amount_for_pay as d_amount_for_pay, @v_d_amount_payed as d_amount_payed, @v_id_payment_method as id_payment_method, @v_vc_payment_method as vc_payment_method,  @v_vc_name as vc_name, @v_vc_last_name as vc_last_name ,  @v_vc_sur_name as vc_sur_name, @v_vc_email as vc_email,  @v_i_status as i_status, @v_vc_status as vc_status ,  @v_i_pay_status as i_pay_status ,  @v_vc_pay_status as vc_pay_status,    @v_dt_next_payment as dt_next_payment  , @v_is_debtor as  is_debtor , @v_id_last_membership_model as id_last_membership_model, @v_vc_last_membership_model as vc_last_membership_model, @v_i_promotion_period as i_promotion_period, @v_is_owner_membership as is_owner_membership, @v_id_visit_model as id_visit_model,   @v_vc_dashboard_description_vist as vc_dashboard_description_vist,  @v_is_registration_transaction as is_registration_transaction, @v_id_registration_model as id_registration_model, @v_vc_registration_model as vc_registration_model, @v_vc_dashboard_description_reg as vc_dashboard_description_reg, @v_i_transaction_type as i_transaction_type');

        $id_client =  $payment_data_membership_result[0]->id_client;
        $id_visit_model =  $payment_data_membership_result[0]->id_visit_model;
        $id_last_membership_model=  $payment_data_membership_result[0]->id_last_membership_model;
        $id_email_type='2';
        $id_client_type='3';

        $dt_enqueue = Carbon::now();
        $dt_enqueue_time =  Carbon::createFromFormat('Y-m-d H:i:s',  $dt_enqueue, 'America/Mexico_City');
       
        $statment_response=DB::statement('CALL sp_put_mail_enqueue(?,?,?,?,?, @v_is_response_added)',[   $dt_enqueue , $id_client ,  $id_email_type ,  $id_client_type,   $id_membership_payment_transaction]);
        $st_resp_result=DB::select('SELECT @v_is_response_added AS  is_response_added');

        // se seleciona el mail que se enviara en en el disoarador

        $statment_trigger=DB::statement('CALL sp_get_event_trigger_to_mail_enqueue(?,?,?,?,?,?, @v_is_response_added)',[   $dt_enqueue , $id_client , $id_last_membership_model,   $id_visit_model,  $id_client_type,   $id_membership_payment_transaction]);
        $statment_trigge_result=DB::select('SELECT @v_is_response_added AS  is_response_added');

        
    
        return view('membership.show',compact( 'type_menu','user_system','payment_data_membership_result'));



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
        $type_menu='2';
        $user_system =  \Session::get('vc_user_system');
     
        
        
        $today = Carbon::now();
        $dt_current_day=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');

           //Catalogo de los metodos de pago

        $payment_methods = DB::select('SELECT  id_payment_method, vc_payment_method  FROM cat_payment_methods WHERE b_status = 1  ORDER BY id_payment_method ASC');
         

          //Catalogo de modelos de pago con promocion 
                
        $membership_promotion_models = DB::select('SELECT i_promotion_available, vc_membership_model, vc_dashboard_description, round(d_amount, 2) as d_amount , round ((i_percent_off *100),0) as i_percent_off FROM cat_membership_models WHERE b_status = 1 AND i_promotion_available > 0 ORDER BY i_promotion_available ASC');
             
            // Obtener las caracteristicas del modelo de membresia

        $statment_client=DB::statement('CALL sp_get_client_membership_model (?,?,?,  @v_vc_name, @v_vc_last_name, @v_vc_sur_name,@v_i_status, @v_vc_status, @v_i_pay_status,@v_vc_pay_status,@v_vc_reference_status, @v_dt_reference_status, @v_id_recommended_membership_model, @v_vc_dft_membership_model_client, @v_vc_dft_dashboard_description, @v_id_registration_model, @v_vc_registration_description, @v_i_physical_review, @v_i_nutrition_review, @v_i_community_shirt, @v_reg_dft_amount,  @v_dt_dft_next_payment, @v_dft_amount, @v_dft_i_promotion_period_for_pay, @v_dft_i_promotion_available, @v_dft_i_percent_off, @v_is_owner_client_membership, @v_is_owner_membership , @v_vc_name_owner, @v_vc_last_name_owner, @v_vc_sur_name_owner, @v_splitting_memberships, @v_id_workout_plan, @v_id_coach  )',[ $branch, $id_client, $dt_current_day  ]);
           
        $client_information=DB::select('SELECT  @v_vc_name as vc_name  , @v_vc_last_name as vc_last_name , @v_vc_sur_name as vc_sur_name  ,@v_i_status as i_status , @v_vc_status as vc_status , @v_i_pay_status as i_pay_status ,@v_vc_pay_status as vc_pay_status  ,@v_vc_reference_status as vc_reference_status , @v_dt_reference_status as dt_reference_status , @v_id_recommended_membership_model as id_recommended_membership_model , @v_vc_dft_membership_model_client as vc_dft_membership_model_client , @v_vc_dft_dashboard_description as vc_dft_dashboard_description,  @v_id_registration_model as id_registration_model , @v_vc_registration_description as vc_registration_description, @v_i_physical_review as i_physical_review, @v_i_nutrition_review as i_nutrition_review, @v_i_community_shirt as i_community_shirt, @v_reg_dft_amount as  reg_dft_amount,  @v_dt_dft_next_payment as dt_dft_next_payment, @v_dft_amount as dft_amount, @v_dft_i_promotion_period_for_pay as dft_i_promotion_period_for_pay, @v_dft_i_promotion_available as dft_i_promotion_available , @v_dft_i_percent_off as dft_i_percent_off,    @v_is_owner_client_membership as is_owner_client_membership  , @v_is_owner_membership as  is_owner_membership  , @v_vc_name_owner as vc_name_owner  , @v_vc_last_name_owner as vc_last_name_owner, @v_vc_sur_name_owner as vc_sur_name_owner, @v_splitting_memberships AS splitting_memberships, @v_id_workout_plan AS id_workout_plan, @v_id_coach AS id_coach  ');
            // $id_prospect= $prospect_result[0]->new_prospect;

           //Catalogo de metodos de pago

        $membership_models = DB::select('SELECT id_membership_model_client, vc_membership_model_client , d_amount  FROM tmp_membership_model_for_client  ');
          
         //Catalogo de visitas
         $visit_models = DB::select('SELECT id_visit_model, vc_dashboard_description  FROM cat_visit_models WHERE b_status=1');
        

          // catalogo de niveles de entrenamiento
          $workout_plan= DB::select('SELECT id_workout_plan, vc_workout_plan_alias from cat_workout_plan  where b_status = 1 order by 1 asc');

          //Coach 
          $coach = DB::select('SELECT id_team_member, vc_team_member from cat_team_member  where b_status = 1 AND i_type_team_member in(2,3) order by 1 desc');
 


       return view('membership.edit',compact('membership_promotion_models','client_information',  'membership_models', 'payment_methods', 'visit_models', 'type_menu','user_system', 'id_client','workout_plan','coach'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_client)
    {
    
        $branch='1';
        $type_menu='2';
        $user_system =  \Session::get('vc_user_system');
    
        

        $validatedData = $request->validate([
            'd_amount_payed' => 'required|max:255|regex:/^\d*(\.\d{2})?$/|between:0,100000',
            'vc_payment_transaction_note' => 'nullable|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
             ]);


        $id_client=$request->input('id_client');
        $id_registration_model=$request->input('id_registration_model');
        $id_membership_model_client=$request->input('id_membership_model_client');
        $is_owner_membership=$request->input('is_owner_membership');
        $is_owner_client_membership=$request->input('is_owner_client_membership');
        $i_promotion_period=$request->input('i_promotion_period_for_pay');
        $id_payment_method=$request->input('id_payment_method');
        $d_amount_for_pay=$request->input('d_amount_for_pay');
        $d_amount_payed=$request->input('d_amount_payed');
        $dt_next_payment=$request->input('dt_next_payment');
        $dt_original_next_payment=$request->input('dt_original_next_payment');
        $vc_payment_transaction_note=$request->input('vc_payment_transaction_note');
        $id_visit=$request->input('id_visit');
        $i_days_add=$request->input('i_days_add');
        $id_promotion_code=$request->input('id_promotion_code');
        $id_workout_plan=$request->input('id_workout_plan');
        $id_coach=$request->input('id_coach');
        $i_attendance=$request->input('i_attendance');


        if(empty($is_owner_membership)){$is_owner_membership=0;}
        if(empty($i_promotion_period)){$i_promotion_period=0;}
        if(empty($id_visit)){$id_visit=0;}
        if(empty($i_days_add)){$i_days_add=0;}
        if(empty($is_owner_client_membership)){$is_owner_client_membership=0;}
        if(empty($vc_payment_transaction_note)){$vc_payment_transaction_note='';}
        if(empty($id_promotion_code)){$id_promotion_code=0;}
        if(empty($i_attendance)){ $i_attendance=0;}

        $today = Carbon::now();
        $dt_current_day=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');

        //return $request;


       $create_new_membership=DB::statement('CALL sp_create_membership (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, @v_d_amount_change, @v_d_amount_debt, @v_is_payment_transaction_added, @v_id_membership_payment_transaction, @v_is_debt_added , @v_is_membership_added, @v_is_reinstatement_added, @v_is_model_payment_change, @v_is_membership_note_added, @v_vc_name, @v_vc_last_name)', [ $id_client, $branch, $dt_current_day,  $id_registration_model,  $id_membership_model_client, $id_visit,  $is_owner_membership,   $i_promotion_period, $id_payment_method,  $d_amount_for_pay,   $d_amount_payed,  $dt_next_payment,  $dt_original_next_payment,  $i_days_add,  utf8_decode($vc_payment_transaction_note),$id_promotion_code, $id_workout_plan, $id_coach, $i_attendance]);
        $new_membership_result=DB::select('select @v_id_membership_payment_transaction as id_membership_payment_transaction, @v_d_amount_change as d_amount_change  , @v_d_amount_debt as d_amount_debt, @v_is_debt_added as is_debt_added, @v_is_reinstatement_added  as is_reinstatement_added,  @v_vc_name as vc_name, @v_vc_last_name as vc_last_name   ');
        $id_membership_payment_transaction=$new_membership_result[0]->id_membership_payment_transaction;
        $vc_name=$new_membership_result[0]->vc_name;
        $vc_last_name=$new_membership_result[0]->vc_last_name;


    
    // return view('membership.edit',compact('membership_promotion_models','client_information',  'membership_models', 'payment_methods', 'type_menu','user_system', 'id_client'));
 
    
    return  redirect()->route('membership.show', [$id_membership_payment_transaction] ) -> with('status',' La membresia  de '.$vc_name.' '.$vc_last_name.' fue renovada correctamente');
    
    // return    $membership_result;


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
