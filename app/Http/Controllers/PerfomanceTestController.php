<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerfomanceTestController extends Controller
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
        $type_menu='3';
        $user_system =  \Session::get('vc_user_system');
        

        $i_type_prospect=$request->input('i_type_prospect');
        if(empty( $i_type_prospect)){  $i_type_prospect=0;}

        $id_prospect=$request->input('id_prospect');
        if(empty( $id_prospect)){  $id_prospect=0;}


        //Carga de Catalogos


                 //Experiencia
                $previous_experience = DB::select('SELECT id_previous_experience, vc_previous_experience from cat_previous_experience  where b_status = 1 order by id_previous_experience asc');

                //Metas del prospecto
                $customer_goals = DB::select('SELECT id_customer_goal, vc_customer_goal from cat_customer_goals  where b_status = 1 order by 1 asc');

                //Quien te atendio
                $team_member = DB::select('SELECT id_team_member, vc_team_member from cat_team_member  where b_status = 1 order by 1 asc');

                 //Coach de la prueba
                 $coach = DB::select('SELECT id_team_member, vc_team_member from cat_team_member  where b_status = 1 AND i_type_team_member in(2,3) order by 1 asc');

               //Programa sugerido
               $workout_plan= DB::select('SELECT id_workout_plan, vc_workout_plan_alias from cat_workout_plan  where b_status = 1 order by 1 asc');

                //experiencia de la prueba
                $perfomance_test_service_experience = DB::select('SELECT id_perfomance_test_service_experience, vc_perfomance_test_service_experience from cat_perfomance_test_service_experience where b_status = 1 order by 1 asc');


                $prospect_detail= DB::select('SELECT  id_prospect, vc_name, vc_last_name, vc_sur_name, vc_email, i_age, i_gender , id_previous_experience , id_customer_goal  
                FROM log_prospect WHERE id_prospect=?', [$id_prospect]);


         
            if($i_type_prospect=='0'){

                $id_promotion='0';
                

                    $prospect_detail[$id_promotion]= (object)
                    array( "id_prospect" => "0",
                        "vc_name" => "",
                        "vc_last_name" => "",
                        "vc_sur_name" => "",
                        "vc_email" => "",
                        "i_age" => "",
                        "i_gender" => "",
                        "id_previous_experience" => "0",
                        "id_customer_goal" => "0",
                    );       

               //return      $prospect_detail;
            }
            
      

         if($i_type_prospect=='1'){

             $prospect_detail= DB::select('SELECT 
             id_prospect,
             vc_name,
             vc_last_name,
             vc_sur_name,
             vc_email,
             i_age,
             i_gender,
             id_previous_experience,
             id_customer_goal
         FROM
             log_prospect
         WHERE
             id_prospect =?', [$id_prospect]);
                     
         }


         if($i_type_prospect=='2'){

            $prospect_detail= DB::select('SELECT 
            id_prospect_portal_subscription AS id_prospect,
            vc_name,
            "" AS vc_last_name,
            "" AS vc_sur_name,
            vc_email,
            "" AS i_age,
            "1" AS i_gender,
            "0" AS id_previous_experience,
            "0" AS id_customer_goal
        FROM
            log_prospect_portal_subscription
        WHERE
            id_prospect_portal_subscription =?', [$id_prospect]);
                    
        }

    // return $prospect_detail;
        

         return view('perfomancetest.index',compact( 'previous_experience','customer_goals','team_member','coach','workout_plan', 'perfomance_test_service_experience', 'type_menu','user_system', 'prospect_detail','i_type_prospect' ));
     
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
        $type_menu='3';
        $user_system =  \Session::get('vc_user_system');
        $id_prospect='0';

         //Validaciones 

            
        $validatedData = $request->validate([
            'vc_name' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_last_name' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_sur_name' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_email' => 'required|max:255|email',
            'i_age' => 'required|numeric|between:4,100',
            'i_gender' => 'required|numeric|between:1,2',
            'id_previous_experience' => 'required|numeric',
            'id_customer_goal' => 'required|numeric',

            'i_number_iterations_pll' => 'required|numeric',
            'i_execution_type_pll' => 'required|numeric',
            'i_rhythm_pll' => 'required|numeric',
            'i_velocity_pll' => 'required|numeric',
            'i_average_rom_pll' => 'required|numeric',
            'i_rom_pll' => 'required|numeric',
            'i_type_form_core_pll' => 'required|numeric',
            'vc_test_comments_pll' => 'nullable|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',

            'i_number_iterations_psh' => 'required|numeric',
            'i_execution_type_psh' => 'required|numeric',
            'i_rhythm_psh' => 'required|numeric',
            'i_velocity_psh' => 'required|numeric',
            'i_average_rom_psh' => 'required|numeric',
            'i_rom_psh' => 'required|numeric',
            'i_type_form_core_psh' => 'required|numeric',
            'vc_test_comments_psh' => 'nullable|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',

            'i_number_iterations_cr' => 'required|numeric',
            'i_execution_type_cr' => 'required|numeric',
            'i_rhythm_cr' => 'required|numeric',
            'i_velocity_cr' => 'required|numeric',
            'i_average_rom_cr' => 'required|numeric',
            'i_rom_cr' => 'required|numeric',
            'i_type_form_core_cr' => 'required|numeric',
            'vc_test_comments_cr' => 'nullable|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',

            'i_number_iterations_sqt' => 'required|numeric',
            'i_execution_type_sqt' => 'required|numeric',
            'i_rhythm_sqt' => 'required|numeric',
            'i_velocity_sqt' => 'required|numeric',
            'i_average_rom_sqt' => 'required|numeric',
            'i_rom_cr' => 'required|numeric',
            'i_type_form_core_sqt' => 'required|numeric',
            'vc_test_comments_sqt' => 'nullable|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',

            'id_workout_plan' => 'required|numeric',
            'id_coach' => 'required|numeric',
            'id_perfomance_test_service_experience' => 'required|numeric',
            'id_team_member' => 'required|numeric'
             ]);
    


         //Obteniendo variables post

        $vc_name=$request->input('vc_name');
        $vc_last_name=$request->input('vc_last_name');
        $vc_sur_name=$request->input('vc_sur_name');
        $vc_email=$request->input('vc_email');
        $i_age=$request->input('i_age');
        $i_gender=$request->input('i_gender');
        $id_previous_experience=$request->input('id_previous_experience');
        $id_customer_goal=$request->input('id_customer_goal');

        $i_number_iterations_pll=$request->input('i_number_iterations_pll');
        $i_execution_type_pll=$request->input('i_execution_type_pll');
        $i_rhythm_pll=$request->input('i_rhythm_pll');
        $i_velocity_pll=$request->input('i_velocity_pll');
        $i_average_rom_pll=$request->input('i_average_rom_pll');
        $i_rom_pll=$request->input('i_rom_pll');
        $i_type_form_core_pll=$request->input('i_type_form_core_pll');
        $vc_test_comments_pll=$request->input('vc_test_comments_pll');

        $i_number_iterations_psh=$request->input('i_number_iterations_psh');
        $i_execution_type_psh=$request->input('i_execution_type_psh');
        $i_rhythm_psh=$request->input('i_rhythm_psh');
        $i_velocity_psh=$request->input('i_velocity_psh');
        $i_average_rom_psh=$request->input('i_average_rom_psh');
        $i_rom_psh=$request->input('i_rom_psh');
        $i_type_form_core_psh=$request->input('i_type_form_core_psh');
        $vc_test_comments_psh=$request->input('vc_test_comments_psh');

        $i_number_iterations_sqt=$request->input('i_number_iterations_sqt');
        $i_execution_type_sqt=$request->input('i_execution_type_sqt');
        $i_rhythm_sqt=$request->input('i_rhythm_sqt');
        $i_velocity_sqt=$request->input('i_velocity_sqt');
        $i_average_rom_sqt=$request->input('i_average_rom_sqt');
        $i_rom_sqt=$request->input('i_rom_sqt');
        $i_type_form_core_sqt=$request->input('i_type_form_core_sqt');
        $vc_test_comments_sqt=$request->input('vc_test_comments_sqt');

        $i_number_iterations_cr=$request->input('i_number_iterations_cr');
        $i_execution_type_cr=$request->input('i_execution_type_cr');
        $i_rhythm_cr=$request->input('i_rhythm_cr');
        $i_velocity_cr=$request->input('i_velocity_cr');
        $i_average_rom_cr=$request->input('i_average_rom_cr');
        $i_rom_cr=$request->input('i_rom_cr');
        $i_type_form_core_cr=$request->input('i_type_form_core_cr');
        $vc_test_comments_cr=$request->input('vc_test_comments_cr');

        $id_workout_plan=$request->input('id_workout_plan');
        $id_coach=$request->input('id_coach');
        $id_perfomance_test_service_experience=$request->input('id_perfomance_test_service_experience');
        $id_team_member=$request->input('id_team_member');
        


         //Insercion de datos de prospecto
        
        $statment_prospect=DB::statement('CALL sp_create_new_prospect_perfomance_test (?,?,?,?,?,?,?,?,?,?,?,?,?,?, @id_prospect_perfomance_test)',[ $branch, utf8_decode($vc_name),  utf8_decode($vc_last_name),  utf8_decode($vc_sur_name), $vc_email,  $i_age ,$i_gender,   $id_previous_experience, $id_customer_goal,   $id_workout_plan, $id_coach, $id_perfomance_test_service_experience, $id_team_member , $id_prospect ]);
        $prospect_perfomance_test_result=DB::select('select @id_prospect_perfomance_test as new_prospect_perfomance_test');
        $id_prospect_perfomance_test= $prospect_perfomance_test_result[0]->new_prospect_perfomance_test;


        //Insercion de datos de la prueba, se obtiene los datos del prospecto   que gnero la prueba

       $i_type_perfomance_test='1';
       $prospect_exp_result=DB::statement('CALL sp_create_new_perfomance_test (?,?,?,?,?,?,?,?,?,?,?, @id_perfomance_test)',[ $id_prospect_perfomance_test, $branch,  $i_type_perfomance_test, $i_number_iterations_pll, $i_execution_type_pll, $i_rhythm_pll, $i_velocity_pll,$i_average_rom_pll, $i_rom_pll,  $i_type_form_core_pll,utf8_decode($vc_test_comments_pll) ]);
       $id_perfomance_test=DB::select('select @id_perfomance_test  as new_perfomance_test');
      
       $i_type_perfomance_test='2';
       $prospect_exp_result=DB::statement('CALL sp_create_new_perfomance_test (?,?,?,?,?,?,?,?,?,?,?, @id_perfomance_test)',[ $id_prospect_perfomance_test, $branch,  $i_type_perfomance_test, $i_number_iterations_psh, $i_execution_type_psh, $i_rhythm_psh, $i_velocity_psh,$i_average_rom_psh, $i_rom_psh,  $i_type_form_core_psh,utf8_decode($vc_test_comments_psh) ]);
       $id_perfomance_test=DB::select('select @id_perfomance_test  as new_perfomance_test');

       $i_type_perfomance_test='3';
       $prospect_exp_result=DB::statement('CALL sp_create_new_perfomance_test (?,?,?,?,?,?,?,?,?,?,?, @id_perfomance_test)',[ $id_prospect_perfomance_test, $branch,  $i_type_perfomance_test, $i_number_iterations_sqt, $i_execution_type_sqt, $i_rhythm_sqt, $i_velocity_sqt,$i_average_rom_sqt, $i_rom_sqt,  $i_type_form_core_sqt,utf8_decode($vc_test_comments_sqt) ]);
       $id_perfomance_test=DB::select('select @id_perfomance_test  as new_perfomance_test');
      
       $i_type_perfomance_test='4';
       $prospect_exp_result=DB::statement('CALL sp_create_new_perfomance_test (?,?,?,?,?,?,?,?,?,?,?, @id_perfomance_test)',[ $id_prospect_perfomance_test, $branch,  $i_type_perfomance_test, $i_number_iterations_cr, $i_execution_type_cr, $i_rhythm_cr, $i_velocity_cr,$i_average_rom_cr, $i_rom_cr,  $i_type_form_core_cr,utf8_decode($vc_test_comments_cr) ]);
       $id_perfomance_test=DB::select('select @id_perfomance_test  as new_perfomance_test');
      
      


    return redirect()->route('perfomancetest.index')-> with('status','Datos de la prueba de  '.utf8_decode($vc_name).' '.utf8_decode($vc_last_name).' guardados correctamente');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
