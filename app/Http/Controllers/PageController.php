<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Arr;


class PageController extends Controller
{


    public function login() {
        // Propiedades de Navegacion
        

         $branch='1';
         $type_menu='0';
        
         if(empty(\Session::get('vc_user_system'))){return view('login');}

         else { return redirect()-> to('home_workout'); }

        }


    public function home() {
        // Propiedades de Navegacion
        

         // Propiedades de Navegacion
         $branch='1';
         $type_menu='0';
         $user_system =  \Session::get('vc_user_system');
         
         $today = Carbon::now();
         $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
         $dt_current_day->format('Y-M-D');


         // Cambio de estatus que se ejecuta una sola vez al día, posteriormente se cambiara a un archivo unico

         
        $change_status=DB::statement(' CALL sp_change_status( ?, ?, @v_is_sucessfully_process_to_suspend, @v_is_sucessfully_process_debtors_to_suspend,@v_is_sucessfully_process_marked_active, @v_is_sucessfully_process_marked_to_expire, @v_is_sucessfully_process_marked_expired, @v_is_sucessfully_process_marked_expired_debtor , @v_is_sucessfully_process_expired_visit)',[ $dt_current_day, $branch ]);
        $id_perfomance_test=DB::select('select @v_is_sucessfully_process_to_suspend as is_sucessfully_process_to_suspend   , @v_is_sucessfully_process_debtors_to_suspend  as is_sucessfully_process_debtors_to_suspend ,@v_is_sucessfully_process_marked_active  as  is_sucessfully_process_marked_active, @v_is_sucessfully_process_marked_to_expire as is_sucessfully_process_marked_to_expire, @v_is_sucessfully_process_marked_expired as is_sucessfully_process_marked_expired, @v_is_sucessfully_process_marked_expired_debtor as is_sucessfully_process_marked_expired_debtor, @v_is_sucessfully_process_expired_visit as v_is_sucessfully_process_expired_visit');
       
 
             // Obtener las caracteristicas de los clientes.
            
             $attendance=  DB::select('SELECT count(*) as i_attendance_counted FROM log_attendance_transaction  WHERE DATE(dt_attendance_transaction) = DATE(?)', [$dt_current_day]);
            
             $payment_transaction=  DB::select('SELECT SUM( IF (i_transaction_type=1 || i_transaction_type=3 , d_amount_for_pay, d_amount) ) as d_sum_d_amount FROM log_membership_payment_transaction WHERE DATE(dt_transaction_payment)= DATE(?)', [$dt_current_day]);
            
             $payment_cash_transaction=  DB::select('SELECT SUM( IF (i_transaction_type=1 || i_transaction_type=3 , d_amount_for_pay, d_amount) ) as d_sum_d_amount FROM log_membership_payment_transaction WHERE DATE(dt_transaction_payment)= DATE(?) AND id_payment_method = 1', [$dt_current_day]);
            
             $clients_to_expire_counted = DB::select('SELECT count(*) AS i_clients_to_expire_counted FROM tb_clients WHERE i_status in (1,3) AND i_pay_status = 2  ');
            
             $clients_expired_counted = DB::select('SELECT count(*) AS i_clients_expired_counted FROM tb_clients WHERE i_status in (1,3) AND i_pay_status in (3)');
            
             $clients_actives= DB::select('SELECT count(*) AS i_clients_actives FROM tb_clients WHERE i_status in (1,3) ');
            
             $clients_in_force= DB::select('SELECT count(*) AS i_clients_actives_in_force FROM tb_clients WHERE i_status in (1,3) AND i_pay_status in (1)');

            
             $reinstatement= DB::select('SELECT COUNT(*) AS i_reinstatement  FROM log_reinstatement WHERE DATE(dt_reinstatement) = DATE(?)', [$dt_current_day]);
            
             $suspend_memberships= DB::select('SELECT COUNT(*) AS i_suspend_memberships  FROM  log_suspend_memberships WHERE DATE(dt_suspend_membership) = DATE(?)', [$dt_current_day]);
             
             $new_memberships= DB::select('SELECT COUNT(*) AS i_new_memberships  FROM  log_memberships  WHERE DATE(dt_start_payment_period) = DATE(?)', [$dt_current_day]);

             $visits= DB::select('SELECT COUNT(*) AS i_visits  FROM log_membership_payment_transaction WHERE DATE(dt_transaction_payment)= DATE(?) AND id_registration_model=0', [$dt_current_day]);

             $perfomance_tests= DB::select('SELECT COUNT(*) AS i_perfomance_tests FROM log_prospect_perfomance_test  WHERE DATE(dt_prospect_perfomance_test_created)= DATE(?)', [$dt_current_day]);

             $clients_debts_counted = DB::select('SELECT count(*) AS i_clients_debts_counted FROM tb_clients WHERE i_status in (1,3) AND (i_pay_status in ( -1 ) || is_debtor=1)');
            
             $prospects_today= DB::select('SELECT COUNT(*) AS i_prospect FROM log_prospect  WHERE DATE(dt_prospect_created)= DATE(?)', [$dt_current_day]);

             $prospects_sub_today= DB::select('SELECT COUNT(*) AS i_prospect FROM log_prospect_portal_subscription  WHERE DATE(dt_prospect_created)= DATE(?)', [$dt_current_day]);

             $email_sended_today= DB::select('SELECT COUNT(*) AS i_total_sended
             FROM log_email_response LER
             WHERE DATE(dt_sended) = DATE(?)', [$dt_current_day]);

            $cupons_created_today= DB::select('SELECT COUNT(*) AS i_cupons FROM tb_promotion_codes  WHERE DATE(dt_created)= DATE(?)', [$dt_current_day]);

            $tracking_notes_total= DB::select('SELECT COUNT(*) AS "i_total" 
            FROM log_tracking_notes LTN  WHERE DATE(LTN.dt_created) = DATE(?);', [$dt_current_day]);

            $package_visit_total= DB::select('SELECT COUNT(*) AS "i_total" 
            FROM tb_clients WHERE  i_status="5";');

            $get_client_session=DB::statement('CALL sp_get_client_sessions( ? , @v_i_total)',[$dt_current_day]);
            $client_sessions_summary=DB::select('SELECT  @v_i_total as i_total');

            
            $get_downloads_routine=DB::statement('CALL sp_get_downloads_routine( ? , @v_i_total)',[$dt_current_day]);
            $downloads_routine_summary=DB::select('SELECT  @v_i_total as i_total');

            $get_schedule_resevation_training=DB::statement('CALL sp_get_schedule_resevation_training( ? , @v_i_total)',[$dt_current_day]);
            $schedule_resevation_training_summary=DB::select('SELECT  @v_i_total as i_total');

            $cupon_expired_today= DB::select('SELECT COUNT(*) AS i_total FROM log_expiration_visit_by_date  WHERE DATE(dt_created)= DATE(?)', [$dt_current_day]);

            
            $schedule_date_perfomance_test_created= DB::select('SELECT COUNT(*) AS "i_total" 
            FROM log_schedule_date_perfomance_test LSPT
            WHERE DATE(dt_create)= DATE(?)', [$dt_current_day]);

            $schedule_date_perfomance_test_for_today= DB::select('SELECT COUNT(*) AS "i_total" 
             FROM log_schedule_date_perfomance_test LSPT
            WHERE DATE(dt_schedule_date_perfomance_test) = DATE(?)', [$dt_current_day]);


            $clients_category= DB::select('SELECT 
            count(*) AS i_total , CCM.i_category, CCMC.vc_category
            FROM
            tb_clients C
                LEFT JOIN
            cat_membership_models CCM ON C.id_last_membership_model = CCM.id_membership_model
            LEFT JOIN 
            cat_membership_models_category CCMC ON CCM.i_category=  CCMC.i_category 
            WHERE C.i_status in (1,3) 
            GROUP BY CCM.i_category,  CCMC.vc_category');


                        
            return   view('home',compact('type_menu','user_system','attendance', 'payment_transaction', 'payment_cash_transaction', 'clients_to_expire_counted','clients_expired_counted', 'clients_actives','clients_in_force','new_memberships','reinstatement','suspend_memberships','visits','clients_debts_counted','perfomance_tests','prospects_today','email_sended_today','prospects_sub_today', 'cupons_created_today','tracking_notes_total', 'package_visit_total', 'client_sessions_summary','downloads_routine_summary','cupon_expired_today', 'schedule_date_perfomance_test_created', 'schedule_date_perfomance_test_for_today','schedule_resevation_training_summary','clients_category' ));
     

        }


      


            public function clients_expired() {
        
                 // Propiedades de Navegacion
                
                 $branch='1';
                 $type_menu='0';
                 $user_system =  \Session::get('vc_user_system');
                 
                 // Obtener la fecha actual
                 $today = Carbon::now();
                 $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                 $dt_current_day->format('Y-M-D');
        
                // Obtenemos el detalle de clientes 
                
                 $clients_actives= DB::select('SELECT count(*) AS i_clients_actives FROM tb_clients WHERE i_status in (1,3)');
                 $clients_expired_counted = DB::select('SELECT count(*) AS i_clients_expired_counted FROM tb_clients WHERE i_status in (1,3) AND i_pay_status in (3)');
                 $clients_expired = DB::select('SELECT C.id_client AS id, vc_name, vc_last_name, vc_sur_name, vc_nick_name, dt_last_attendance, DATE(dt_next_payment) AS dt_next_payment, DATEDIFF(DATE(NOW()),DATE(dt_next_payment)) AS i_days_expired,    C.i_status AS i_status,CS.vc_dashboard_status AS vc_dashboard_status, C.i_pay_status AS i_pay_status, CPS.vc_pay_status AS vc_pay_status, is_debtor, id_last_membership_model, CMM.vc_membership_model AS vc_last_membership_model FROM tb_clients C LEFT JOIN tb_clients_personal_information CPI ON C.id_client = CPI.id_client LEFT JOIN cat_status CS ON C.i_status = CS.i_status LEFT JOIN cat_pay_status CPS ON C.i_pay_status = CPS.i_pay_status LEFT JOIN cat_membership_models CMM ON C.id_last_membership_model = CMM.id_membership_model WHERE C.i_status IN (1,3) AND C.i_pay_status IN (3) ORDER BY i_days_expired  DESC ');
            
                 return   view('dashboard.client_expired',compact('clients_expired','clients_expired_counted','clients_actives', 'type_menu','user_system'));
     
                }


                public function clients_to_expire() {
        
                    // Propiedades de Navegacion
                   
                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
           
                   // Obtenemos el detalle de clientes 
                   
                    $clients_actives= DB::select('SELECT count(*) AS i_clients_actives FROM tb_clients WHERE i_status in (1,3)');
                    $clients_to_expire = DB::select('SELECT C.id_client AS id, vc_name, vc_last_name, vc_sur_name, vc_nick_name, dt_last_attendance, DATE(dt_next_payment) AS dt_next_payment, DATEDIFF(DATE(dt_next_payment), DATE(NOW())) AS i_days_to_expire,   C.i_status AS i_status  ,CS.vc_dashboard_status AS vc_dashboard_status , C.i_pay_status AS i_pay_status  , CPS.vc_pay_status AS vc_pay_status,  is_debtor, id_last_membership_model , CMM.vc_membership_model AS  vc_last_membership_model FROM tb_clients C LEFT JOIN tb_clients_personal_information CPI ON C.id_client = CPI.id_client LEFT JOIN cat_status CS ON C.i_status = CS.i_status  LEFT JOIN cat_pay_status CPS ON C.i_pay_status = CPS.i_pay_status LEFT JOIN cat_membership_models CMM ON C.id_last_membership_model = CMM.id_membership_model  WHERE C.i_status in (1,3) AND C.i_pay_status = 2  ORDER BY i_days_to_expire ASC ');
                    $clients_to_expire_counted = DB::select('SELECT count(*) AS i_clients_to_expire_counted FROM tb_clients WHERE i_status in (1,3) AND i_pay_status = 2  ');
            


                    return   view('dashboard.client_to_expire',compact('clients_to_expire','clients_to_expire_counted','clients_actives', 'type_menu','user_system'));
        
                   }




                   public function clients_debts() {
        
                    // Propiedades de Navegacion
                   
                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
           
                   // Obtenemos el detalle de clientes 
                   
                    $clients_actives= DB::select('SELECT count(*) AS i_clients_actives FROM tb_clients WHERE i_status in (1,3)');
                    $clients_debts = DB::select('SELECT C.id_client AS id, vc_name, vc_last_name, vc_sur_name, vc_nick_name, dt_last_attendance, DATE(dt_next_payment) AS dt_next_payment ,   C.i_status AS i_status  ,CS.vc_dashboard_status AS vc_dashboard_status , C.i_pay_status AS i_pay_status  , CPS.vc_pay_status AS vc_pay_status,  is_debtor, id_last_membership_model , CMM.vc_membership_model AS  vc_last_membership_model FROM tb_clients C LEFT JOIN tb_clients_personal_information CPI ON C.id_client = CPI.id_client LEFT JOIN cat_status CS ON C.i_status = CS.i_status  LEFT JOIN cat_pay_status CPS ON C.i_pay_status = CPS.i_pay_status LEFT JOIN cat_membership_models CMM ON C.id_last_membership_model = CMM.id_membership_model  WHERE C.i_status in (1,3) AND (C.i_pay_status in ( -1 ) || C.is_debtor=1)  ORDER BY dt_next_payment ASC ');
                    $clients_debts_counted = DB::select('SELECT count(*) AS i_clients_debts_counted FROM tb_clients WHERE i_status in (1,3) AND (i_pay_status in ( -1 ) || is_debtor=1) ');
            

                    return   view('dashboard.client_debts',compact('clients_debts','clients_debts_counted','clients_actives', 'type_menu','user_system'));
        
                   }




                public function payments_transaction(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');


                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
            $payment_transactions_detail=  DB::select('SELECT LMPT.id_client , IF(LMPT.id_registration_model!=0 , IF (LMPT.is_registration_transaction=0 , "Renovación de Membresía", "Nueva membresía e inscripción") , "Visita") AS 	vc_pay ,  IF(LMPT.id_registration_model!=0 , IF (LMPT.is_registration_transaction=0, CMM.vc_membership_model, CONCAT(CRM.vc_dashboard_description," + ",CMM.vc_membership_model) )  , CVM.vc_visit_model)  AS vc_pay_detail, CPI.vc_name, CPI.vc_last_name, CPI.vc_sur_name, CPI.vc_nick_name,  CPM.vc_payment_method,   IF (i_transaction_type=1 || i_transaction_type=3, LMPT.d_amount_for_pay, LMPT.d_amount) AS d_sum_d_amount, i_transaction_type , i_last_status, i_last_pay_status, LMPT.id_payment_method AS id_payment_method, dt_transaction_payment, LMPT.id_registration_model AS id_registration_model  FROM log_membership_payment_transaction LMPT LEFT JOIN cat_membership_models CMM ON LMPT.id_membership_model = CMM.id_membership_model LEFT JOIN tb_clients_personal_information CPI ON LMPT.id_client = CPI.id_client LEFT JOIN cat_payment_methods CPM ON LMPT.id_payment_method = CPM.id_payment_method LEFT JOIN log_payment_transaction_visit_details LPTVD ON LMPT.id_membership_payment_transaction=LPTVD.id_membership_payment_transaction LEFT JOIN cat_visit_models CVM  ON CVM.id_visit_model=LPTVD.id_visit_model  LEFT JOIN cat_registration_models CRM ON CMM.id_registration_model = CRM.id_registration_model  WHERE DATE(dt_transaction_payment)>=  DATE(?) AND DATE(dt_transaction_payment)< DATE(?) ORDER BY  dt_transaction_payment ASC', [$dt_start_day, $dt_end_day]);
            $payment_transactions_summary=  DB::select('SELECT DATE(dt_transaction_payment) AS dt_day, SUM(IF( id_payment_method=1,  IF (i_transaction_type=1 || i_transaction_type=3, d_amount_for_pay, d_amount), 0) )AS d_sum_d_amount_cash, SUM(IF(id_payment_method=2, IF (i_transaction_type=1 || i_transaction_type=3, d_amount_for_pay, d_amount), 0)) AS d_sum_d_amount_card, SUM(IF(id_payment_method=3, IF (i_transaction_type=1 || i_transaction_type=3, d_amount_for_pay, d_amount), 0)) AS d_sum_d_amount_pass, SUM(IF(id_payment_method=4, IF (i_transaction_type=1 || i_transaction_type=3, d_amount_for_pay, d_amount), 0)) AS d_sum_d_amount_trasnfer, SUM(IF (i_transaction_type=1 || i_transaction_type=3, d_amount_for_pay, d_amount)) AS d_sum_d_amount_total  FROM log_membership_payment_transaction WHERE DATE(dt_transaction_payment)>= DATE(?) AND DATE(dt_transaction_payment)< DATE(?) GROUP BY 1 ORDER BY dt_day ASC', [$dt_start_day, $dt_end_day]);
            $payment_transactions_summary_total=  DB::select('SELECT  SUM(IF( id_payment_method=1,  IF (i_transaction_type=1 || i_transaction_type=3, d_amount_for_pay, d_amount), 0) )AS d_sum_d_amount_cash, SUM(IF(id_payment_method=2, IF (i_transaction_type=1 || i_transaction_type=3, d_amount_for_pay, d_amount), 0)) AS d_sum_d_amount_card, SUM(IF(id_payment_method=3, IF (i_transaction_type=1 || i_transaction_type=3, d_amount_for_pay, d_amount), 0)) AS d_sum_d_amount_pass, SUM(IF(id_payment_method=4, IF (i_transaction_type=1 || i_transaction_type=3, d_amount_for_pay, d_amount), 0)) AS d_sum_d_amount_trasnfer, SUM(IF (i_transaction_type=1 || i_transaction_type=3, d_amount_for_pay, d_amount)) AS d_sum_d_amount_total  FROM log_membership_payment_transaction WHERE DATE(dt_transaction_payment)>= DATE(?) AND DATE(dt_transaction_payment)< DATE(?) ', [$dt_start_day, $dt_end_day]);
             
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');

            
                 
              return   view('dashboard.payments_transaction',compact('payment_transactions_detail', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r', 'payment_transactions_summary','payment_transactions_summary_total'));
        
                   }



                   public function attendance(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');


                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
     
            $attendance_summary= DB::select('SELECT  COUNT(IF(CPI.i_gender = 1, 1, NULL))  AS "i_gender_man", COUNT(IF(CPI.i_gender = 2, 1, NULL))  AS "i_gender_woman", COUNT(CPI.i_gender)  AS "i_total",  DATE(dt_attendance_transaction) AS dt_attendance_transaction   FROM log_attendance_transaction  LAT LEFT JOIN tb_clients_personal_information CPI ON LAT.id_client = CPI.id_client  WHERE DATE(dt_attendance_transaction)  >= DATE(?)  AND DATE(dt_attendance_transaction) < DATE(?)  GROUP BY 4 ;', [$dt_start_day, $dt_end_day]);
            $attendance_summary_total= DB::select('SELECT  COUNT(IF(CPI.i_gender = 1, 1, NULL))  AS "i_gender_man", COUNT(IF(CPI.i_gender = 2, 1, NULL))  AS "i_gender_woman", COUNT(CPI.i_gender)  AS "i_total" FROM log_attendance_transaction  LAT LEFT JOIN tb_clients_personal_information CPI ON LAT.id_client = CPI.id_client  WHERE DATE(dt_attendance_transaction)  >= DATE(?)  AND DATE(dt_attendance_transaction) < DATE(?) ;', [$dt_start_day, $dt_end_day]);
            $attendance_detail = DB::select('SELECT  dt_attendance_transaction,  remove_accents(vc_name) AS vc_name, remove_accents(vc_last_name) AS vc_last_name, remove_accents(vc_sur_name) AS vc_sur_name, vc_nick_name, dt_last_attendance, DATE(dt_next_payment) AS dt_next_payment, C.i_status AS i_status,CS.vc_dashboard_status AS vc_dashboard_status, C.i_pay_status AS i_pay_status, CPS.vc_pay_status AS vc_pay_status, is_debtor, id_last_membership_model, CMM.vc_membership_model AS vc_last_membership_model, i_visit_period  FROM log_attendance_transaction LAT LEFT JOIN  tb_clients C ON LAT.id_client = C.id_client LEFT JOIN tb_clients_personal_information CPI ON C.id_client = CPI.id_client  LEFT JOIN cat_status CS ON C.i_status = CS.i_status  LEFT JOIN cat_pay_status CPS ON C.i_pay_status = CPS.i_pay_status LEFT JOIN cat_membership_models CMM ON C.id_last_membership_model = CMM.id_membership_model WHERE  DATE(dt_attendance_transaction) = DATE(?) ORDER BY dt_attendance_transaction ASC', [$dt_current_day]);
          
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 


            $dt_lw_start_day= new Carbon('last Week');
            $dt_lw_start_day=$dt_lw_start_day->format('Y-m-d');
            if($today->dayOfWeek == Carbon::MONDAY)
            $dt_lw_end_day = $today;
            else
            $dt_lw_end_day = new Carbon('last monday');
            $dt_lw_end_day=$dt_lw_end_day->format('Y-m-d');
            
            $attendance_last_week= DB::select('SELECT (WEEKDAY(dt_attendance_transaction) + 1) as i_day, count(*) as i_attendance_counted  FROM log_attendance_transaction WHERE DATE(dt_attendance_transaction)  >= DATE(?)  AND DATE(dt_attendance_transaction) < DATE(?)  GROUP BY i_day;', [$dt_lw_start_day, $dt_lw_end_day]);
            $i=0;
            $j=1;
            foreach($attendance_last_week as $i_lw){
                $lw_array[$i_lw->i_day]= array ("i_attendance_counted" => $i_lw->i_attendance_counted   );
                while($j<$i_lw->i_day ||  $j==7){$array[$i]= 0;  $j= $j+1;  $i= $i+1; }
                $array[$i]=(int)$i_lw->i_attendance_counted ;
                $i= $i+1;
                $j= $j+1;
                
            }

            


           
            if($today->dayOfWeek == Carbon::MONDAY)
            $dt_tw_start_day = $today;
            else
            $dt_tw_start_day = new Carbon('last monday');
            $dt_tw_start_day=$dt_tw_start_day->format('Y-m-d');
            $dt_tw_end_day = new Carbon('tomorrow');
            $dt_tw_end_day=$dt_tw_end_day->format('Y-m-d');
            
            $attendance_this_week= DB::select('SELECT (WEEKDAY(dt_attendance_transaction) + 1) as i_day, count(*) as i_attendance_counted  FROM log_attendance_transaction WHERE DATE(dt_attendance_transaction)  >= DATE(?)  AND DATE(dt_attendance_transaction) < DATE(?)  GROUP BY i_day;', [$dt_tw_start_day, $dt_tw_end_day]);
            $i=0;
            $j=1;
            foreach($attendance_this_week as $i_tw){
                $tw_array[$i_tw->i_day]= array ("i_attendance_counted" => $i_tw->i_attendance_counted   );
                while($j<$i_tw->i_day ||  $j==7){$array2[$i]= 0;  $j= $j+1;  $i= $i+1; }
                $array2[$i]=(int)$i_tw->i_attendance_counted ;
                $i= $i+1;
                $j= $j+1;
                    
            }  while($j<7){$array2[$i]= 0;  $j= $j+1;  $i= $i+1; }

            //echo $array2= json_encode($array2);
            //echo json_encode($array, JSON_FORCE_OBJECT);
            
           return   view('dashboard.attendance',compact('attendance_detail','attendance_summary','attendance_summary_total', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r', 'array','array2'));
        
                   }


                   

                   public function reinstatement(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');

                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
     
                          
            $reinstatement_summary= DB::select('SELECT COUNT(*) AS i_total, DATE(dt_reinstatement) AS dt_reinstatement  FROM log_reinstatement WHERE DATE(dt_reinstatement) >= DATE(?) AND  DATE(dt_reinstatement)  < DATE(?) GROUP BY 2', [$dt_start_day, $dt_end_day]);
            $reinstatement_summary_total= DB::select('SELECT COUNT(*) AS i_total  FROM log_reinstatement WHERE DATE(dt_reinstatement) >= DATE(?) AND  DATE(dt_reinstatement)  < DATE(?)', [$dt_start_day, $dt_end_day]);
            $reinstatement_detail = DB::select(' SELECT dt_reinstatement, LR.dt_registration, dt_last_start_payment_period, dt_last_suspend_membership, TIMESTAMPDIFF(MONTH, dt_last_start_payment_period, dt_last_suspend_membership) AS i_period, remove_accents(vc_name) AS vc_name, remove_accents(vc_last_name) AS vc_last_name, remove_accents(vc_sur_name) AS vc_sur_name, vc_nick_name, 
            LMPT.id_membership_model, CMM.vc_membership_model 
            FROM log_reinstatement LR
            JOIN log_membership_payment_transaction LMPT ON LR.id_client = LMPT.id_client AND DATE(dt_reinstatement)= DATE(dt_transaction_payment) AND LMPT.is_registration_transaction!="1"
            LEFT JOIN tb_clients_personal_information CPI ON LMPT.id_client = CPI.id_client
            LEFT JOIN cat_membership_models CMM ON LMPT.id_membership_model = CMM.id_membership_model
            WHERE DATE(dt_reinstatement) >= DATE(?) AND  DATE(dt_reinstatement) < DATE(?)', [$dt_start_day, $dt_end_day]);
          
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 
            return   view('dashboard.reinstatement',compact('reinstatement_detail','reinstatement_summary','reinstatement_summary_total', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
                   }


                   public function suspend(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');

                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
     
                          
            $suspend_summary= DB::select('SELECT COUNT(*) AS i_total, DATE(dt_suspend_membership) AS dt_suspend_membership  FROM log_suspend_memberships WHERE DATE(dt_suspend_membership) >= DATE(?) AND  DATE(dt_suspend_membership)  < DATE(?) GROUP BY 2', [$dt_start_day, $dt_end_day]);
            $suspend_summary_total= DB::select('SELECT COUNT(*) AS i_total  FROM log_suspend_memberships WHERE DATE(dt_suspend_membership) >= DATE(?) AND  DATE(dt_suspend_membership)  < DATE(?)', [$dt_start_day, $dt_end_day]);
            $suspend_detail = DB::select('SELECT LSM.dt_suspend_membership, LSM.dt_registration, LSM.dt_start_payment_period, TIMESTAMPDIFF(MONTH, LSM.dt_start_payment_period, LSM.dt_suspend_membership) AS i_period, remove_accents(vc_name) AS vc_name, remove_accents(vc_last_name) AS vc_last_name, remove_accents(vc_sur_name) AS vc_sur_name, vc_nick_name, CSR.vc_suspended_reasons
            FROM log_suspend_memberships  LSM
            LEFT JOIN tb_clients_personal_information CPI ON LSM.id_client = CPI.id_client
            LEFT JOIN cat_suspended_reasons CSR ON LSM.id_suspended_reason = CSR.id_suspended_reason
            WHERE DATE(LSM.dt_suspend_membership) >= DATE(?) AND  DATE(LSM.dt_suspend_membership)  < DATE(?)', [$dt_start_day, $dt_end_day]);
          
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 
            return   view('dashboard.suspend',compact('suspend_detail','suspend_summary','suspend_summary_total', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
                   }


                   public function new_membership(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');

                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
     
                          
            $new_membership_summary= DB::select('SELECT COUNT(*) AS i_total, DATE(dt_start_payment_period) AS dt_start_payment_period FROM log_memberships  WHERE DATE(dt_start_payment_period) >= DATE(?) AND  DATE(dt_start_payment_period)  < DATE(?) GROUP BY 2', [$dt_start_day, $dt_end_day]);
            $new_membership_summary_total= DB::select('SELECT COUNT(*) AS i_total  FROM log_memberships WHERE DATE(dt_start_payment_period) >= DATE(?) AND  DATE(dt_start_payment_period)  < DATE(?)', [$dt_start_day, $dt_end_day]);
            $new_membership_detail = DB::select('SELECT  dt_start_payment_period, LM.dt_registration, LM.id_client,   remove_accents(vc_name) AS vc_name, remove_accents(vc_last_name) AS vc_last_name, remove_accents(vc_sur_name) AS vc_sur_name, vc_nick_name, CMM.vc_membership_model,  CMM.i_promotion_available ,  CRM.vc_registration_model 
            FROM log_memberships  LM
            JOIN log_membership_payment_transaction  LMPT ON LM.id_client = LMPT.id_client AND DATE(dt_start_payment_period)= DATE(dt_transaction_payment)    AND  LMPT.is_registration_transaction="1"
            JOIN tb_clients_personal_information CPI ON LM.id_client = CPI.id_client 
            LEFT JOIN cat_membership_models CMM ON LMPT.id_membership_model = CMM.id_membership_model
            LEFT JOIN cat_registration_models CRM ON LMPT.id_registration_model = CRM.id_registration_model
            WHERE DATE(dt_start_payment_period) >=  DATE(?) AND  DATE(dt_start_payment_period)  < DATE(?)  ORDER BY dt_start_payment_period ASC ', [$dt_start_day, $dt_end_day]);
          
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 
            return   view('dashboard.new_membership',compact('new_membership_detail','new_membership_summary','new_membership_summary_total', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
                   }



                   public function visit(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');

                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
     
                          
            $visit_summary= DB::select('SELECT  COUNT(*) AS i_total, DATE(dt_transaction_payment) AS dt_transaction_payment  FROM log_membership_payment_transaction  WHERE  id_registration_model=0 AND DATE(dt_transaction_payment)>= DATE(?) AND DATE(dt_transaction_payment)< DATE(?) GROUP BY 2 ORDER BY dt_transaction_payment ASC', [$dt_start_day, $dt_end_day]);
            $visit_summary_total= DB::select('SELECT  COUNT(*) AS i_total  FROM log_membership_payment_transaction  WHERE  id_registration_model=0 AND DATE(dt_transaction_payment)>= DATE(?) AND DATE(dt_transaction_payment)< DATE(?)', [$dt_start_day, $dt_end_day]);
            $visit_detail = DB::select('SELECT LMPT.dt_transaction_payment, LMPT.id_membership_payment_transaction,  LMPT.id_client,  CPI.vc_name, CPI.vc_last_name, CPI.vc_sur_name, CPI.vc_nick_name, LMPT.id_registration_model AS id_registration_model, CRM.vc_registration_model, CMM.vc_membership_model, CVM.id_visit_model, CVM.vc_visit_model
            FROM log_membership_payment_transaction LMPT
            LEFT JOIN cat_membership_models CMM ON LMPT.id_membership_model = CMM.id_membership_model
            LEFT JOIN tb_clients_personal_information CPI ON LMPT.id_client = CPI.id_client
            LEFT JOIN log_payment_transaction_visit_details LPTVD ON LMPT.id_membership_payment_transaction=LPTVD.id_membership_payment_transaction
            LEFT JOIN cat_visit_models CVM ON CVM.id_visit_model=LPTVD.id_visit_model
            LEFT JOIN cat_registration_models CRM ON CMM.id_registration_model = CRM.id_registration_model
            WHERE LMPT.id_registration_model=0 AND DATE(dt_transaction_payment)>= DATE(?) AND DATE(dt_transaction_payment)< DATE(?)
            ORDER BY dt_transaction_payment ASC', [$dt_start_day, $dt_end_day]);
          
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 
            return   view('dashboard.visit',compact('visit_detail','visit_summary_total','visit_summary', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
                   }

                    public function perfomance_tests() {
        
                    // Propiedades de Navegacion
                   
                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
           
                   // Obtenemos el detalle de clientes 
                   
                   $perfomance_tests_total= DB::select('SELECT COUNT(*) AS i_perfomance_test_total FROM log_prospect_perfomance_test');
                    $perfomance_tests= DB::select('SELECT COUNT(*) AS i_perfomance_tests FROM log_prospect_perfomance_test  WHERE DATE(dt_prospect_perfomance_test_created)= DATE(?)', [$dt_current_day]);
                    $perfomance_tests_detail= DB::select('SELECT 
                    LPPT.dt_prospect_perfomance_test_created,
                    LPPT.id_prospect_perfomance_test,
                    LPPT.vc_name,
                    LPPT.vc_last_name,
                    LPPT.vc_sur_name,
                    LPPT.i_age,
                    vc_gender,
                    LPPT.vc_email,
                    vc_previous_experience,
                    vc_customer_goal,
                    vc_workout_plan_name,
                    vc_workout_plan_alias,
                    TCPI.id_client
                    
                FROM
                    log_prospect_perfomance_test LPPT
                        LEFT JOIN
                    cat_gender CG ON LPPT.i_gender = CG.i_gender
                        LEFT JOIN
                    cat_previous_experience CPE ON CPE.id_previous_experience = LPPT.id_previous_experience
                        LEFT JOIN
                    cat_customer_goals CCG ON CCG.id_customer_goal = LPPT.id_customer_goal
                        LEFT JOIN
                    cat_workout_plan CWP ON CWP.id_workout_plan = LPPT.id_workout_plan
                      LEFT JOIN
                   tb_clients_personal_information TCPI ON TCPI.id_prospect_perfomance_test = LPPT.id_prospect_perfomance_test 
                    
                
                ORDER BY dt_prospect_perfomance_test_created DESC');


                    return   view('dashboard.perfomance_tests',compact('perfomance_tests_total','perfomance_tests','perfomance_tests_detail', 'type_menu','user_system'));
        
                   }



                   public function email_sended(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');


                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
     
            $email_sended_summary= DB::select('SELECT COUNT(IF(LER.id_code_response = 0, 1, NULL)) AS "i_sended", COUNT(IF(LER.id_code_response = 2, 1, NULL)) AS "i_empty_mail", COUNT(IF(LER.id_code_response = 3, 1, NULL)) AS "i_pre_sended", COUNT(LER.id_code_response) AS "i_total" ,DATE(dt_sended) AS dt_sended
            FROM log_email_response LER
            LEFT JOIN cat_code_response CCR  ON LER.id_code_response = CCR.id_code_response
            WHERE DATE(dt_sended) >= DATE(?) AND DATE(dt_sended) < DATE(?) GROUP BY 5;', [$dt_start_day, $dt_end_day]);
            $email_sended_summary_total= DB::select('SELECT COUNT(IF(LER.id_code_response = 0, 1, NULL)) AS "i_sended", COUNT(IF(LER.id_code_response = 2, 1, NULL)) AS "i_empty_mail", COUNT(IF(LER.id_code_response = 3, 1, NULL)) AS "i_pre_sended", COUNT(LER.id_code_response) AS "i_total"
            FROM log_email_response LER
            LEFT JOIN cat_code_response CCR  ON LER.id_code_response = CCR.id_code_response
            WHERE DATE(dt_sended) >= DATE(?) AND DATE(dt_sended) < DATE(?);', [$dt_start_day, $dt_end_day]);
            $email_sended_detail = DB::select('SELECT dt_sended, vc_client_type, remove_accents(vc_name) AS vc_name, remove_accents(vc_last_name) AS vc_last_name, remove_accents(vc_sur_name) AS vc_sur_name,
            vc_nick_name,   vc_email, CET.id_email_type AS id_email_type,   CET.vc_email_type AS vc_email_type  , CCR.id_code_response AS id_code_response , CCR.vc_code_response AS vc_code_response
           FROM log_email_response LER
           LEFT JOIN tb_clients C ON LER.id_client = C.id_client
           LEFT JOIN tb_clients_personal_information CPI ON C.id_client = CPI.id_client
           LEFT JOIN cat_code_response CCR ON LER.id_code_response = CCR.id_code_response
           LEFT JOIN cat_email_type CET ON LER.id_email_type = CET.id_email_type
           LEFT JOIN cat_client_type  CCT ON LER.id_client_type= CCT.id_client_type
           WHERE DATE(dt_sended) >= DATE(?) AND DATE(dt_sended) < DATE(?)   AND  LER.id_client_type IN ("3", "4") 
            UNION ALL
            SELECT dt_sended, vc_client_type, remove_accents(vc_name) AS vc_name, remove_accents(vc_last_name) AS vc_last_name, remove_accents(vc_sur_name) AS vc_sur_name,
           "" AS vc_nick_name,  vc_email, CET.id_email_type AS id_email_type,  CET.vc_email_type AS vc_email_type  , CCR.id_code_response AS id_code_response , CCR.vc_code_response AS vc_code_response
           FROM log_email_response LER
           LEFT JOIN log_prospect_perfomance_test LPPT ON LER.id_client = LPPT.id_prospect_perfomance_test
           LEFT JOIN cat_code_response CCR ON LER.id_code_response = CCR.id_code_response
           LEFT JOIN cat_email_type CET ON LER.id_email_type = CET.id_email_type
           LEFT JOIN cat_client_type  CCT ON LER.id_client_type= CCT.id_client_type
           WHERE  DATE(dt_sended) >= DATE(?) AND DATE(dt_sended) < DATE(?)   AND LER.id_client_type="2"
            UNION ALL
            SELECT dt_sended, vc_client_type, remove_accents(vc_name) AS vc_name, remove_accents(vc_last_name) AS vc_last_name, remove_accents(vc_sur_name) AS vc_sur_name,
            ""  AS vc_nick_name,  vc_email, CET.id_email_type AS id_email_type,  CET.vc_email_type AS vc_email_type  , CCR.id_code_response AS id_code_response , CCR.vc_code_response AS vc_code_response
           FROM log_email_response LER
           LEFT JOIN log_prospect LP ON LER.id_client = LP.id_prospect
           LEFT JOIN cat_code_response CCR ON LER.id_code_response = CCR.id_code_response
           LEFT JOIN cat_email_type CET ON LER.id_email_type = CET.id_email_type
           LEFT JOIN cat_client_type  CCT ON LER.id_client_type= CCT.id_client_type
           WHERE DATE(dt_sended) >= DATE(?) AND DATE(dt_sended) < DATE(?)   AND LER.id_client_type="1" 
           UNION ALL
           SELECT dt_sended, vc_client_type, remove_accents(vc_name) AS vc_name, "" AS vc_last_name, "" AS vc_sur_name,
            ""  AS vc_nick_name,  vc_email, CET.id_email_type AS id_email_type,  CET.vc_email_type AS vc_email_type  , CCR.id_code_response AS id_code_response , CCR.vc_code_response AS vc_code_response
           FROM log_email_response LER
           LEFT JOIN log_prospect_portal_subscription LPPS ON LER.id_client = LPPS.id_prospect_portal_subscription
           LEFT JOIN cat_code_response CCR ON LER.id_code_response = CCR.id_code_response
           LEFT JOIN cat_email_type CET ON LER.id_email_type = CET.id_email_type
           LEFT JOIN cat_client_type  CCT ON LER.id_client_type= CCT.id_client_type
           WHERE DATE(dt_sended) >= DATE(?) AND DATE(dt_sended) < DATE(?)   AND LER.id_client_type="5" 
           UNION ALL
           SELECT dt_sended, vc_client_type, remove_accents(vc_name) AS vc_name, "" AS vc_last_name, "" AS vc_sur_name,
            ""  AS vc_nick_name,  vc_email, CET.id_email_type AS id_email_type,  CET.vc_email_type AS vc_email_type  , CCR.id_code_response AS id_code_response , CCR.vc_code_response AS vc_code_response
           FROM log_email_response LER
           LEFT JOIN log_prospect_portal_message  LPPM ON LER.id_client = LPPM.id_prospect_portal_message
           LEFT JOIN cat_code_response CCR ON LER.id_code_response = CCR.id_code_response
           LEFT JOIN cat_email_type CET ON LER.id_email_type = CET.id_email_type
           LEFT JOIN cat_client_type  CCT ON LER.id_client_type= CCT.id_client_type
           WHERE DATE(dt_sended) >= DATE(?) AND DATE(dt_sended) < DATE(?)   AND LER.id_client_type="6" 
           ORDER BY dt_sended ASC', [$dt_start_day, $dt_end_day,$dt_start_day, $dt_end_day, $dt_start_day, $dt_end_day, $dt_start_day, $dt_end_day, $dt_start_day, $dt_end_day  ]);
           
           $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 
            return   view('dashboard.email_sended',compact('email_sended_detail','email_sended_summary','email_sended_summary_total', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
                   }


                   public function prospects() {
        
                    // Propiedades de Navegacion
                   
                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
           
                   // Obtenemos el detalle de clientes 
                   
                    $prospects_total= DB::select('SELECT COUNT(*) AS i_prospect_total FROM log_prospect');
                    $prospects_today= DB::select('SELECT COUNT(*) AS i_prospect FROM log_prospect  WHERE DATE(dt_prospect_created)= DATE(?)', [$dt_current_day]);
                    $prospects_detail= DB::select('SELECT dt_prospect_created, vc_name, vc_last_name, vc_sur_name, i_age, vc_gender, vc_email, vc_cellphone_number,  vc_previous_experience, vc_customer_goal, vc_acquisition_channel
                    FROM log_prospect LP
                    LEFT JOIN cat_gender CG ON LP.i_gender = CG.i_gender
                    LEFT JOIN cat_previous_experience CPE ON CPE.id_previous_experience = LP.id_previous_experience
                    LEFT JOIN cat_customer_goals CCG ON CCG.id_customer_goal = LP.id_customer_goal
                    LEFT JOIN cat_acquisition_channel CAC ON CAC.id_acquisition_channel = LP.id_acquisition_channel
                    ORDER BY dt_prospect_created DESC');


                    return   view('dashboard.prospect',compact('prospects_total','prospects_today','prospects_detail', 'type_menu','user_system'));
        
                   }


                   public function prospects_subscription() {
        
                    // Propiedades de Navegacion
                   
                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
           
                   // Obtenemos el detalle de clientes 
                   
                    $prospects_sub_total= DB::select('SELECT COUNT(*) AS i_prospect_total FROM log_prospect_portal_subscription');
                    $prospects_sub_today= DB::select('SELECT COUNT(*) AS i_prospect FROM log_prospect_portal_subscription  WHERE DATE(dt_prospect_created)= DATE(?)', [$dt_current_day]);
                    $prospects_sub_detail= DB::select('SELECT dt_prospect_created, vc_name ,  vc_email, id_type_prospect, vc_tracking_key, vc_device,  vc_browser
                    FROM log_prospect_portal_subscription  LPPS
                    LEFT JOIN view_cat_tracking_keys VCTK ON LPPS.id_tracking_key= VCTK.id_tracking_key
                    LEFT JOIN view_cat_user_agent_types VCUAT ON LPPS.id_user_agent_type=VCUAT.id_user_agent_type
                    ORDER BY dt_prospect_created DESC');


                    return   view('dashboard.prospect_subscription',compact('prospects_sub_total','prospects_sub_today','prospects_sub_detail', 'type_menu','user_system'));
        
                   }

                   public function cupons() {
        
                    // Propiedades de Navegacion
                   
                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
           
                   // Obtenemos el detalle de clientes 
                   $cupons_created_total= DB::select('SELECT COUNT(*) AS i_cupons_total FROM tb_promotion_codes');
                   $cupons_used_total= DB::select('SELECT COUNT(*) AS i_cupons_used FROM tb_promotion_codes WHERE dt_used!="0000-00-00 00:00:00"');
                   $cupons_created_today= DB::select('SELECT COUNT(*) AS i_cupons_today FROM tb_promotion_codes  WHERE DATE(dt_created)= DATE(?)', [$dt_current_day]);
                   
                    
                    $cupons_detail= DB::select('SELECT 
                    TPC.dt_created,
                    TPC.vc_promotion_code,
                    TPC.dt_expiration,
                    IF(TPC.dt_used = "0000-00-00 00:00:00",
                        0,
                        1) AS i_used,
                    DATE(TPC.dt_used) AS dt_used,
                    CPCC.i_unity,
                    CPT.vc_promotion_type,
                    CPET.vc_promotion_promotion_exchange_type,
                    TPC.id_client_type,
                    TPC.id_client,
                    CASE
                        WHEN TPC.id_client_type LIKE 3 THEN CPI.vc_email
                        WHEN TPC.id_client_type LIKE 4 THEN CPI.vc_email
                        WHEN TPC.id_client_type LIKE 8 THEN CPI.vc_email
                        WHEN TPC.id_client_type LIKE 9 THEN CPI.vc_email
                        WHEN TPC.id_client_type LIKE 1 THEN LP.vc_email
                        WHEN TPC.id_client_type LIKE 2 THEN LPPT.vc_email
                        WHEN TPC.id_client_type LIKE 5 THEN LPPS.vc_email
                    END AS vc_email,
                    CASE
                        WHEN TPC.id_client_type LIKE 3 THEN CPI.vc_cellphone_number
                        WHEN TPC.id_client_type LIKE 4 THEN CPI.vc_cellphone_number
                        WHEN TPC.id_client_type LIKE 8 THEN CPI.vc_cellphone_number
                        WHEN TPC.id_client_type LIKE 9 THEN CPI.vc_cellphone_number
                        WHEN TPC.id_client_type LIKE 1 THEN LP.vc_cellphone_number
                        WHEN TPC.id_client_type LIKE 2 THEN ""
                        WHEN TPC.id_client_type LIKE 5 THEN LPPS.vc_cellphone_number
                    END AS vc_cellphone_number,
                    CASE
                        WHEN TPC.id_client_type LIKE 3 THEN CPI.vc_name
                        WHEN TPC.id_client_type LIKE 4 THEN CPI.vc_name
                        WHEN TPC.id_client_type LIKE 8 THEN CPI.vc_name
                        WHEN TPC.id_client_type LIKE 9 THEN CPI.vc_name
                        WHEN TPC.id_client_type LIKE 1 THEN LP.vc_name
                        WHEN TPC.id_client_type LIKE 2 THEN LPPT.vc_name
                        WHEN TPC.id_client_type LIKE 5 THEN LPPS.vc_name
                    END AS vc_name,
                    TPC.id_client_reference,
                    CPR.vc_name AS vc_name_referred,
                    CPR.vc_last_name AS vc_last_name_referred,
                    CPR.vc_sur_name AS vc_sur_name_referred
                FROM
                    tb_promotion_codes TPC
                        LEFT JOIN
                    tb_clients_personal_information CPI ON TPC.id_client = CPI.id_client
                        AND TPC.id_client_type IN (3 , 4, 8, 9)
                        LEFT JOIN
                    log_prospect LP ON TPC.id_client = LP.id_prospect
                        AND TPC.id_client_type IN (1)
                        LEFT JOIN
                    log_prospect_perfomance_test LPPT ON TPC.id_client = LPPT.id_prospect_perfomance_test
                        AND TPC.id_client_type IN (2)
                        LEFT JOIN
                    log_prospect_portal_subscription LPPS ON TPC.id_client = LPPS.id_prospect_portal_subscription
                        AND TPC.id_client_type IN (5)
                        JOIN
                    cat_promotion_codes_config CPCC ON TPC.id_promotion_config = CPCC.id_promotion_config
                        JOIN
                    cat_promotion_type CPT ON CPCC.i_promotion_type = CPT.i_promotion_type
                        JOIN
                    cat_promotion_exchange_type CPET ON CPCC.i_promotion_exchange_type = CPET.i_promotion_exchange_type
                        LEFT JOIN
                    tb_clients_personal_information CPR ON TPC.id_client_reference = CPR.id_client
                ORDER BY TPC.dt_created DESC ');


                    return   view('dashboard.cupons',compact('cupons_created_total','cupons_used_total','cupons_created_today', 'cupons_detail', 'type_menu','user_system'));
        
                   }


                   public function tracking_notes(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');


                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
     

            $tracking_notes_summary= DB::select('SELECT COUNT(*) AS "i_total" , DATE(dt_created) AS dt_created
            FROM log_tracking_notes LTN
            WHERE DATE(dt_created) >= DATE(?) AND DATE(dt_created) < DATE(?) GROUP BY 2;', [$dt_start_day, $dt_end_day]);

            $tracking_notes_summary_total= DB::select('SELECT COUNT(*) AS "i_total" 
            FROM log_tracking_notes LTN
            WHERE DATE(dt_created) >= DATE(?) AND DATE(dt_created) < DATE(?);', [$dt_start_day, $dt_end_day]);



            $tracking_notes_detail = DB::select('SELECT 
            CPI.id_client,
            LTN.dt_created,
            CPI.vc_name, 
            CPI.vc_nick_name,
            CPI.vc_last_name,
            CPI.vc_sur_name,
            CTM.vc_team_member,
            CTTM.vc_type_team_member,
            CTNT.id_tracking_note_type,
            CTNT.vc_tracking_note_type,
            LTN.vc_tracking_note_title,
            LTN.vc_tracking_note
            FROM
            log_tracking_notes LTN
            LEFT JOIN 
            tb_clients_personal_information CPI ON LTN.id_client = CPI.id_client
                LEFT JOIN
            cat_tracking_note_type CTNT ON LTN.id_tracking_note_type = CTNT.id_tracking_note_type
                LEFT JOIN
            cat_team_member CTM ON LTN.id_team_member = CTM.id_team_member
                LEFT JOIN
            cat_type_team_member CTTM ON CTM.i_type_team_member = CTTM.i_type_team_member
            WHERE LTN.b_status="1" AND DATE(LTN.dt_created) >=  DATE(?) AND  DATE(LTN.dt_created)  < DATE(?)  ORDER BY LTN.dt_created DESC ', [$dt_start_day, $dt_end_day]);
                    
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 
            return   view('dashboard.tracking_notes',compact('tracking_notes_detail','tracking_notes_summary','tracking_notes_summary_total', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
                   }


                   public function package_visit() {
        
                    // Propiedades de Navegacion
                   
                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
           
                   // Obtenemos el detalle de clientes 

                   $package_visit_total= DB::select('SELECT COUNT(*) AS "i_total" 
                   FROM tb_clients WHERE  i_status="5";');

                    //$package_visit_today= DB::select('SELECT COUNT(*) AS "i_total" 
                    //FROM tb_clients WHERE  i_status="5"  AND DATE(dt_created)= DATE(?)', [$dt_current_day]);

                  // $cupons_created_total= DB::select('SELECT COUNT(*) AS i_cupons_total FROM tb_promotion_codes');
                  // $cupons_used_total= DB::select('SELECT COUNT(*) AS i_cupons_used FROM tb_promotion_codes WHERE dt_used!="0000-00-00 00:00:00"');
                  // $cupons_created_today= DB::select('SELECT COUNT(*) AS i_cupons_today FROM tb_promotion_codes  WHERE DATE(dt_created)= DATE(?)', [$dt_current_day]);
                   
                    
                    $package_visit_detail= DB::select('SELECT
                    C.id_client,
                    LPTVD.dt_change,
                    LPTVD.id_membership_payment_transaction,
                    REMOVE_ACCENTS(vc_name) AS vc_name,
                    REMOVE_ACCENTS(vc_last_name) AS vc_last_name,
                    REMOVE_ACCENTS(vc_sur_name) AS vc_sur_name,
                    vc_nick_name,
                    i_visit_period,
                    LPTVD.id_visit_model,
                    CVM.id_visit_model,
                    vc_visit_model,
                    vc_promotion_code
                    FROM
                    tb_clients C
                    LEFT JOIN
                    tb_clients_personal_information CPI ON C.id_client = CPI.id_client
                    LEFT JOIN
                    (SELECT
                    MAX(id_membership_payment_transaction) AS id_membership_payment_transaction,
                    id_client
                    FROM
                    log_membership_payment_transaction
                    GROUP BY id_client) LMPT ON C.id_client = LMPT.id_client
                    LEFT JOIN
                    log_payment_transaction_visit_details LPTVD ON LMPT.id_membership_payment_transaction = LPTVD.id_membership_payment_transaction
                    LEFT JOIN
                    cat_visit_models CVM ON LPTVD.id_visit_model =CVM.id_visit_model
                    LEFT JOIN
                    (SELECT
                    id_transaction_reference, vc_promotion_code
                    FROM
                    log_promotion_codes_used
                    WHERE
                    i_promotion_type = "3") LPCU ON LMPT.id_membership_payment_transaction = LPCU.id_transaction_reference
                    
                    WHERE
                    C.i_status = "5"
                    AND C.i_visit_period != "0" ORDER BY  LPTVD.dt_change DESC');


                    return   view('dashboard.package_visit',compact('package_visit_total', 'package_visit_detail', 'type_menu','user_system'));
        
                   }


                   public function client_sessions(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');


                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
            
     
            $create_tmp_client_routine=DB::statement('CALL sp_create_tmp_client_sessions_for_dates( ?,?,? , @v_i_total)',[$dt_current_day,$dt_start_day, $dt_end_day ]);
            $client_session_total=DB::select('SELECT  @v_i_total as i_total');


            $client_sessions_summary= DB::select('SELECT i_total ,  dt_created
            FROM tmp_client_session_total_for_dates');

            $client_sessions_detail = DB::select('SELECT
            dt_created,
            DATE(dt_next_payment) AS dt_next_payment,
            id,
            vc_name,
            vc_last_name,
            vc_sur_name,
            vc_nick_name,
            vc_email,
            vc_cellphone_number,
            i_status,
            vc_dashboard_status,
            i_pay_status,
            vc_pay_status,
            is_debtor,
            id_last_membership_model,
            vc_last_membership_model,
            id_last_workout_plan,
            vc_workout_plan_name
            FROM
            tmp_client_session_for_dates');
                    
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 
            return   view('dashboard.client_sessions',compact('client_sessions_detail','client_session_total', 'client_sessions_summary', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
                   }


            public function downloads_routines(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');


                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
            
     
            $create_tmp_downloads_routines=DB::statement('CALL sp_create_tmp_downloads_routines_for_dates( ?,?,? , @v_i_total)',[$dt_current_day,$dt_start_day, $dt_end_day ]);
            $downloads_routines_total=DB::select('SELECT  @v_i_total as i_total');

            $downloads_routines_summary= DB::select('SELECT i_total ,  dt_created
            FROM tmp_downloads_routines_total_for_dates');

            $downloads_routines_detail = DB::select('SELECT
            dt_created,
            vc_workout_day,
            vc_workout_description,
            DATE(dt_next_payment) AS dt_next_payment,
            id,
            vc_name,
            vc_last_name,
            vc_sur_name,
            vc_nick_name,
            vc_email,
            vc_cellphone_number,
            i_status,
            vc_dashboard_status,
            i_pay_status,
            vc_pay_status,
            is_debtor,
            id_last_membership_model,
            vc_last_membership_model,
            id_last_workout_plan,
            vc_workout_plan_name
            FROM
            tmp_downloads_routines_for_dates');
                    
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 
            return   view('dashboard.downloads_routines',compact('downloads_routines_detail','downloads_routines_total', 'downloads_routines_summary', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
                   }

           public function visit_expired() {
        
                    // Propiedades de Navegacion
                   
                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
           
                   // Obtenemos el detalle de clientes 
                   
                    $visit_expired_total= DB::select('SELECT COUNT(*) AS i_total FROM log_expiration_visit_by_date');
                    $visit_expired_today= DB::select('SELECT COUNT(*) AS i_total_today FROM log_expiration_visit_by_date  WHERE DATE(dt_created)= DATE(?)', [$dt_current_day]);
                    $visit_expired_detail= DB::select('SELECT 
                    C.id_client AS id,
                    vc_name,
                    vc_last_name,
                    vc_sur_name,
                    vc_nick_name,
                    dt_last_attendance,
                    DATE(dt_last_payment) AS dt_last_payment,
                    dt_created,
                    LEVD.i_visit_period,
                    CVM.vc_visit_model
                    FROM
                    log_expiration_visit_by_date LEVD
                        LEFT JOIN
                    tb_clients C ON C.id_client = LEVD.id_client
                        LEFT JOIN
                    tb_clients_personal_information CPI ON C.id_client = CPI.id_client
                        LEFT JOIN
                    cat_visit_models CVM ON CVM.id_visit_model = LEVD.id_last_visit_model');


        return   view('dashboard.visit_expired',compact('visit_expired_total','visit_expired_today','visit_expired_detail', 'type_menu','user_system'));
        
                   }     
                   
                   





                   public function schedule_date_perfomance_test(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');


                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
     

            $schedule_date_perfomance_test_created= DB::select('SELECT COUNT(*) AS "i_total" 
            FROM log_schedule_date_perfomance_test LSPT
            WHERE DATE(dt_create) >= DATE(?) AND DATE(dt_create) < DATE(?) ;', [$dt_start_day, $dt_end_day]);

            $schedule_date_perfomance_test_for_today= DB::select('SELECT COUNT(*) AS "i_total" 
             FROM log_schedule_date_perfomance_test LSPT
            WHERE DATE(dt_schedule_date_perfomance_test) >= DATE(?) AND DATE(dt_schedule_date_perfomance_test) < DATE(?);', [$dt_start_day, $dt_end_day]);

            $schedule_date_perfomance_test_summary= DB::select('SELECT COUNT(*) AS "i_total" , DATE(dt_create) AS dt_create
            FROM log_schedule_date_perfomance_test LSPT
            WHERE DATE(dt_create) >= DATE(?) AND DATE(dt_create) < DATE(?) GROUP BY 2;', [$dt_start_day, $dt_end_day]);



            $schedule_date_perfomance_test_detail = DB::select(' SELECT 
                    CPI.id_client,
                    LSPT.id_client_type,
                    CPI.vc_name,
                    CPI.vc_nick_name,
                    CPI.vc_last_name,
                    CPI.vc_sur_name,
                    CPI.vc_cellphone_number,
                    LSPT.dt_create,
                    DATE(LSPT.dt_schedule_date_perfomance_test) AS dt_schedule_date_perfomance_test,
                    DAY(LSPT.dt_schedule_date_perfomance_test) AS dt_schedule_day_perfomance_test,
                    LSPT.vc_day,
                    LSPT.vc_hour,
                    LSPT.i_status
                FROM
                    log_schedule_date_perfomance_test LSPT
                        LEFT JOIN
                    tb_clients_personal_information CPI ON LSPT.id_client = CPI.id_client
                WHERE
                    DATE(LSPT.dt_create) >= DATE(?)
                        AND DATE(LSPT.dt_create) < DATE(?)
                        AND LSPT.id_client_type IN ("3" , "4") 
                UNION ALL 
                SELECT 
                    LPPS.id_prospect_portal_subscription,
                    LSPT.id_client_type,
                    LPPS.vc_name,
                    "" AS vc_nick_name,
                    "" AS vc_last_name,
                    "" AS vc_sur_name,
                    LPPS.vc_cellphone_number,
                    LSPT.dt_create,
                    DATE(LSPT.dt_schedule_date_perfomance_test) AS dt_schedule_date_perfomance_test,
                    DAY(LSPT.dt_schedule_date_perfomance_test) AS dt_schedule_day_perfomance_test,
                    LSPT.vc_day,
                    LSPT.vc_hour,
                    LSPT.i_status
                FROM
                    log_schedule_date_perfomance_test LSPT
                        LEFT JOIN
                    log_prospect_portal_subscription LPPS ON LSPT.id_client = LPPS.id_prospect_portal_subscription
                WHERE
                    DATE(LSPT.dt_create) >= DATE(?)
                        AND DATE(LSPT.dt_create) < DATE(?)
                        AND LSPT.id_client_type IN ("1", "5", "4")
                ORDER BY dt_create DESC;', [$dt_start_day, $dt_end_day, $dt_start_day, $dt_end_day]);
                    
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 
            return   view('dashboard.schedule_date_perfomance_test',compact('schedule_date_perfomance_test_created','schedule_date_perfomance_test_for_today','schedule_date_perfomance_test_summary', 'schedule_date_perfomance_test_detail', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
                   }


                   public function schedule_resevation_training(Request $i_option) {
        
                    // Propiedades de Navegacion
                   
                    $i_period = $i_option->input('i_period');


                    $branch='1';
                    $type_menu='0';
                    $user_system =  \Session::get('vc_user_system');
                    
                    // Obtener la fecha actual
                    // $today = Carbon::now();
                    $today = Carbon::now();
                    $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
                    $dt_current_day->format('Y-M-D');
                    $i_add_days=-1;

                    if($i_period == '1') {
                    $name = 'Hoy';
                    $dt_start_day= Carbon::now();
                    $dt_start_day=$dt_start_day->format('Y-m-d');
                    $dt_end_day= new Carbon('tomorrow');
                    $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                 

                     if($i_period=='2') {
                     $name = 'Esta semana';
                     //$today = new Carbon();
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_start_day = $today;
                     else
                     $dt_start_day = new Carbon('last monday');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='3') {
                     $name = 'Semana anterior';
                     $dt_start_day= new Carbon('last Week');
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     if($today->dayOfWeek == Carbon::MONDAY)
                     $dt_end_day = $today;
                     else
                     $dt_end_day = new Carbon('last monday');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='4') {
                     $name = 'Todo el mes';
                     $dt_start_day= Carbon::now()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= new Carbon('tomorrow');
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }

                     if($i_period=='5') {
                     $name = 'Mes anterior';
                     $dt_start_day= Carbon::now()->subMonth()->startOfMonth();
                     $dt_start_day=$dt_start_day->format('Y-m-d');
                     $dt_end_day= Carbon::now()->startOfMonth();
                     $dt_end_day=$dt_end_day->format('Y-m-d');
                     }
                     // Obtenemos el detalle de clientes
                   
            
     
            $create_tmp_schedule_resevation_training=DB::statement('CALL sp_create_tmp_schedule_resevation_training_for_dates( ?,?,? , @v_i_total)',[$dt_current_day,$dt_start_day, $dt_end_day ]);
            $schedule_resevation_training_total=DB::select('SELECT  @v_i_total as i_total');


            $schedule_resevation_training_summary= DB::select('SELECT i_total ,  dt_create
            FROM tmp_schedule_resevation_training_total_for_dates');

            $schedule_resevation_training_detail = DB::select('SELECT
            DATE(dt_schedule_resevation_training) AS  dt_schedule_resevation_training,
            DAY(dt_schedule_resevation_training) AS day_schedule_resevation_training,
            vc_day,
            vc_hour,
            dt_create,
            DATE(dt_next_payment) AS dt_next_payment,
            id,
            vc_name,
            vc_last_name,
            vc_sur_name,
            vc_nick_name,
            vc_email,
            vc_cellphone_number,
            i_status,
            vc_dashboard_status,
            i_pay_status,
            vc_pay_status,
            is_debtor,
            id_last_membership_model,
            vc_last_membership_model,
            id_last_workout_plan,
            vc_workout_plan_name
            FROM
            tmp_schedule_resevation_training_for_dates ');
                    
            $dt_end_day_r = new Carbon($dt_end_day);
            $dt_end_day_r->addDays($i_add_days);
            $dt_end_day_r= $dt_end_day_r->format('Y-m-d');
                 
            return   view('dashboard.schedule_resevation_training',compact('schedule_resevation_training_detail','schedule_resevation_training_total', 'schedule_resevation_training_summary', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
                   }



}
