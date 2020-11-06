<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;


class entityController extends Controller
{
    public function home() {
        // Propiedades de Navegacion
        
        $branch='1';
        $type_menu='0';
       
        
        $membership_models = DB::select('SELECT id_membership_model, vc_membership_model, vc_dashboard_description, round(d_amount, 2) as d_amount , round ((i_percent_off *100),0) as i_percent_off FROM cat_membership_models WHERE b_status = 1  ORDER BY i_promotion_available ASC');
         

        // return  $type_menu;
         return view('showstates/index',compact('type_menu','user_system','membership_models'));

        }

        public function byChangeDate($dt_next_payment, $i_add_days) {
          // Propiedades de Navegacion
          
          $branch='1';
          $type_menu='0';
     
          
          $dt_new_next_payment = new Carbon($dt_next_payment);
          $dt_new_next_payment->addDays($i_add_days);
          $dt_new_next_payment= $dt_new_next_payment->format('Y-m-d');
          $js_new_next_payment[$i_add_days]= array ("dt_new_next_payment" =>   $dt_new_next_payment);

          return  $js_new_next_payment;
           
  
          }

        public function byMemberhip() {
            // Propiedades de Navegacion
            
            $branch='1';
            $type_menu='0';
          
            
            $membership_models = DB::select('SELECT id_membership_model_client, vc_membership_model_client , id_registration_model, vc_registration_description,  DATE(v_dt_next_payment_created) v_dt_next_payment_created , CONVERT(d_amount ,DECIMAL(20,2)) d_amount , CONVERT(d_reg_amount ,DECIMAL(20,2)) d_reg_amount ,   CONVERT((d_reg_amount + d_amount),DECIMAL(20,2))  d_total_amount , i_promotion_period_for_pay ,  if(i_promotion_period_for_pay=0,"No aplica", i_promotion_period_for_pay) vc_promotion_period_for_pay FROM tmp_membership_model_for_client ');
            
            // $id_membership= array( 'id',)

           // foreach($membership_models as $i){
             //   $id_membership['id']= $i->id_membership_model_client  ;}

            // $resources = array(
              //  array("name" => "Resource 1", "names" => "resource1"),
               // array("name" => "Resource 2", "names"=> "resource2")
               // );

            foreach($membership_models as $i_mm){
                $cat_membership_client[$i_mm->id_membership_model_client]= array ("vc_membership_model_client" => utf8_encode($i_mm->vc_membership_model_client), "dt_next_payment_created" =>  $i_mm->v_dt_next_payment_created , "d_amount" =>  $i_mm->d_amount, "id_registration_model"=>  $i_mm->id_registration_model, "vc_registration_description"=> utf8_encode($i_mm->vc_registration_description), "d_reg_amount"=>  $i_mm->d_reg_amount, "d_total_amount"=>  $i_mm->d_total_amount, "i_promotion_period_for_pay"=>  $i_mm->i_promotion_period_for_pay, "vc_promotion_period_for_pay"=>  utf8_encode($i_mm->vc_promotion_period_for_pay)   );
          }
    
            // return  $type_menu;
             return $cat_membership_client ;
    
            }

            public function byVisit() {
              // Propiedades de Navegacion
              
              $branch='1';
              $type_menu='0';

              

              $visit_models = DB::select('SELECT id_visit_model, vc_dashboard_description, CONVERT(d_amount ,DECIMAL(20,2)) d_amount, round ((i_percent_off *100),0) as i_percent_off , i_limit_period  FROM cat_visit_models WHERE b_status=1');
        
              
              // $id_membership= array( 'id',)
  
             // foreach($membership_models as $i){
               //   $id_membership['id']= $i->id_membership_model_client  ;}
  
              // $resources = array(
                //  array("name" => "Resource 1", "names" => "resource1"),
                 // array("name" => "Resource 2", "names"=> "resource2")
                 // );
  
              foreach($visit_models as $i_mm){
                  $cat_visit_models[$i_mm->id_visit_model]= array ("vc_dashboard_description" => utf8_encode($i_mm->vc_dashboard_description),"d_amount" => $i_mm->d_amount,"i_percent_off" => $i_mm->i_percent_off,"i_limit_period" => $i_mm->i_limit_period     );
            }
      
              // return  $type_menu;
               return  $cat_visit_models ;
      
              }


