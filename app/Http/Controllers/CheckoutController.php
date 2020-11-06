<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;


class CheckoutController extends Controller
{
    public function home() {
        // Propiedades de Navegacion
       
        $branch='1';
        $type_menu='0';
        $user_system =  \Session::get('vc_user_system');
        
        // Obtener la fecha actual
        $today = Carbon::now();
        $dt_current_day= Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
        $dt_current_day->format('Y-M-D');
       
        
        $pending_checkout = DB::select('SELECT 
        COUNT(*) AS i_total
        FROM
            tb_clients
        WHERE
            DATE(dt_last_attendance) = DATE(?) 
            AND (DATE(dt_last_check_out) != DATE(dt_last_attendance)   OR  dt_last_check_out="0000-00-00 00:00:00")
            AND  DATE_ADD((dt_last_attendance),
            INTERVAL 3 HOUR) >= ?  
           ', [$dt_current_day, $dt_current_day]);
  

        //$i_total = $pending_checkout[0]->i_total;


        // return  $type_menu;
         return   $pending_checkout;

        }


}
