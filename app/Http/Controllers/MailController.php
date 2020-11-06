<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
// Use Mail;
Use Session;
Use Redirect; 
use SendGrid\Mail\Mail;
use SendGrid;


class MailController extends Controller
{
    public function home() {
        // Propiedades de Navegacion
        
        $branch='1';
        $type_menu='0';
        $data='1';
        //$id_code_response='1';  

        $dt_enqueue = Carbon::now();
        $dt_enqueue_time =  Carbon::createFromFormat('Y-m-d H:i:s',  $dt_enqueue, 'America/Mexico_City');
       

        // $tb_email_queue = DB::select('SELECT EQ.id_email_queue, EQ.id_client, EQ.id_email_type, EQ.id_client_type, EQ.id_transaction_reference, EQ.id_promotion_code, EQ.id_schedule_reference FROM tb_email_queue EQ
        // JOIN cat_email_type ET ON EQ.id_email_type=ET.id_email_type AND ET.b_status>0  WHERE EQ.id_session="" AND EQ.dt_dequeue="0000-00-00 00:00:00"  ORDER BY ET.i_cardinality ASC LIMIT 2');
        
          $tb_email_queue = DB::select('SELECT 
              EQ.id_email_queue,
              EQ.id_client,
              EQ.id_email_type,
              EQ.id_client_type,
              EQ.id_transaction_reference,
              EQ.id_promotion_code,
              EQ.id_schedule_reference
             FROM
              tb_email_queue EQ
                  JOIN
              cat_email_type ET ON EQ.id_email_type = ET.id_email_type
                  AND ET.b_status > 0
            WHERE
              EQ.id_session = ""
                  AND EQ.dt_dequeue = "0000-00-00 00:00:00"
                  AND (EQ.dt_delivery_after="0000-00-00 00:00:00" OR EQ.dt_delivery_after <= ?) 
          ORDER BY ET.i_cardinality ASC
          LIMIT 2;',[ $dt_enqueue_time ]);
              


        


        foreach($tb_email_queue as $i_queue)
        {
         $id_email_queue= $i_queue->id_email_queue;
         $id_email_type= $i_queue->id_email_type;
         $id_client= $i_queue->id_client;
         $id_client_type= $i_queue->id_client_type;
         $id_transaction_reference= $i_queue->id_transaction_reference;
         $id_promotion_code= $i_queue->id_promotion_code;
         $id_schedule_reference = $i_queue->id_schedule_reference;
         $b_client=base64_encode($id_client);

         
          $statment_dequeue=DB::statement('CALL sp_get_mail_information_dequeue(?,?,?,?,?,@v_vc_email_html_view,  @v_vc_email_subject, @v_vc_email_cc, @v_vc_email_attach, @v_vc_email_header, @v_vc_email_body, @v_vc_email_footer , @v_vc_directory_img, @v_vc_name, @v_vc_last_name , @v_vc_sur_name , @v_vc_nick_name , @v_vc_email, @v_vc_cellphone_number, @v_dt_next_payment, @v_id_code_response )',[ $dt_enqueue_time ,  $id_email_queue,  $id_client ,  $id_email_type ,  $id_client_type ]);
          $st_deq_result=DB::select('SELECT @v_vc_email_html_view AS vc_email_html_view,  @v_vc_email_subject AS vc_email_subject, @v_vc_email_cc AS vc_email_cc, @v_vc_email_attach AS vc_email_attach, @v_vc_email_header AS  vc_email_header , @v_vc_email_body AS vc_email_body, @v_vc_email_footer AS vc_email_footer, @v_vc_directory_img AS  vc_directory_img,  @v_vc_name AS  vc_name , @v_vc_last_name AS vc_last_name , @v_vc_sur_name AS vc_sur_name , @v_vc_nick_name AS vc_nick_name , @v_vc_email AS vc_email, @v_vc_cellphone_number AS vc_cellphone_number,  @v_dt_next_payment AS dt_next_payment, @v_id_code_response AS id_code_response');
          $id_code_response = $st_deq_result[0]->id_code_response;
          $vc_email_html_view = $st_deq_result[0]->vc_email_html_view;
       

    if($id_promotion_code!=0){

            $get_cupon_data=DB::statement('CALL sp_get_code_promotion_for_email( ? , @v_vc_promotion_code, @v_dt_expiration,  @v_d_promotion_amount, @v_vc_promotion_type,@v_vc_promotion_promotion_exchange_type,@v_vc_code_value)',[ $id_promotion_code ]);
            $cupon_data_result=DB::select('SELECT  @v_vc_promotion_code as vc_promotion_code ,@v_dt_expiration as dt_expiration,  @v_d_promotion_amount  AS d_promotion_amount,  @v_vc_promotion_type as vc_promotion_type ,@v_vc_promotion_promotion_exchange_type as vc_promotion_promotion_exchange_type ,@v_vc_code_value as vc_code_value');
          
            
           }
          else {
            
            $cupon_data_result= [
              "vc_promotion_code" => "1",
              "dt_expiration" => "2",
              "vc_promotion_type" => "1",
              "vc_promotion_promotion_exchange_type" => "2",
              "vc_code_value" => "2",
          ]; 
        }


        if($id_transaction_reference!=0){

        $get_payment_data_membership=DB::statement('CALL sp_get_payment_transaction_for_email( ? , @v_d_amount_for_pay, @v_d_amount_payed, @v_id_payment_method, @v_vc_payment_method,  @v_dt_next_payment, @v_is_debtor, @v_id_membership_model, @v_vc_membership_model, @v_i_promotion_period, @v_is_owner_membership, @v_id_visit_model,  @v_vc_dashboard_description_visit, @v_is_registration_transaction, @v_id_registration_model, @v_vc_registration_model, @v_vc_dashboard_description_reg, @v_i_transaction_type)',[ $id_transaction_reference ]);
        $payment_data_membership_result=DB::select('SELECT  @v_d_amount_for_pay as d_amount_for_pay , @v_d_amount_payed as d_amount_payed , @v_id_payment_method as id_payment_method, @v_vc_payment_method as vc_payment_method,  @v_dt_next_payment as dt_next_payment, @v_is_debtor as is_debtor, @v_id_membership_model as id_membership_model, @v_vc_membership_model as vc_membership_model, @v_i_promotion_period as i_promotion_period, @v_is_owner_membership as is_owner_membership, @v_id_visit_model as id_visit_model,  @v_vc_dashboard_description_visit as vc_dashboard_description_visit, @v_is_registration_transaction as is_registration_transaction, @v_id_registration_model as id_registration_model, @v_vc_registration_model as vc_registration_model, @v_vc_dashboard_description_reg as vc_dashboard_description_reg, @v_i_transaction_type as i_transaction_type');
        
       } else {
        
        $payment_data_membership_result = [
          "1" => "1",
          "2" => "2",
      ];

       }

       if($id_schedule_reference!=0){

          $schedule_data_result=DB::select('SELECT DAY(dt_schedule_date_perfomance_test) AS dt_schedule_day_perfomance_test, DATE(dt_schedule_date_perfomance_test) AS dt_schedule_date_perfomance_test, vc_day, vc_hour FROM  log_schedule_date_perfomance_test WHERE  id_schedule_date_perfomance_test=?  LIMIT 1;', [$id_schedule_reference] );
        
       } else {
        
        $schedule_data_result = [
          "1" => "1",
          "2" => "2",
      ];

       }


          
      if($id_code_response==0){
         
// https://myaccount.google.com/lesssecureapps
           // Mail::send($vc_email_html_view, ['st_deq_result' => $st_deq_result,'payment_data_membership_result'=>$payment_data_membership_result] ,  function ($message) use ($st_deq_result)  {

            $vc_email_subject = utf8_encode($st_deq_result[0]->vc_email_subject);
            $vc_email = $st_deq_result[0]->vc_email;
            $vc_email_cc = $st_deq_result[0]->vc_email_cc;
            $vc_name = utf8_encode($st_deq_result[0]->vc_name);


           //   $message->from('info@forzagravitygym.com', 'Forza Gravity Gym');
            //  $message->subject($vc_name.', '.$vc_email_subject);
           //   $message->to($vc_email)->cc($vc_email_cc);
          //});


          $view=view($vc_email_html_view,compact('st_deq_result','payment_data_membership_result','cupon_data_result', 'schedule_data_result','b_client','id_client_type','id_email_type' ))->render();
 
          $email = new Mail();
          $email->setFrom("contacto@forzagravitygym.com", "Forza Gravity Gym");
          $email->setSubject($vc_name.', '.$vc_email_subject);
          $email->addTo($vc_email, $vc_name);
          $email->addContent("text/plain", "Forza Gravity Gym");
          $email->addContent("text/html",  $view );
          $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));
          try {
              $response = $sendgrid->send( $email);
              $vc_response= $response->statusCode() . " Http_Requet";
              //print_r($response->headers());
              //print $response->body() . "\n";
          } catch (Exception $e) {
              echo 'Caught exception: '.$e->getMessage() ."\n";
          }

      }

          $dt_response = Carbon::now();
          $dt_response_time =  Carbon::createFromFormat('Y-m-d H:i:s',  $dt_response, 'America/Mexico_City');

          $statment_response=DB::statement('CALL sp_put_mail_information_response(?,?,?,?,?,?, @v_is_response_added)',[   $dt_response_time , $id_client ,  $id_email_type ,  $id_client_type,   $id_code_response,  $vc_response]);
          $st_resp_result=DB::select('SELECT @v_is_response_added AS  is_response_added');
 
        }
      