              public function byEmail_type() {
                // Propiedades de Navegacion
                
                $branch='1';
                $type_menu='0';
              
                
                
                $email_type = DB::select('SELECT id_email_type, vc_email_type, vc_email_description, vc_email_subject, vc_email_header, vc_email_body, vc_email_footer, vc_directory_img, is_client_active, is_client_suspend, is_prospect, is_test_prospect,  is_visit_package_active, is_visit_package_expired, is_portal_prospect 
                FROM cat_email_type WHERE is_push="1" 
                ORDER BY id_email_type ASC');
                
                
                foreach($email_type as $i_et){
                    $cat_email_type[$i_et->id_email_type]= array ("vc_email_type" => utf8_encode($i_et->vc_email_type), "vc_email_description" =>  utf8_encode($i_et->vc_email_description) , "vc_email_subject" => utf8_encode($i_et->vc_email_subject),  "vc_email_header" => utf8_encode($i_et->vc_email_header), "vc_email_body" => utf8_encode($i_et->vc_email_body), "vc_email_footer" => utf8_encode($i_et->vc_email_footer), "vc_directory_img" => utf8_encode($i_et->vc_directory_img), "is_client_active"=>  $i_et->is_client_active, "is_client_suspend"=>  $i_et->is_client_suspend,  "is_prospect"=>  $i_et->is_prospect, "is_test_prospect"=>  $i_et->is_test_prospect , "is_visit_package_active"=>  $i_et->is_visit_package_active  , "is_visit_package_expired"=>  $i_et->is_visit_package_expired ,"is_portal_prospect"=>  $i_et->is_portal_prospect  );
              }
        
        

                 return $cat_email_type;
        
                }

                
                public function byPromotionCupon($vc_cupon_promotion) {
                  // Propiedades de Navegacion
                  
                  $branch='1';
                  $type_menu='0';
                  $id_promotion='1';
                  $v_i_promotion_exchange_type='0';
                  $v_is_expired='1';
                  $v_is_used='1';
                  $v_id_promotion_code='0';
                  $v_dt_expiration='0000-00-00';
                  $v_dt_used='0000-00-00';
                  $v_d_promotion_amount='0.00';
                  $v_i_percent_off='.00';
                  $v_i_promotion_type='0';
             
                 
                $feature_promotion_code[$id_promotion]= array ("i_promotion_exchange_type" =>   $v_i_promotion_exchange_type, "is_expired" =>  $v_is_expired, "is_used" =>  $v_is_used, "id_promotion_code" =>  $v_id_promotion_code, "dt_expiration" =>  $v_dt_expiration , "dt_used" =>  $v_dt_used , "d_promotion_amount" => $v_d_promotion_amount,  "i_percent_off" =>$v_i_percent_off,  "i_promotion_type" =>$v_i_promotion_type );
            
                  
                $promotion_code = DB::select('SELECT  "1" AS id,  IF( TPC.dt_expiration< NOW(), 1,0) AS is_expired, IF(TPC.dt_used!="000-00-00 00:00:00",1,0) AS is_used,  CPC.i_promotion_exchange_type, TPC.id_promotion_code , TPC.dt_expiration, TPC.dt_used, TPC.i_percent_off, TPC.d_promotion_amount,TPC.i_promotion_days, CPT.i_promotion_type,  CPT.vc_promotion_type,
                CPET.vc_promotion_promotion_exchange_type
                FROM
                tb_promotion_codes TPC
                LEFT JOIN
                cat_promotion_codes_config CPC ON TPC.id_promotion_config=CPC.id_promotion_config
                LEFT JOIN
                cat_promotion_type CPT ON CPC.i_promotion_type = CPT.i_promotion_type
                LEFT JOIN
                cat_promotion_exchange_type CPET ON CPC.i_promotion_exchange_type = CPET.i_promotion_exchange_type
                WHERE TPC.vc_promotion_code=?
                LIMIT 1', [$vc_cupon_promotion] );
                                
                if($promotion_code!='')
                  {
                foreach($promotion_code as $i_pc){
                  
                $feature_promotion_code[$i_pc->id]= array ("i_promotion_exchange_type" =>  $i_pc->i_promotion_exchange_type, "is_expired" =>  $i_pc->is_expired, "is_used" =>  $i_pc->is_used, "id_promotion_code" =>  $i_pc->id_promotion_code, "dt_expiration" =>  $i_pc->dt_expiration , "dt_used" =>  $i_pc->dt_used , "d_promotion_amount" => $i_pc->d_promotion_amount,  "i_percent_off" =>$i_pc->i_percent_off,  "i_promotion_type" =>$i_pc->i_promotion_type );  }
                 
              } 


                  //$dt_new_next_payment = new Carbon($dt_next_payment);
                 // $dt_new_next_payment->addDays($i_add_days);
                  // $dt_new_next_payment= $dt_new_next_payment->format('Y-m-d');
                  // $js_new_next_payment[$i_add_days]= array ("dt_new_next_payment" =>   $dt_new_next_payment);
        
                  return  $feature_promotion_code;
                   
          
                  }

                  

}
