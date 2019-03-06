<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

use \App\Models\Admin\Sale;

class RegisterSaleController extends Controller
{
    public function index(Request $request)
    {
        return view('registerSale.index');
    }

    public function register_sale(Request $request)
    {
        if (Auth::check()) {

            $sale_data = $request->all();
            $sale = new Sale();
            $record_data = array_merge($sale_data['personalInfo'], $sale_data['questionnaire_data']);
            $sale->fill($record_data);
            $sale->age = $sale_data['age'];
            $sale->seller_id = Auth::user()->id;
            $sale->packageType = $sale_data['packageType'];
            $sale->price = $sale_data['price'];

            //var_dump($record_data);
            //return;

            if($sale->save())
                return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    }

    public function register_questionnaire(Request $request)
    {

        if (Auth::check()) {
/*
            $data = [
                "idd_type"  => "agency",
                "idd_email"  => "boris@email.com",
                "idd_first_name"  => "Boris",
                "idd_last_name"  => "Aminov",
                "idd_birthday"  => "2019-02-04",
                "idd_tax_code"  => "54345433",

                "idd_private_life" => false,
                "idd_professional_life" => true,

                "idd_insurance_needs_professional_activity" => true,
                "idd_insurance_needs_employee_manager_life" => true,
                "idd_insurance_needs_employee_manager_retirement" => true,
                "idd_insurance_needs_company_assets" => true,
                "idd_insurance_needs_business_credit" => true,
                "idd_insurance_needs_vehicle" => true,
                "idd_insurance_needs_injuries_illness" => true,
                "idd_insurance_needs_home_family" => false,
                "idd_insurance_needs_family_members" => false,
                "idd_insurance_needs_pension" => false,
                "idd_insurance_needs_annuity" => false,
                "idd_insurance_needs_heirs_income" => false,
                "idd_insurance_needs_investing_saving" => false,

                "idd_talking_business_profession" => "I run Wemteq with 20 developers.And there are so many skill sets.",

                "idd_business_protect_danni_to_me" => false,
                "idd_business_protect_damage_others" => true,
                "idd_business_protect_legal_disputes" => true,
                "idd_business_protect_activity_interruption" => false,

                "idd_accident_protect_disease" => false,
                "idd_accident_protect_hospitalization" => true,
                "idd_accident_protect_accident" => false,
                "idd_accident_protect_traveling" => true,

                "idd_home_family_protect_family" => false,
                "idd_home_family_protect_house" =>true,
                "idd_home_family_protect_legal_dispute" => true,

                "idd_paid_up_condition_after_expiration" => true,

                "idd_risk_tolerance_media_sr13_sr14" => true,

                "idd_insurance_knowledge_level_base" => false,

                "idd_subscription_type" => "half-yearly",

                "idd_business_last_year_turnover"  => "$600"
            ];
            return view('registerSale.questionnaire', $data);
*/

            $data = $request->all();

            //Generate PDF File
            $pdf = \PDF::loadView('registerSale.questionnaire', $data);
            //return $pdf->stream('invoice.pdf');
            $file_name = uniqid();
            $pdf->setWarnings(false)->save('pdf_tmp/' . $file_name . '.pdf');

            //Get Full Path of Generated PDF File
            $file_full_path = public_path('pdf_tmp/' . $file_name . '.pdf');

            //Send Email With Attachment
            \Mail::send('registerSale.mail', ['name'=> $data['idd_first_name'] . ' ' . $data['idd_last_name']], function($message) use ($data, $file_full_path){
                $message->to($data['idd_email'], 'Assiqurapp')->subject
                    ('Risultato per la tua assicurazione');
                $message->attach($file_full_path);
                $message->from('noreply@gmail.com','Assiqurapp');
            });

            unlink($file_full_path);

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    }
}
