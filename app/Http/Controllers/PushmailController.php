<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class PushmailController extends Controller
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
        $type_menu='4';
        $user_system =  \Session::get('vc_user_system');
        

        //Carga de Catalogos

        $visit_package_actives= DB::select('SELECT count(*) AS i_visits_active FROM tb_clients WHERE i_status ="5" AND b_blacklist="0" ');

        $clients_actives= DB::select('SELECT count(*) AS i_clients_active FROM tb_clients WHERE i_status in (1,3) AND b_blacklist="0" ');
            
        $clients_suspend= DB::select('SELECT count(*) AS i_clients_suspend FROM tb_clients WHERE i_status in (2,4) AND b_blacklist="0" ');

        $visit_package_expired= DB::select('SELECT count(*) AS i_visits_expired FROM tb_clients WHERE i_status ="6"  AND b_blacklist="0" ');

        $prospects= DB::select('SELECT count(*) AS i_prospects FROM log_prospect WHERE b_blacklist="0"  ');
         
        $prospects_test= DB::select('SELECT count(*) AS i_prospects_test FROM log_prospect_perfomance_test WHERE id_prospect_perfomance_test NOT IN (SELECT id_prospect_perfomance_test FROM tb_clients_personal_information) AND b_blacklist="0"  ');

        $prospects_portal_subscription= DB::select('SELECT count(*) AS i_portal_prospect FROM log_prospect_portal_subscription WHERE b_blacklist="0" ');

                //Catalogo de modelos de pago con promocion 
                
               $email_type = DB::select('SELECT id_email_type, vc_email_type, vc_email_description, vc_email_subject, vc_email_header, vc_email_body, vc_email_footer, vc_directory_img, is_client_active, is_client_suspend, is_prospect, is_test_prospect, is_visit_package_active, is_visit_package_expired, is_portal_prospect
               FROM cat_email_type WHERE is_push="1" 
               ORDER BY id_email_type ASC');
                
            

         return view('pushmail.index',compact('email_type', 'type_menu','user_system','clients_actives','clients_suspend','prospects', 'prospects_test','prospects_portal_subscription','visit_package_expired','visit_package_actives'));
     
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
        $type_menu='4';
        $user_system =  \Session::get('vc_user_system');


        $today = Carbon::now();
        $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
        $dt_current_day->format('Y-M-D');

         //Validaciones 

        $validatedData = $request->validate([
            'vc_email_subject' => 'required|max:255|regex:/^([!()%:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([!()%:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_email_header' => 'required|max:255|regex:/^([!()%:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([!()%:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_email_body' => 'nullable|max:255',
            'vc_email_footer' => 'nullable|max:255|regex:/^([!()%:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([!()%:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_directory_img' => 'nullable|max:255'
             ]);
    


         //Obteniendo variables post
        $id_email_type=$request->input('id_email_type');
        $vc_email_subject=$request->input('vc_email_subject');
        $vc_email_header=$request->input('vc_email_header');
        $vc_email_body=$request->input('vc_email_body');
        $vc_email_footer=$request->input('vc_email_footer');
        $vc_directory_img=$request->input('vc_directory_img');
        $is_client_active=$request->input('is_client_active');
        $is_client_suspend=$request->input('is_client_suspend');
        $is_prospect=$request->input('is_prospect');
        $is_test_prospect=$request->input('is_test_prospect');
        $is_portal_prospect=$request->input('is_portal_prospect');
        $is_visit_package_active=$request->input('is_visit_package_active');
        $is_visit_package_expired=$request->input('is_visit_package_expired');


        if(empty($vc_directory_img)){$vc_directory_img="";}
    

         //Actualizacion de mensaje
        
        $statment_mail_type=DB::statement('CALL sp_change_mail_type (?,?,?,?,?,?, @v_is_client_active, @v_is_client_suspend, @v_is_prospect, @v_is_test_prospect , @v_is_visit_package_active , @v_is_visit_package_expired ,  @v_is_portal_prospect ,  @v_vc_push_unique_key)', [$id_email_type, utf8_decode( $vc_email_subject),  utf8_decode($vc_email_header),  utf8_decode($vc_email_body), utf8_decode($vc_email_footer),   $vc_directory_img ]);
        $mail_type_result=DB::select('SELECT  @v_is_client_active AS is_client_active_cfg , @v_is_client_suspend AS is_client_suspend_cfg, @v_is_prospect AS is_prospect_cfg, @v_is_test_prospect AS is_test_prospect_cfg, @v_is_visit_package_active  AS  is_visit_package_active_cfg ,  @v_is_visit_package_expired AS  is_visit_package_expired_cfg ,  @v_is_portal_prospect  AS  is_portal_prospect_cfg ,  @v_vc_push_unique_key AS vc_push_unique_key');
        $is_client_active_cfg= $mail_type_result[0]->is_client_active_cfg;
        $is_client_suspend_cfg= $mail_type_result[0]->is_client_suspend_cfg;
        $is_prospect_cfg= $mail_type_result[0]->is_prospect_cfg;
        $is_test_prospect_cfg= $mail_type_result[0]->is_test_prospect_cfg;
        $is_visit_package_active_cfg=  $mail_type_result[0]->is_visit_package_active_cfg;
        $is_visit_package_expired_cfg= $mail_type_result[0]->is_visit_package_expired_cfg;
        $is_portal_prospect_cfg=  $mail_type_result[0]->is_portal_prospect_cfg;
        $vc_push_unique_key= $mail_type_result[0]->vc_push_unique_key;

        //Insercion de volcado de datos por cada caso

        if($is_client_active_cfg==1){

            if(!empty($is_client_active)){

        $id_client_type= 3; 
        $statment_mail_push_active=DB::statement('CALL sp_create_push_message(?,?,?,?,?, @v_i_total_marked)', [ $branch, $dt_current_day, $id_client_type, $id_email_type,$vc_push_unique_key ]);
        $mail_push_active_result=DB::select('SELECT  @v_i_total_marked AS v_i_total_marked');
        // $is_client_active_cfg= $mail_type_result[0]->is_client_active_cfg;

             }
         }


         if($is_client_suspend_cfg==1){

            if(!empty($is_client_suspend)){

        $id_client_type= 4; 
        $statment_mail_push_suspend=DB::statement('CALL sp_create_push_message(?,?,?,?,?, @v_i_total_marked)', [ $branch, $dt_current_day, $id_client_type, $id_email_type,$vc_push_unique_key ]);
        $mail_push_suspend_result=DB::select('SELECT  @v_i_total_marked AS v_i_total_marked');
        // $is_client_active_cfg= $mail_type_result[0]->is_client_active_cfg;

             }
         }


         if($is_prospect_cfg==1){

            if(!empty($is_prospect)){

        $id_client_type= 1; 
        $statment_mail_push_prospect=DB::statement('CALL sp_create_push_message(?,?,?,?,?, @v_i_total_marked)', [ $branch, $dt_current_day, $id_client_type, $id_email_type,$vc_push_unique_key ]);
        $mail_push_prospect_result=DB::select('SELECT  @v_i_total_marked AS v_i_total_marked');
        // $is_client_active_cfg= $mail_type_result[0]->is_client_active_cfg;

             }
         }


         if($is_test_prospect_cfg==1){

            if(!empty($is_test_prospect)){

        $id_client_type= 2; 
        $statment_mail_push_test_prospect=DB::statement('CALL sp_create_push_message(?,?,?,?,?, @v_i_total_marked)', [ $branch, $dt_current_day, $id_client_type, $id_email_type,$vc_push_unique_key ]);
        $mail_push_test_prospect_result=DB::select('SELECT  @v_i_total_marked AS v_i_total_marked');
        // $is_client_active_cfg= $mail_type_result[0]->is_client_active_cfg;

        
             }
         }



         if($is_portal_prospect_cfg==1){

            if(!empty($is_portal_prospect)){

        $id_client_type= 5; 
        $statment_mail_push_test_prospect=DB::statement('CALL sp_create_push_message(?,?,?,?,?, @v_i_total_marked)', [ $branch, $dt_current_day, $id_client_type, $id_email_type,$vc_push_unique_key ]);
        $mail_push_test_prospect_result=DB::select('SELECT  @v_i_total_marked AS v_i_total_marked');
        // $is_client_active_cfg= $mail_type_result[0]->is_client_active_cfg;

        
             }
         }




         if($is_visit_package_active_cfg==1){

            if(!empty($is_visit_package_active)){

        $id_client_type= 8; 
        $statment_mail_push_test_prospect=DB::statement('CALL sp_create_push_message(?,?,?,?,?, @v_i_total_marked)', [ $branch, $dt_current_day, $id_client_type, $id_email_type,$vc_push_unique_key ]);
        $mail_push_test_prospect_result=DB::select('SELECT  @v_i_total_marked AS v_i_total_marked');
        // $is_client_active_cfg= $mail_type_result[0]->is_client_active_cfg;

        
             }
         }




         if($is_visit_package_expired_cfg==1){

            if(!empty($is_visit_package_expired)){

        $id_client_type= 9; 
        $statment_mail_push_test_prospect=DB::statement('CALL sp_create_push_message(?,?,?,?,?, @v_i_total_marked)', [ $branch, $dt_current_day, $id_client_type, $id_email_type,$vc_push_unique_key ]);
        $mail_push_test_prospect_result=DB::select('SELECT  @v_i_total_marked AS v_i_total_marked');
        // $is_client_active_cfg= $mail_type_result[0]->is_client_active_cfg;

        
             }
         }




         
         
    
         return  redirect()->route('pushmail.show', [$vc_push_unique_key] ) -> with('status','Los datos del tipo de mensaje fueron actualizados correctamente');
    
    
    

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($vc_push_unique_key)
    {
        $branch='1';
        $type_menu='4';
        $user_system =  \Session::get('vc_user_system');

        $get_total_pushed = DB::select('SELECT  IF( SUM(i_total_marked) !="", SUM(i_total_marked) , 0 )   AS i_total FROM log_push_message_created WHERE vc_push_unique_key =? ', [$vc_push_unique_key]);
        


        $get_message_changed = DB::select('SELECT 
        ET.id_email_type,  ET.vc_email_type, ET.vc_email_description, ETC.dt_change
        FROM cat_email_type ET LEFT JOIN log_email_type_changed ETC ON ET.id_email_type=ETC.id_email_type
        WHERE ETC.vc_push_unique_key=?', [$vc_push_unique_key]);

        $get_total_pushed_segment = DB::select('SELECT  IF( SUM(i_total_marked) !="", SUM(i_total_marked) , 0 )   AS i_total , id_client_type  FROM log_push_message_created WHERE vc_push_unique_key=?  GROUP BY id_client_type', [$vc_push_unique_key]);
        

        
        return view('pushmail.show',compact( 'type_menu','user_system','get_total_pushed', 'get_message_changed','get_total_pushed_segment'));

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
