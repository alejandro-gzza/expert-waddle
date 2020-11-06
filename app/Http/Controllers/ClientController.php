<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientController extends Controller
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
        $type_menu='5';
        $user_system =  \Session::get('vc_user_system');
        $id_team_member=  \Session::get('id_team_member');
        

            // Obtener las caracteristicas de los clientes.

           // $clients = DB::select('SELECT C.id_client AS id, remove_accents(vc_name) AS vc_name, remove_accents(vc_last_name) AS vc_last_name, remove_accents(vc_sur_name) AS vc_sur_name, vc_nick_name, vc_email, vc_cellphone_number,   C.i_status AS i_status,CS.vc_dashboard_status AS vc_dashboard_status, C.i_pay_status AS i_pay_status, CPS.vc_pay_status AS vc_pay_status, is_debtor, id_last_membership_model, CMM.vc_membership_model AS vc_last_membership_model, id_prospect_perfomance_test  FROM tb_clients C LEFT JOIN tb_clients_personal_information CPI ON C.id_client = CPI.id_client LEFT JOIN cat_status CS ON C.i_status = CS.i_status  LEFT JOIN cat_pay_status CPS ON C.i_pay_status = CPS.i_pay_status LEFT JOIN cat_membership_models CMM ON C.id_last_membership_model = CMM.id_membership_model  ORDER BY C.id_client');
            
            $clients = DB::select('SELECT DISTINCT C.id_client AS id,
                    REMOVE_ACCENTS(vc_name) AS vc_name,
                    REMOVE_ACCENTS(vc_last_name) AS vc_last_name,
                    REMOVE_ACCENTS(vc_sur_name) AS vc_sur_name,
                    vc_nick_name,
                    vc_email,
                    vc_cellphone_number,
                    C.i_status AS i_status,
                    CS.vc_dashboard_status AS vc_dashboard_status,
                    C.i_pay_status AS i_pay_status,
                    CPS.vc_pay_status AS vc_pay_status,
                    is_debtor,
                    id_last_membership_model,
                    CMM.vc_membership_model AS vc_last_membership_model,
                    id_prospect_perfomance_test,
                    DATE(LTN.dt_created) AS  dt_created,
                    CTM.vc_team_member
                    FROM
                    tb_clients C 
                    LEFT JOIN
                    tb_clients_personal_information  CPI ON C.id_client = CPI.id_client
                    LEFT JOIN
                    cat_status CS ON C.i_status = CS.i_status
                    LEFT JOIN
                    cat_pay_status CPS ON C.i_pay_status = CPS.i_pay_status
                    LEFT JOIN
                    cat_membership_models CMM ON C.id_last_membership_model = CMM.id_membership_model
                    LEFT JOIN
                    (SELECT MAX(dt_created) AS dt_created, id_client, id_team_member FROM log_tracking_notes WHERE id_team_member= ? GROUP BY  id_client, id_team_member )
                    LTN ON C.id_client = LTN.id_client
                    LEFT JOIN
                    cat_team_member CTM ON LTN.id_team_member = CTM.id_team_member
                    LEFT JOIN
                    cat_type_team_member CTTM ON CTM.i_type_team_member = CTTM.i_type_team_member
                     ORDER BY C.id_client', [$id_team_member]);
                                
                      
            // Catalogo estados


        // return  $date ;

           return view('client.index',compact('clients', 'type_menu','user_system'));
     
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
        $branch='1';
        $type_menu='5';
        $user_system =  \Session::get('vc_user_system');


    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_client)
    {
        $branch='1';
        $type_menu='5';
        $user_system =  \Session::get('vc_user_system');
        

        
        $client = DB::select('SELECT C.id_client AS id, remove_accents(vc_name) AS vc_name, remove_accents(vc_last_name) AS vc_last_name, remove_accents(vc_sur_name) AS vc_sur_name, vc_nick_name, vc_email, vc_cellphone_number, C.i_status AS i_status,CS.vc_dashboard_status AS vc_dashboard_status, C.i_pay_status AS i_pay_status, CPS.vc_pay_dashboard_status AS vc_pay_dashboard_status, is_debtor, id_last_membership_model, CMM.vc_membership_model AS vc_last_membership_model,  DATE(dt_registration) AS dt_registration , IF(dt_next_payment!= "0000-00-00 00:00:00", DATE(dt_next_payment), "" ) AS dt_next_payment, IF(dt_suspend_membership!= "0000-00-00 00:00:00" AND dt_next_payment= "0000-00-00 00:00:00" , DATE(dt_suspend_membership), "" ) AS  dt_suspend_membership, CONCAT( vc_adress_street, " ", vc_adress_number, " ",vc_adress_apartment) AS vc_adress, vc_emergency_contact_name , vc_emergency_contact_last_name, vc_emergency_contact_sur_name, vc_emergency_contact_cell_number, IF(CPE.id_previous_experience!=0 , vc_previous_experience,"") AS vc_previous_experience, IF(CCG.id_customer_goal!=0 , vc_customer_goal,"") AS  vc_customer_goal , vc_workout_plan_name, vc_workout_plan_alias, vc_acquisition_channel, TIMESTAMPDIFF(year,dt_born, now()) as i_years_old, CG.vc_gender, IF(CPI.id_employment=0,  CPI.vc_employment, CE.vc_employment) AS vc_employment, vc_civil_status, id_prospect_perfomance_test   FROM tb_clients C LEFT JOIN tb_clients_personal_information CPI ON C.id_client = CPI.id_client LEFT JOIN cat_status CS ON C.i_status = CS.i_status LEFT JOIN cat_pay_status CPS ON C.i_pay_status = CPS.i_pay_status LEFT JOIN cat_membership_models CMM ON C.id_last_membership_model = CMM.id_membership_model LEFT JOIN cat_previous_experience CPE ON CPI.id_previous_experience = CPE.id_previous_experience LEFT JOIN cat_customer_goals CCG ON CPI.id_first_customer_goal = CCG.id_customer_goal LEFT JOIN cat_workout_plan CWP ON CPI.id_first_workout_plan = CWP.id_workout_plan LEFT JOIN cat_acquisition_channel CAC ON CPI.id_acquisition_channel = CAC.id_acquisition_channel LEFT JOIN cat_gender CG ON CPI.i_gender = CG.i_gender LEFT JOIN cat_employments CE ON CPI.id_employment = CE.id_employment LEFT JOIN cat_civil_status CCE ON CPI.i_civil_status = CCE.i_civil_status WHERE C.id_client= ?', [ $id_client]);
        $id_prospect_perfomance_test=$client[0]->id_prospect_perfomance_test;
        $weekly_average =DB::select('SELECT (COUNT(WEEK(dt_attendance_transaction)) * 100 ) /  (DAYOFWEEK(NOW())-1) AS i_weekly_average FROM log_attendance_transaction WHERE WEEK(dt_attendance_transaction) = (WEEK(NOW()))  AND  id_client= ?', [ $id_client]);
        $total_month_attendance =DB::select('SELECT COUNT(*) AS i_total_month_attendance  FROM log_attendance_transaction WHERE MONTH(dt_attendance_transaction) = MONTH(NOW())  AND  id_client= ?', [ $id_client]);
        $payment_transactions_detail=  DB::select('SELECT 
        LMPT.id_client,
        IF(LMPT.id_registration_model != 0,
            IF(LMPT.is_registration_transaction = 0,
                "Renovación de Membresía",
                "Nueva membresía e inscripción"),
            "Visita") AS vc_pay,
        IF(LMPT.id_registration_model != 0,
            IF(LMPT.is_registration_transaction = 0,
                CMM.vc_membership_model,
                CONCAT(CRM.vc_dashboard_description,
                        " + ",
                        CMM.vc_membership_model)),
            CVM.vc_visit_model) AS vc_pay_detail,
        CPI.vc_name,
        CPI.vc_last_name,
        CPI.vc_sur_name,
        CPI.vc_nick_name,
        CPM.vc_payment_method,
        IF(i_transaction_type = 1
                || i_transaction_type = 3,
            LMPT.d_amount_for_pay,
            LMPT.d_amount) AS d_sum_d_amount,
        i_transaction_type,
        i_last_status,
        i_last_pay_status,
        LMPT.id_payment_method AS id_payment_method,
        LPTN.vc_payment_transaction_note,
        dt_transaction_payment
    FROM
        log_membership_payment_transaction LMPT
            LEFT JOIN
        log_payment_transaction_notes LPTN  ON LMPT.id_membership_payment_transaction = LPTN.id_membership_payment_transaction
            LEFT JOIN     
        cat_membership_models CMM ON LMPT.id_membership_model = CMM.id_membership_model
            LEFT JOIN
        tb_clients_personal_information CPI ON LMPT.id_client = CPI.id_client
            LEFT JOIN
        cat_payment_methods CPM ON LMPT.id_payment_method = CPM.id_payment_method
            LEFT JOIN
        log_payment_transaction_visit_details LPTVD ON LMPT.id_membership_payment_transaction = LPTVD.id_membership_payment_transaction
            LEFT JOIN
        cat_visit_models CVM ON CVM.id_visit_model = LPTVD.id_visit_model
            LEFT JOIN
        cat_registration_models CRM ON CMM.id_registration_model = CRM.id_registration_model
    WHERE
        LMPT.id_client = ?
    ORDER BY dt_transaction_payment DESC', [$id_client]);

        $perfomance_test_detail=  DB::select('SELECT LPT.i_type_perfomance_test,  vc_type_perfomance_test, i_number_iterations ,  LPT.id_execution_type, vc_execution_type, LPT.id_rhythm , vc_rhythm, LPT.id_velocity,  vc_velocity, i_average_rom, LPT.id_rom,  vc_rom, LPT.id_type_form_core , vc_type_form_core, vc_test_comments   
        FROM log_perfomance_test LPT
        LEFT JOIN cat_type_perfomance_test  CTPT  ON LPT.i_type_perfomance_test= CTPT.i_type_perfomance_test
        LEFT JOIN cat_execution_type  CET   ON LPT.id_execution_type= CET.id_execution_type
        LEFT JOIN cat_rhythm  CR   ON LPT.id_rhythm= CR.id_rhythm
        LEFT JOIN cat_velocity  CV   ON LPT.id_velocity= CV.id_velocity
        LEFT JOIN cat_rom  CRO  ON LPT.id_rom= CRO.id_rom
        LEFT JOIN cat_type_form_core  CTFC  ON LPT.id_type_form_core= CTFC.id_type_form_core
        WHERE LPT.id_prospect_perfomance_test = ?', [$id_prospect_perfomance_test]);
        

        $tracking_notes_detail=  DB::select('SELECT 
        LTN.dt_created,
        CTM.vc_team_member,
        CTTM.vc_type_team_member,
        CTNT.id_tracking_note_type,
        CTNT.vc_tracking_note_type,
        LTN.vc_tracking_note_title,
        LTN.vc_tracking_note
        FROM
        log_tracking_notes LTN
            LEFT JOIN
        cat_tracking_note_type CTNT ON LTN.id_tracking_note_type = CTNT.id_tracking_note_type
            LEFT JOIN
        cat_team_member CTM ON LTN.id_team_member = CTM.id_team_member
            LEFT JOIN
        cat_type_team_member CTTM ON CTM.i_type_team_member = CTTM.i_type_team_member
        WHERE LTN.b_status="1" AND LTN.id_client=?  ORDER BY  LTN.dt_created DESC', [$id_client]);
        
       // return  $client;
       return view('client.show',compact('client','type_menu','user_system', 'weekly_average','total_month_attendance','payment_transactions_detail','perfomance_test_detail',  'tracking_notes_detail'));

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
