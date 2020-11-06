<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuspendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
         
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



       /*
        $get_payment_data_membership=DB::statement('CALL sp_get_payment_transaction_membership( ? , @v_id_client, @v_d_amount_for_pay, @v_d_amount_payed, @v_id_payment_method, @v_vc_payment_method,   @v_vc_name,  @v_vc_last_name,  @v_vc_sur_name, @v_vc_email,   @v_i_status,  @v_vc_status,  @v_i_pay_status,  @v_vc_pay_status, @v_dt_next_payment, @v_is_debtor, @v_id_last_membership_model, @v_vc_last_membership_model, @v_i_promotion_period, @v_is_owner_membership, @v_id_visit_model,  @v_vc_dashboard_description_vist, @v_is_registration_transaction, @v_id_registration_model, @v_vc_registration_model, @v_vc_dashboard_description_reg, @v_i_transaction_type)',[ $id_membership_payment_transaction ]);
        $payment_data_membership_result=DB::select('SELECT  @v_id_client as id_client,  @v_d_amount_for_pay as d_amount_for_pay, @v_d_amount_payed as d_amount_payed, @v_id_payment_method as id_payment_method, @v_vc_payment_method as vc_payment_method,  @v_vc_name as vc_name, @v_vc_last_name as vc_last_name ,  @v_vc_sur_name as vc_sur_name, @v_vc_email as vc_email,  @v_i_status as i_status, @v_vc_status as vc_status ,  @v_i_pay_status as i_pay_status ,  @v_vc_pay_status as vc_pay_status,    @v_dt_next_payment as dt_next_payment  , @v_is_debtor as  is_debtor , @v_id_last_membership_model as id_last_membership_model, @v_vc_last_membership_model as vc_last_membership_model, @v_i_promotion_period as i_promotion_period, @v_is_owner_membership as is_owner_membership, @v_id_visit_model as id_visit_model,   @v_vc_dashboard_description_vist as vc_dashboard_description_vist,  @v_is_registration_transaction as is_registration_transaction, @v_id_registration_model as id_registration_model, @v_vc_registration_model as vc_registration_model, @v_vc_dashboard_description_reg as vc_dashboard_description_reg, @v_i_transaction_type as i_transaction_type');

        $id_client =  $payment_data_membership_result[0]->id_client;
        $id_email_type='2';
        $id_client_type='3';

        $dt_enqueue = Carbon::now();
        $dt_enqueue_time =  Carbon::createFromFormat('Y-m-d H:i:s',  $dt_enqueue, 'America/Mexico_City');
       
        $statment_response=DB::statement('CALL sp_put_mail_enqueue(?,?,?,?,?, @v_is_response_added)',[   $dt_enqueue , $id_client ,  $id_email_type ,  $id_client_type,   $id_membership_payment_transaction]);
        $st_resp_result=DB::select('SELECT @v_is_response_added AS  is_response_added');

        return view('debt.show',compact( 'type_menu','user_system','payment_data_membership_result'));
*/

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
        $type_menu='0';
        $user_system =  \Session::get('vc_user_system');
     
        $today = Carbon::now();
        $dt_current_day=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');

        //Preguntamos si el cliente tiene adeudos.

        $get_data_membership=DB::statement('CALL sp_get_membership ( ? ,?, @v_vc_name,  @v_vc_last_name,  @v_vc_sur_name, @v_i_period,  @v_i_status,  @v_vc_status,  @v_i_pay_status,  @v_vc_pay_status,  @v_dt_registration, @v_dt_start_payment_period,  @v_dt_next_payment, @v_is_debtor, @v_id_last_membership_model, @v_vc_last_membership_model )',[ $branch, $id_client  ]);
        $membership_result=DB::select('select   @v_vc_name as vc_name, @v_vc_last_name as vc_last_name, @v_vc_sur_name as vc_sur_name, @v_i_status as  i_status, @v_vc_status as vc_status,   @v_i_pay_status as i_pay_status , @v_vc_pay_status as vc_pay_status,    @v_dt_registration as dt_registration,  @v_dt_start_payment_period as dt_start_payment_period, @v_dt_next_payment as dt_next_payment,  @v_vc_last_membership_model as vc_last_membership_model, @v_i_period as i_period');
        
    
        //Catalogo de las razones de baja
        $suspended_reasons = DB::select('SELECT  id_suspended_reason, vc_suspended_reasons  FROM cat_suspended_reasons WHERE b_status = 1  ORDER BY id_suspended_reason ASC');
         

     
        return view('suspend.edit',compact('membership_result', 'suspended_reasons', 'type_menu','user_system', 'id_client','dt_current_day'));
     
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
        $type_menu='2';
        $user_system =  \Session::get('vc_user_system');
    

        $validatedData = $request->validate([
            'vc_payment_transaction_note' => 'nullable|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            ]);


        $id_client=$request->input('id_client');
        $dt_suspend_membership=$request->input('dt_suspend_membership');
        $dt_start_payment_period=$request->input('dt_start_payment_period');
        $dt_registration=$request->input('dt_registration');
        $id_suspended_reason=$request->input('id_suspended_reason');
        $i_status=$request->input('i_status');
        $vc_suspend_transaction_note=$request->input('vc_suspend_transaction_note');
        $vc_name=$request->input('vc_name');
        $vc_last_name=$request->input('vc_last_name');

    

        if(empty($id_suspended_reason)){$id_suspended_reason=1;}

        $today = Carbon::now();
        $dt_current_day=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');

      
     //  CALL sp_put_suspended_membership(<{IN `v_id_client` INT}>, <{IN `v_id_branch` INT}>, <{IN `v_dt_suspend_membership` DATETIME}>, <{IN `v_dt_start_payment_period` DATETIME}>, <{IN `v_dt_registration` DATETIME}>, <{IN `v_id_suspended_reason` SMALLINT(6)}>, <{`v_i_status` SMALLINT(6)}>, <{IN `v_vc_suspend_transaction_note` VARCHAR(512)}>, <{OUT `v_id_suspend_membership` SMALLINT}>);



         $put_suspend_membership=DB::statement('CALL sp_put_suspended_membership(?,?,?,?,?,?,?,?,   @v_id_suspend_membership)', [ $id_client,  $branch, $dt_suspend_membership, $dt_start_payment_period, $dt_registration, $id_suspended_reason, $i_status, $vc_suspend_transaction_note ] );
         $put_suspend_membership_result=DB::select('select @v_id_suspend_membership as  id_suspend_membership');
         
         
        

    
    // return view('membership.edit',compact('membership_promotion_models','client_information',  'membership_models', 'payment_methods', 'type_menu','user_system', 'id_client'));
 
    //return   view('db_clients_expired',compact('clients_expired','clients_expired_counted','clients_actives', 'type_menu','user_system'));
     
    // return  redirect()->route('debt.show', [$id_membership_payment_transaction] ) -> with('status',' La membresia  de '.$vc_name.' '.$vc_last_name.' fue renovada correctamente');
 
    return  redirect()->route('db_clients_expired' ) -> with('status',' La membresia  de '.$vc_name.' '.$vc_last_name.' fue suspendida exitosamente');
    

    


    // return    $put_suspend_membership_result;


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
