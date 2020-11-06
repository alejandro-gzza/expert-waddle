<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchAttendanceController extends Controller
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
        $type_menu='1';
        $user_system =  \Session::get('vc_user_system');
        $today = Carbon::now();
        $dt_current_day=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
        
        //OBTENER RESERVACIONES
        $resevation_training=DB::statement('CALL sp_create_tmp_resevation_attendance(?)', [$dt_current_day] );

            // Obtener las caracteristicas de los clientes.
            $clients = DB::select('SELECT 
            C.id_client AS id,
            REMOVE_ACCENTS(vc_name) AS vc_name,
            REMOVE_ACCENTS(vc_last_name) AS vc_last_name,
            REMOVE_ACCENTS(vc_sur_name) AS vc_sur_name,
            vc_nick_name,
            TIMESTAMPDIFF(HOUR,
                dt_last_attendance,
               ?) AS i_time_attendance,
            "0" AS   i_weekly_average,
            dt_last_attendance,
            dt_last_check_out,
            IF(DATE(dt_last_attendance)=  DATE(dt_last_check_out), TIMESTAMPDIFF(MINUTE,
        dt_last_attendance,  dt_last_check_out) ,0) AS  i_time_average,
            DATE(dt_next_payment) AS dt_next_payment,
            C.i_status AS i_status,
            CS.vc_dashboard_status AS vc_dashboard_status,
            C.i_pay_status AS i_pay_status,
            CPS.vc_pay_status AS vc_pay_status,
            is_debtor,
            id_last_membership_model,
            CMM.vc_membership_model AS vc_last_membership_model,
            i_visit_period,
            TSRA.dt_schedule_resevation_training,
            TSRA.vc_day,
            TSRA.vc_hour,
            TSRA.dt_create,
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
            cat_workout_plan CWP ON CWP.id_workout_plan = CPI.id_last_workout_plan
                LEFT JOIN
            tmp_schedule_resevation_attendance TSRA ON C.id_client = TSRA.id_client
        WHERE
            C.i_status IN (1 , 3, 5)
        ORDER BY C.id_client',[$dt_current_day ]);
           
            // Catalogo estados
        // return  $date ;

           return  view('attendance.index',compact('clients', 'type_menu','user_system'));
     
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
    public function store($id_client)
    {
         // Propiedades de Navegacion
         $branch='1';
         $type_menu='1';
         $user_system =  \Session::get('vc_user_system');
         
        // $today = Carbon::now();
        // $dt_current_day=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
         
             // Obtener las caracteristicas de los clientes.
       // $clients = DB::select('SELECT C.id_client AS id, remove_accents(vc_name), remove_accents(vc_last_name), remove_accents(vc_sur_name), dt_last_attendance, DATE(dt_next_payment) AS dt_next_payment, C.i_status AS i_status  ,CS.vc_dashboard_status AS vc_dashboard_status , C.i_pay_status AS i_pay_status  , CPS.vc_pay_status AS vc_pay_status,  is_debtor, id_last_membership_model , CMM.vc_membership_model AS  vc_last_membership_model FROM tb_clients C LEFT JOIN tb_clients_personal_information CPI ON C.id_client = CPI.id_client LEFT JOIN cat_status CS ON C.i_status = CS.i_status  LEFT JOIN cat_pay_status CPS ON C.i_pay_status = CPS.i_pay_status LEFT JOIN cat_membership_models CMM ON C.id_last_membership_model = CMM.id_membership_model  WHERE C.i_status in (1,3) ORDER BY C.id_client');
            
             // Catalogo estados
         // return  $date ;
 
            return  view('attendance.index',compact('clients', 'type_menu','user_system'));
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
        $type_menu='1';
        $user_system =  \Session::get('vc_user_system');

        
        
        $today = Carbon::now();
        $dt_attendance=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');

        if(!empty($id_client)){

            $statment_attendance=DB::statement('CALL sp_create_new_attendance (?,?,?, @id_attendance)',[$branch, $id_client,  $dt_attendance  ]);
            $attendance_result=DB::select('select @id_attendance as new_attendance');
    }


        $get_data_membership=DB::statement('CALL sp_get_membership ( ? ,?, @v_vc_name,  @v_vc_last_name,  @v_vc_sur_name, @v_i_period, @v_i_status,  @v_vc_status,  @v_i_pay_status,  @v_vc_pay_status,  @v_dt_registration, @v_dt_start_payment_period,  @v_dt_next_payment, @v_is_debtor, @v_id_last_membership_model, @v_vc_last_membership_model )',[ $branch, $id_client  ]);
        $membership_result=DB::select('select   @v_vc_name as vc_name, @v_vc_last_name as vc_last_name,  @v_i_status as  i_status,   @v_i_pay_status as i_pay_status ,   @v_dt_registration as dt_registration ');
        
        $vc_name = $membership_result[0]->vc_name;
        $vc_last_name = $membership_result[0]->vc_last_name;
        


        return redirect()->route('attendance.store', [$id_client]) -> with('status',' La asistencia de  '. $vc_name.' '.$vc_last_name.' fue generada existosamente.');
      
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
        $type_menu='1';
        $user_system =  \Session::get('vc_user_system');
     
        
        
        $today = Carbon::now();
        $dt_check_out=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');

        if(!empty($id_client)){


            $statment_attendance=DB::statement('CALL sp_create_new_check_out(?,?,?, @id_check_out_transaction)',[$branch, $id_client,  $dt_check_out ]);
            $attendance_result=DB::select('SELECT @id_check_out_transaction AS id_check_out_transaction');
        
        }

       

        $get_data_membership=DB::statement('CALL sp_get_membership ( ? ,?, @v_vc_name,  @v_vc_last_name,  @v_vc_sur_name, @v_i_period, @v_i_status,  @v_vc_status,  @v_i_pay_status,  @v_vc_pay_status,  @v_dt_registration, @v_dt_start_payment_period,  @v_dt_next_payment, @v_is_debtor, @v_id_last_membership_model, @v_vc_last_membership_model )',[ $branch, $id_client  ]);
        $membership_result=DB::select('select   @v_vc_name as vc_name, @v_vc_last_name as vc_last_name,  @v_i_status as  i_status,   @v_i_pay_status as i_pay_status ,   @v_dt_registration as dt_registration ');
        
        $vc_name = $membership_result[0]->vc_name;
        $vc_last_name = $membership_result[0]->vc_last_name;
        


        return redirect()->route('attendance.store', [$id_client]) -> with('status',' La salida  de  '. $vc_name.' '.$vc_last_name.' fue guardada existosamente.');
      
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id_client)
    {
     

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
