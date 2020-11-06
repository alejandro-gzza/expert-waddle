<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DebtController extends Controller
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

        //Preguntamos si el cliente tiene adeudos.

        $statment_debt=DB::statement('CALL sp_get_debt_payment (?,?, @v_id_debt_payment , @v_d_amount_debt, @v_d_amount_for_pay, @v_d_amount_payed, @v_id_payment_method, @v_vc_payment_method, @v_vc_name, @v_vc_last_name, @v_vc_sur_name, @v_i_status, @v_vc_status, @v_i_pay_status, @v_vc_pay_status, @v_dt_next_payment, @v_is_debtor, @v_id_last_membership_model, @v_vc_last_membership_model, @v_is_owner_membership)',[ $branch, $id_client ]);
        $debt_information=DB::select('select @v_id_debt_payment as id_debt_payment, @v_d_amount_debt as d_amount_debt, @v_d_amount_for_pay as d_amount_for_pay, @v_d_amount_payed as d_amount_payed ,  @v_vc_name as vc_name , @v_vc_last_name as vc_last_name , @v_vc_sur_name as vc_sur_name, @v_i_status as i_status , @v_vc_status as vc_status, @v_i_pay_status as i_pay_status, @v_vc_pay_status as vc_pay_status ,  @v_dt_next_payment  as dt_next_payment, @v_is_debtor as is_debtor, @v_vc_last_membership_model  as vc_last_membership_model');
        $id_debt_payment = $debt_information[0]->id_debt_payment;

        //Catalogo de los metodos de pago
        $payment_methods = DB::select('SELECT  id_payment_method, vc_payment_method  FROM cat_payment_methods WHERE b_status = 1  ORDER BY id_payment_method ASC');
         

     
                return view('debt.edit',compact('debt_information', 'payment_methods', 'type_menu','user_system', 'id_client'));
     
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
              ]);

        $id_debt_payment=$request->input('id_debt_payment');
        $id_payment_method=$request->input('id_payment_method');
        $d_amount_payed=$request->input('d_amount_payed');

        if(empty($id_debt_payment)){$id_debt_payment=0;}

        $today = Carbon::now();
        $dt_current_day=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');

       //  CALL sp_put_paymet_debt ('19','2019-04-18 11:27:09','1','100.00'  , @v_d_amount_change, @v_d_amount_debt, @v_is_payment_transaction_added, @v_id_membership_payment_transaction, @v_is_debt_previous_closed, @v_vc_name , @v_vc_last_name );


         $put_paymet_debt_membership=DB::statement('CALL sp_put_paymet_debt (?,?,?,?,  @v_d_amount_change, @v_d_amount_debt, @v_is_payment_transaction_added, @v_id_membership_payment_transaction, @v_is_debt_previous_closed, @v_vc_name, @v_vc_last_name)', [ $id_debt_payment,  $dt_current_day, $id_payment_method,$d_amount_payed] );
         $put_paymet_debt_result=DB::select('select @v_id_membership_payment_transaction as id_membership_payment_transaction,  @v_is_debt_previous_closed as  is_debt_previous_closed,   @v_vc_name as vc_name, @v_vc_last_name as vc_last_name   ');
         $id_membership_payment_transaction=$put_paymet_debt_result[0]->id_membership_payment_transaction;
         $vc_name=$put_paymet_debt_result[0]->vc_name;
         $vc_last_name=$put_paymet_debt_result[0]->vc_last_name;


    
    // return view('membership.edit',compact('membership_promotion_models','client_information',  'membership_models', 'payment_methods', 'type_menu','user_system', 'id_client'));
 
    
    return  redirect()->route('debt.show', [$id_membership_payment_transaction] ) -> with('status',' La membresia  de '.$vc_name.' '.$vc_last_name.' fue renovada correctamente');
    
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
