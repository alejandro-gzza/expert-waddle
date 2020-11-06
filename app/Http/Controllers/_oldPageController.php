<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;


class _oldPageController extends Controller
{


    public function login() {
        // Propiedades de Navegacion
        

         $branch='1';
         $type_menu='0';
        
         if(empty(\Session::get('vc_user_system'))){return view('login');}

         else { return redirect()-> to('dashboard'); }

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
            
             $reinstatement= DB::select('SELECT COUNT(*) AS i_reinstatement  FROM log_reinstatement WHERE DATE(dt_reinstatement) = DATE(NOW())');
            
             $suspend_memberships= DB::select('SELECT COUNT(*) AS i_suspend_memberships  FROM  log_suspend_memberships WHERE DATE(dt_suspend_membership) = DATE(?)', [$dt_current_day]);
             
             $new_memberships= DB::select('SELECT COUNT(*) AS i_new_memberships  FROM  log_memberships  WHERE DATE(dt_start_payment_period) = DATE(?)', [$dt_current_day]);

             $visits= DB::select('SELECT COUNT(*) AS i_visits  FROM log_membership_payment_transaction WHERE DATE(dt_transaction_payment)= DATE(?) AND id_registration_model=0', [$dt_current_day]);

             $perfomance_tests= DB::select('SELECT COUNT(*) AS i_perfomance_tests FROM log_prospect_perfomance_test  WHERE DATE(dt_prospect_perfomance_test_created)= DATE(?)', [$dt_current_day]);

             $clients_debts_counted = DB::select('SELECT count(*) AS i_clients_debts_counted FROM tb_clients WHERE i_status in (1,3) AND (i_pay_status in ( -1 ) || is_debtor=1)');
            

             // Catalogo estados
         // return  $date ;
 
            return   view('home',compact('clients_expired', 'clients_to_expire','type_menu','user_system','attendance', 'payment_transaction', 'payment_cash_transaction', 'clients_to_expire_counted','clients_expired_counted', 'clients_actives','clients_in_force','new_memberships','reinstatement','suspend_memberships','visits','clients_debts_counted','perfomance_tests'));
     
  

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
                 
            return   view('dashboard.attendance',compact('attendance_detail','attendance_summary','attendance_summary_total', 'type_menu','user_system','i_period', 'name','dt_start_day','dt_end_day_r'));
        
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
            $suspend_detail = DB::select('SELECT LSM.dt_suspend_membership, LSM.dt_registration, LSM.dt_start_payment_period, TIMESTAMPDIFF(MONTH, LSM.dt_start_payment_period, LSM.dt_suspend_membership) AS i_period, remove_accents(vc_name) AS vc_name, remove_accents(vc_last_name) AS vc_last_name, remove_accents(vc_sur_name) AS vc_sur_name, vc_nick_name
            FROM log_suspend_memberships  LSM
            LEFT JOIN tb_clients_personal_information CPI ON LSM.id_client = CPI.id_client
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
                   
                    
                    $perfomance_tests= DB::select('SELECT COUNT(*) AS i_perfomance_tests FROM log_prospect_perfomance_test  WHERE DATE(dt_prospect_perfomance_test_created)= DATE(?)', [$dt_current_day]);
                    $perfomance_tests_detail= DB::select('SELECT  dt_prospect_perfomance_test_created, vc_name, vc_last_name, vc_sur_name, i_age, vc_gender, vc_email, vc_previous_experience , vc_customer_goal, vc_workout_plan_name , vc_workout_plan_alias
                   FROM log_prospect_perfomance_test LPPT
                   LEFT JOIN cat_gender CG ON  LPPT.i_gender = CG.i_gender
                   LEFT JOIN cat_previous_experience  CPE ON  CPE.id_previous_experience = LPPT.id_previous_experience
                   LEFT JOIN cat_customer_goals CCG  ON  CCG.id_customer_goal = LPPT.id_customer_goal
                   LEFT JOIN cat_workout_plan CWP  ON CWP.id_workout_plan = LPPT.id_workout_plan  ORDER BY dt_prospect_perfomance_test_created DESC');


                    return   view('dashboard.perfomance_tests',compact('perfomance_tests','perfomance_tests_detail', 'type_menu','user_system'));
        
                   }

}
