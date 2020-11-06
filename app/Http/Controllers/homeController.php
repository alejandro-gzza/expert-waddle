<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SendGrid\Mail\Mail;
use SendGrid;

class homeController extends Controller {
  public function index() {


        
    $payment_data_membership_result = [
        "d_amount_for_pay" => "1",
        "d_amount_payed" => "2",
    ]; 


    $name='memo';
    $view=view('emails.welcome',compact('name'))->render();
 

    $email = new Mail();
   // $email->setFrom("forzagravitygym@gmail.com", "Forza Gravity Gym");
    $email->setSubject("Bienvenido".$name);
    $email->addTo("g_monrrealc@hotmail.com", "Guillermo Monrreal");
    $email->addContent("text/plain", "Lo mismo");
    $email->addContent("text/html",  $view );
    $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));
    try {
        $response = $sendgrid->send( $email);
        print $response->statusCode() . "\n";
        //print_r($response->headers());
        //print $response->body() . "\n";
    } catch (Exception $e) {
        echo 'Caught exception: '. $e->getMessage() ."\n";
    }
  }
}