         return  $tb_email_queue;
        //  return view('showstates/index',compact('type_menu','user_system','membership_models'));

        }

      

        public function test_transcation() {
          // Propiedades de Navegacion
          
          $branch='1';
          $type_menu='0';
          $data='1';
          //$id_code_response='1';  
  
          $dt_enqueue = Carbon::now();
          $dt_enqueue_time =  Carbon::createFromFormat('Y-m-d H:i:s',  $dt_enqueue, 'America/Mexico_City');
         
          $tb_email_queue = DB::select('SELECT EQ.id_email_queue, EQ.id_client, EQ.id_email_type, EQ.id_client_type, EQ.id_transaction_reference , EQ.id_promotion_code , EQ.id_schedule_reference FROM tb_email_queue EQ
          JOIN cat_email_type ET ON EQ.id_email_type=ET.id_email_type AND ET.b_status>0  WHERE EQ.id_session="TEST"   ORDER BY ET.i_cardinality ASC LIMIT 1');
       

         $id_email_queue= $tb_email_queue[0]->id_email_queue;
         $id_email_type= $tb_email_queue[0]->id_email_type;
         $id_client= $tb_email_queue[0]->id_client;
         $id_client_type= $tb_email_queue[0]->id_client_type;
         $id_transaction_reference= $tb_email_queue[0]->id_transaction_reference;
         $id_promotion_code= $tb_email_queue[0]->id_promotion_code;
         $id_schedule_reference = $tb_email_queue[0]->id_schedule_reference;
         $b_client=base64_encode($id_client);
           
         $statment_dequeue=DB::statement('CALL sp_get_mail_information_test(?,?,?,?,?,@v_vc_email_html_view,  @v_vc_email_subject, @v_vc_email_cc, @v_vc_email_attach, @v_vc_email_header, @v_vc_email_body, @v_vc_email_footer , @v_vc_directory_img, @v_vc_name, @v_vc_last_name , @v_vc_sur_name , @v_vc_nick_name , @v_vc_email, @v_vc_cellphone_number, @v_dt_next_payment, @v_id_code_response )',[ $dt_enqueue_time ,  $id_email_queue,  $id_client ,  $id_email_type ,  $id_client_type ]);
         $st_deq_result=DB::select('SELECT @v_vc_email_html_view AS vc_email_html_view,  @v_vc_email_subject AS vc_email_subject, @v_vc_email_cc AS vc_email_cc, @v_vc_email_attach AS vc_email_attach, @v_vc_email_header AS  vc_email_header , @v_vc_email_body AS vc_email_body, @v_vc_email_footer AS vc_email_footer, @v_vc_directory_img AS  vc_directory_img,  @v_vc_name AS  vc_name , @v_vc_last_name AS vc_last_name , @v_vc_sur_name AS vc_sur_name , @v_vc_nick_name AS vc_nick_name , @v_vc_email AS vc_email, @v_vc_cellphone_number AS vc_cellphone_number, @v_dt_next_payment AS dt_next_payment, @v_id_code_response AS id_code_response');
         $id_code_response = $st_deq_result[0]->id_code_response;
         $vc_email_html_view = $st_deq_result[0]->vc_email_html_view;
      

         if($id_promotion_code!=0){

          $get_cupon_data=DB::statement('CALL sp_get_code_promotion_for_email( ? , @v_vc_promotion_code,@v_dt_expiration,  @v_d_promotion_amount, @v_vc_promotion_type,@v_vc_promotion_promotion_exchange_type,@v_vc_code_value)',[ $id_promotion_code ]);
          $cupon_data_result=DB::select('SELECT  @v_vc_promotion_code as vc_promotion_code ,@v_dt_expiration as dt_expiration,  @v_d_promotion_amount  AS d_promotion_amount, @v_vc_promotion_type as vc_promotion_type ,@v_vc_promotion_promotion_exchange_type as vc_promotion_promotion_exchange_type ,@v_vc_code_value as vc_code_value');
        
          
         }
        else {
          
          $cupon_data_result= [
            "vc_promotion_code" => "1",
            "dt_expiration" => "2",
            "vc_promotion_type" => "1",
            "vc_promotion_promotion_exchange_type" => "2",
            "vc_code_value" => "2",
        ]; 
      }


       
          if($id_transaction_reference!=0){
  
          $get_payment_data_membership=DB::statement('CALL sp_get_payment_transaction_for_email( ? , @v_d_amount_for_pay, @v_d_amount_payed, @v_id_payment_method, @v_vc_payment_method,  @v_dt_next_payment, @v_is_debtor, @v_id_membership_model, @v_vc_membership_model, @v_i_promotion_period, @v_is_owner_membership, @v_id_visit_model,  @v_vc_dashboard_description_visit, @v_is_registration_transaction, @v_id_registration_model, @v_vc_registration_model, @v_vc_dashboard_description_reg, @v_i_transaction_type)',[ $id_transaction_reference ]);
          $payment_data_membership_result=DB::select('SELECT  @v_d_amount_for_pay as d_amount_for_pay , @v_d_amount_payed as d_amount_payed , @v_id_payment_method as id_payment_method, @v_vc_payment_method as vc_payment_method,  @v_dt_next_payment as dt_next_payment, @v_is_debtor as is_debtor, @v_id_membership_model as id_membership_model, @v_vc_membership_model as vc_membership_model, @v_i_promotion_period as i_promotion_period, @v_is_owner_membership as is_owner_membership, @v_id_visit_model as id_visit_model,  @v_vc_dashboard_description_visit as vc_dashboard_description_visit, @v_is_registration_transaction as is_registration_transaction, @v_id_registration_model as id_registration_model, @v_vc_registration_model as vc_registration_model, @v_vc_dashboard_description_reg as vc_dashboard_description_reg, @v_i_transaction_type as i_transaction_type');
          
         } else {
          
          $payment_data_membership_result = [
            "1" => "1",
            "2" => "2",
        ]; 
      }
      
      
      if($id_schedule_reference!=0){

        $schedule_data_result=DB::select('SELECT DAY(dt_schedule_date_perfomance_test) AS dt_schedule_day_perfomance_test,  DATE(dt_schedule_date_perfomance_test) AS dt_schedule_date_perfomance_test, vc_day, vc_hour FROM  log_schedule_date_perfomance_test WHERE  id_schedule_date_perfomance_test=?  LIMIT 1;', [$id_schedule_reference] );
      
     } else {
      
      $schedule_data_result = [
        "1" => "1",
        "2" => "2",
    ];

     }

  
      return view($vc_email_html_view ,compact('st_deq_result', 'payment_data_membership_result','cupon_data_result', 'schedule_data_result', 'b_client','id_client_type','id_email_type' ));
  
          }
  
        
       
    


          public function mailing_preview(Request $i_type) {
            // Propiedades de Navegacion
            
            $id_email_type= $i_type->input('id_email_type');

            $branch='1';
            $type_menu='0';
            $data='1';
            $user_system =  \Session::get('vc_user_system');
            

            if(!empty($user_system)){

              $vc_name_dft=$user_system; 
            }
            else {$vc_name_dft="Cerati";}

            $vc_nick_name_dft="test";
    
            $dt_today = Carbon::now();
            $dt_next_payment_df =  Carbon::createFromFormat('Y-m-d H:i:s',  $dt_today, 'America/Mexico_City');
           
            $id_client= '573';
            $id_client_type= '4';
            $b_client=base64_encode($id_client);

           $statment_mail=DB::statement('CALL sp_get_mail_test(?,?,?,?, @v_vc_email_html_view,  @v_vc_email_subject, @v_vc_email_cc, @v_vc_email_attach, @v_vc_email_header, @v_vc_email_body, @v_vc_email_footer , @v_vc_directory_img, @v_vc_name, @v_vc_nick_name , @v_dt_next_payment )',[ $vc_name_dft ,  $vc_nick_name_dft,   $dt_next_payment_df ,  $id_email_type ]);
           $st_deq_result=DB::select('SELECT @v_vc_email_html_view AS vc_email_html_view,  @v_vc_email_subject AS vc_email_subject, @v_vc_email_cc AS vc_email_cc, @v_vc_email_attach AS vc_email_attach, @v_vc_email_header AS  vc_email_header , @v_vc_email_body AS vc_email_body, @v_vc_email_footer AS vc_email_footer, @v_vc_directory_img AS  vc_directory_img,  @v_vc_name AS  vc_name ,   @v_vc_nick_name AS vc_nick_name ,  @v_vc_email AS vc_email,  @v_vc_cellphone_number AS vc_cellphone_number, @v_dt_next_payment AS dt_next_payment');
           
           
           $vc_email_html_view = $st_deq_result[0]->vc_email_html_view;
        
         
             // $payment_data_membership_result=DB::select('SELECT  @v_d_amount_for_pay as d_amount_for_pay , @v_d_amount_payed as d_amount_payed , @v_id_payment_method as id_payment_method, @v_vc_payment_method as vc_payment_method,  @v_dt_next_payment as dt_next_payment, @v_is_debtor as is_debtor, @v_id_membership_model as id_membership_model, @v_vc_membership_model as vc_membership_model, @v_i_promotion_period as i_promotion_period, @v_is_owner_membership as is_owner_membership, @v_id_visit_model as id_visit_model,  @v_vc_dashboard_description_visit as vc_dashboard_description_visit, @v_is_registration_transaction as is_registration_transaction, @v_id_registration_model as id_registration_model, @v_vc_registration_model as vc_registration_model, @v_vc_dashboard_description_reg as vc_dashboard_description_reg, @v_i_transaction_type as i_transaction_type');
            
             $id_promotion_code='1';
             $get_cupon_data=DB::statement('CALL sp_get_code_promotion_for_email( ? , @v_vc_promotion_code,@v_dt_expiration, @v_d_promotion_amount , @v_vc_promotion_type,@v_vc_promotion_promotion_exchange_type,@v_vc_code_value)',[ $id_promotion_code ]);
            $cupon_data_result=DB::select('SELECT  @v_vc_promotion_code as vc_promotion_code ,@v_dt_expiration as dt_expiration,  @v_d_promotion_amount  AS d_promotion_amount ,   @v_vc_promotion_type as vc_promotion_type ,@v_vc_promotion_promotion_exchange_type as vc_promotion_promotion_exchange_type ,@v_vc_code_value as vc_code_value');
         
           
            $schedule_data_result=DB::select('SELECT DAY(dt_schedule_date_perfomance_test) AS dt_schedule_day_perfomance_test, DATE(dt_schedule_date_perfomance_test) AS dt_schedule_date_perfomance_test, vc_day, vc_hour FROM  log_schedule_date_perfomance_test WHERE  id_schedule_date_perfomance_test="1"  LIMIT 1;');
      

            
            $payment_data_membership_result = [
              "d_amount_for_pay" => "1",
              "d_amount_payed" => "2",
          ]; 
   
         
          return   view($vc_email_html_view ,compact('st_deq_result', 'payment_data_membership_result', 'schedule_data_result','cupon_data_result','b_client', 'id_client_type','id_email_type' ));
    
            }
    
        




}
