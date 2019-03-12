<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

use Spatie\Async\Pool;

use \App\Models\Admin\Sale;

class RegisterSaleController extends Controller
{
    /**
     * Constants
     */
    const organization_key = '4ab2b297-5503-4dee-85dc-42f8d95514e2';
    const login_email    = 'andrea@persiko.it';
    const api_url = 'https://demo.xyzmo.com/Api/v4.0';
    const link_url = 'https://demo.xyzmo.com/SawViewer/SignAnyWhere.aspx';
    const public_url = 'http://127.0.0.1:8000';

    const tmp_path = 'pdf_tmp/';
    const contract_document_path = 'signed_documents/';
    const template_document_file = 'template_documents/pdfafiescavuoto.pdf';

    public function index(Request $request)
    {
        return view('registerSale.index');
    }

    /**
     * Register Sale And Envelope;Return Redirect URL
     */
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

            //Set Pending Status
            $sale->pending = 1;

            //Save Data without envelope_id to get ID
            $sale->save();

            //Prepare Sign
            $signDetail = self::prepare_sign($sale);

            $sale->envelope_id =  $signDetail['envelope_id'];

            //Save Data with envelope_id
            $sale->save();

            return response()->json(['success' => true, 'redirect' => $signDetail['redirectUrl']]);
        }
        return response()->json(['success' => false], 401);
    }

    public function register_questionnaire(Request $request)
    {

        if (Auth::check()) {
            $data = $request->all();

            //Generate PDF File
            $pdf = \PDF::loadView('registerSale.questionnaire', $data);
            //return $pdf->stream('invoice.pdf');
            $file_name = uniqid();
            $pdf->setWarnings(false)->save(self::tmp_path . $file_name . '.pdf');

            //Get Full Path of Generated PDF File
            $file_full_path = public_path(self::tmp_path . $file_name . '.pdf');

            //Send Email With Attachment
            \Mail::send('registerSale.mail', ['name'=> $data['idd_first_name'] . ' ' . $data['idd_last_name']], function($message) use ($data, $file_full_path){
                $message->to($data['idd_email'], 'AssyTech')->subject
                    ('Risultato per la tua assicurazione');
                $message->attach($file_full_path);
                $message->from('noreply@gmail.com','AssyTech');
            });

            unlink($file_full_path);

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    }

    /**
     * Reg Successful page, update pending status for sale
     */
    public function reg_successfull(Request $request)
    {
        //Update Pending Status
        $sale_id = $request->sale_id;

        if( is_null($sale_id) )
            exit;

        $sale = Sale::find($sale_id);

        //Update Sale Status
        $sale->pending = 0;

        if($sale->save())
            return view('regSuccessfull', ['sale_id' => $sale->id]);

        echo "Something went wrong...";
    }

    /**
     * Download completed Document in the background
     */
    public function download_finished_document_background(Request $request)
    {
        $sale_id = $request->sale_id;

        if( is_null($sale_id) )
            exit;

        $sale = Sale::find($sale_id);

        while(1)
        {
            //Save Finished Document
            $Envelope = self::namirialGetEnvelope($sale->envelope_id);

            if(count($Envelope->Bulks[0]->FinishedDocuments) > 0)
            {
                $generatedFile = self::namirialDownloadDocument($Envelope->Bulks[0]->FinishedDocuments[0]->FlowDocumentId, $sale->id . ".pdf");

                //Send Email With Attachment
                \Mail::send('registerSale.contractMail', ['name'=> $sale->idd_first_name . ' ' . $sale->idd_last_name], function($message) use ($sale, $generatedFile){
                    $message->to($sale->idd_email, 'AssyTech')->subject
                        ('AssyTech - il tuo contratto');
                    $message->attach($generatedFile);
                    $message->from('noreply@gmail.com','AssyTech');
                });
                return;
            }
            sleep(5);
        }
    }

    /**
     * Download Finished Document
     */
    public function download_document(Request $request)
    {
        if (Auth::check()) {
            $sale_id = $request->sale_id;
            $filename = self::contract_document_path . $sale_id . '.pdf';
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false); // required for certain browsers
            header('Content-Type: application/pdf');

            header('Content-Disposition: attachment; filename="'. basename('SignedDocument.pdf') . '";');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($filename));

            readfile($filename);

            exit;
        }
    }

    /**
     * Test Envelope Status
     */
    public function esign_test(Request $request)
    {
        $SspFileId = self::namirialUploadTemporarySspFile_v1('pdf_tmp/test.pdf');

        $preTasks = self::namirialGetPreparedTasks($SspFileId);

        $EnvelopeId = self::namirialSendEnvelope($SspFileId, $preTasks);

        $Envelop = self::namirialGetEnvelope($EnvelopeId);

        $WorkstepRedirectionUrl = $Envelop->Bulks[0]->Steps[0]->WorkstepRedirectionUrl;

        $WorkstepId = self::namirialGetWorkstepId($WorkstepRedirectionUrl);

        //$ioslink = self::namirialGetRedirectToIos($WorkstepRedirectionUrl);

        $DocumentRedirectLink = self::link_url . '?WorkstepId=' . $WorkstepId . '&setLng=it';
        echo $DocumentRedirectLink;
    }

    /**
     * Log Envelope Event
     */
    public function event_log(Request $request)
    {

        $envelope   = $request->envelope;
        $action   = $request->action;
        $internalid   = $request->internalid;

        $myfile = fopen("pdf_tmp/logs.txt", "a") or die("Unable to open file!");
        $txt = $envelope . '     ' . $action . '     ' . $internalid . '\n';
        fwrite($myfile, "\n". $txt);
        fclose($myfile);
    }

    /**
     * Prepare Sign
     */
    public static function prepare_sign($sale)
    {
        $pdfUrl = self::generateSignPDF($sale);

        $SspFileId = self::namirialUploadTemporarySspFile_v1($pdfUrl);

        $preTasks = self::namirialGetPreparedTasks($SspFileId);

        $EnvelopeId = self::namirialSendEnvelope($SspFileId, $preTasks, $sale);

        $Envelop = self::namirialGetEnvelope($EnvelopeId);

        $WorkstepRedirectionUrl = $Envelop->Bulks[0]->Steps[0]->WorkstepRedirectionUrl;

        $WorkstepId = self::namirialGetWorkstepId($WorkstepRedirectionUrl);

        $DocumentRedirectLink = self::link_url . '?WorkstepId=' . $WorkstepId . '&setLng=it';
        return [
            'redirectUrl' => $DocumentRedirectLink,
            'envelope_id' => $EnvelopeId
        ];
    }

    public static function namirialAuthroize()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::api_url . '/authorization',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER  => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json',
                'organizationKey: ' . self::organization_key,
                'userLoginName: ' . self::login_email
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $httpcode;
    }

    public static function namirialUploadTemporarySspFile_v1($file_name)
    {
        $curlFILE = curl_init ();
        curl_setopt_array ( $curlFILE, array (
            CURLOPT_URL => self::api_url . '/sspfile/uploadtemporary',
            CURLOPT_HTTPHEADER => array (
                'accept: application/json',
                'Content-Type: multipart/form-data',
                'organizationKey: ' . self::organization_key,
                'userLoginName: ' . self::login_email
            ),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => array (
                'File' => new \CURLFile ( $file_name )
            )
        ) );
        $response = json_decode(curl_exec($curlFILE));
        $err = curl_error($curlFILE);

        if($err)
        {
            return false;
        }
        curl_close($curlFILE);
        return $response->SspFileId;
    }

    public static function namirialSendEnvelope($SspFileId, $preTasks, $sale)
    {
        $envelopeModel = [
            "SspFileIds" => [
              $SspFileId
            ],
            "SendEnvelopeDescription" => [
                "Steps" => [
                    [
                        "OrderIndex" => 0,
                        "RecipientType" => "Signer",
                        "Recipients" => [
                            [
                                "Email" => $sale->idd_email,
                                "FirstName" => $sale->idd_first_name,
                                "LastName" => $sale->idd_last_name,
                                'DisableEmail' => true
                            ]
                        ],
                        "WorkstepConfiguration" => [
                            "WorkstepLabel" => 'workstep label',
                            "WorkstepTimeToLiveInMinutes" => 30,
                            "Policy" => [
                                "WorkstepTasks" => [
                                    "Tasks" => $preTasks
                                ]
                            ],
                            "FinishAction"=> [
                                "ClientActions"=> [
                                  [
                                    "RemoveDocumentFromRecentDocumentList"=> true,
                                    "CallClientActionOnlyAfterSuccessfulSync"=> true,
                                    "ClientName"=> "SIGNificant SignAnywhere",
                                    "CloseApp"=> true,
                                    "Action"=> self::public_url . "/reg_successfull?sale_id=" . $sale->id
                                  ]
                                ]
                            ],
                        ],

                    ]
                ],
                "StatusUpdateCallbackUrl" => self::public_url . "/log?envelope=##EnvelopeId##&action=##Action##&internalid=" . $sale->id,
                "Name" => "AssyTech Contract",
                "EmailSubject" => "Our contract",
                "EmailBody" => "Hey please sign the document",
                "DisplayedEmailSender" => "AssyTech",
                "EnableReminders" => false,
                "FirstReminderDayAmount"=> 0,
                "RecurrentReminderDayAmount"=> 0,
                "BeforeExpirationDayAmount"=> 0,
                "DaysUntilExpire"=> 1,
            ]
        ];
        $curl = curl_init ();
        curl_setopt_array ( $curl, array (
            CURLOPT_URL => self::api_url . '/envelope/send',
            CURLOPT_HTTPHEADER => array (
                'accept: application/json',
                'Content-Type: application/json',
                'organizationKey: ' . self::organization_key,
                'userLoginName: ' . self::login_email
            ),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => json_encode($envelopeModel)
        ) );
        $response = json_decode(curl_exec($curl));
        $err = curl_error($curl);

        if($err)
        {
            return false;
        }
        curl_close($curl);

        //echo "<pre>";
        //var_dump($response);
        //exit;

        return $response->EnvelopeId;
    }

    public static function namirialGetEnvelope($EnvelopeId)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::api_url . '/envelope/' . $EnvelopeId,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json',
                'organizationKey: ' . self::organization_key,
                'userLoginName: ' . self::login_email
            ),
        ));
        $response = json_decode(curl_exec($curl));
        $err = curl_error($curl);

        if($err)
        {
            return false;
        }
        curl_close($curl);
        return $response;
    }

    public static function namirialGetWorkstepId($WorkstepRedirectionUrl)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $WorkstepRedirectionUrl . "&responseType=returnWorkstepId",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($err)
        {
            return false;
        }
        curl_close($curl);
        return $response;
    }

    public static function namirialGetRedirectToIos($WorkstepRedirectionUrl)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $WorkstepRedirectionUrl . "&responseType=redirectToAndroidApp",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($err)
        {
            return false;
        }
        curl_close($curl);
        return $response;
    }

    public static function namirialGetPreparedTasks($SspFileId)
    {
        $prepareEnvelopeModel = [
            "SspFileIds" => [
                $SspFileId
            ],
            "AdHocWorkstepConfiguration" => [
                "WorkstepLabel" => "Work Step Label",
                "SmallTextZoomFactorPercent" => 100,
                "WorkstepTimeToLiveInMinutes" => 30
            ]
        ];
        $curl = curl_init ();
        curl_setopt_array ( $curl, array (
            CURLOPT_URL => self::api_url . '/envelope/prepare',
            CURLOPT_HTTPHEADER => array (
                'accept: application/json',
                'Content-Type: application/json',
                'organizationKey: ' . self::organization_key,
                'userLoginName: ' . self::login_email
            ),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => json_encode($prepareEnvelopeModel)
        ) );
        $response = json_decode(curl_exec($curl));
        $err = curl_error($curl);

        if($err)
        {
            return false;
        }
        curl_close($curl);

        $tasks = $response->AdHocWorkstepConfigResult->Policy->WorkstepTasks->Tasks;
        for($i = 0; $i < count($tasks); $i++)
        {
            $tasks[$i]->AllowedSignatureTypes = [
                [
                    "DiscriminatorType" => "SigTypeDraw2Sign",
                    "Preferred" => true
                ]
            ];
            $tasks[$i]->Size = [
                "Width" => 120,
                "Height" => 20
            ];
        }
        return $tasks;
    }

    public static function namirialDownloadDocument($DocumentId, $fileName)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::api_url . '/envelope/downloadCompletedDocument/' . $DocumentId,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'accept: application/octet-stream',
                'organizationKey: ' . self::organization_key,
                'userLoginName: ' . self::login_email
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($err)
        {
            return false;
        }
        curl_close($curl);

        $documentFile = fopen(self::contract_document_path . $fileName, "w") or die("Unable to open file!");
        fwrite($documentFile, $response);
        fclose($documentFile);

        return self::contract_document_path . $fileName;
    }

    public static function generateSignPDF($sale)
    {
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile(self::template_document_file);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // import a page
            $templateId = $pdf->importPage($pageNo);
            // get the size of the imported page
            $size = $pdf->getTemplateSize($templateId);

            // create a page (landscape or portrait depending on the imported page size)
            if ($size['orientation'] == 'L') {
                $pdf->AddPage('L', array($size['width'], $size['height']));
            } else {
                $pdf->AddPage('P', array($size['width'], $size['height']));
            }

            // use the imported page
            $pdf->useTemplate($templateId);



            if($pageNo == 1)
            {
                //Output Name
                $pdf->SetFont('Helvetica');
                $pdf->SetFontSize(6.5);
                $pdf->SetXY(45, 57.3);
                $pdf->Write(5, $sale->idd_first_name . ' ' . $sale->idd_last_name);
            }
            else if($pageNo == 2)
            {
                $pdf->SetFont('Helvetica');
                $pdf->SetFontSize(5);
                $current_location = self::get_client_location();

                $date_location_str = date("d/m/Y") . '  ' . $current_location['city'] . ' ' . self::countryCodeToCountry($current_location['country']);
                //Output Current Date
                $pdf->SetXY(30, 73.2);
                $pdf->Write(5, $date_location_str);

                $pdf->SetXY(30, 109);
                $pdf->Write(5, $date_location_str);
            }
        }

        // Output the new PDF
        $file_name = uniqid();

        $pdf->Output(self::tmp_path . $file_name . '.pdf','F');

        //Get Full Path of Generated PDF File
        $file_full_path = public_path(self::tmp_path . $file_name . '.pdf');

        return $file_full_path;
    }

    public static function get_client_location() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        if($ipaddress == '127.0.0.1')
            $ipaddress = '185.57.29.193';
        $json  = file_get_contents("http://ipinfo.io/$ipaddress/geo");
        $json  =  json_decode($json ,true);

        return $json;
    }
    public static function countryCodeToCountry($code) {
        $code = strtoupper($code);
        if ($code == 'AF') return 'Afghanistan';
        if ($code == 'AX') return 'Aland Islands';
        if ($code == 'AL') return 'Albania';
        if ($code == 'DZ') return 'Algeria';
        if ($code == 'AS') return 'American Samoa';
        if ($code == 'AD') return 'Andorra';
        if ($code == 'AO') return 'Angola';
        if ($code == 'AI') return 'Anguilla';
        if ($code == 'AQ') return 'Antarctica';
        if ($code == 'AG') return 'Antigua and Barbuda';
        if ($code == 'AR') return 'Argentina';
        if ($code == 'AM') return 'Armenia';
        if ($code == 'AW') return 'Aruba';
        if ($code == 'AU') return 'Australia';
        if ($code == 'AT') return 'Austria';
        if ($code == 'AZ') return 'Azerbaijan';
        if ($code == 'BS') return 'Bahamas the';
        if ($code == 'BH') return 'Bahrain';
        if ($code == 'BD') return 'Bangladesh';
        if ($code == 'BB') return 'Barbados';
        if ($code == 'BY') return 'Belarus';
        if ($code == 'BE') return 'Belgium';
        if ($code == 'BZ') return 'Belize';
        if ($code == 'BJ') return 'Benin';
        if ($code == 'BM') return 'Bermuda';
        if ($code == 'BT') return 'Bhutan';
        if ($code == 'BO') return 'Bolivia';
        if ($code == 'BA') return 'Bosnia and Herzegovina';
        if ($code == 'BW') return 'Botswana';
        if ($code == 'BV') return 'Bouvet Island (Bouvetoya)';
        if ($code == 'BR') return 'Brazil';
        if ($code == 'IO') return 'British Indian Ocean Territory (Chagos Archipelago)';
        if ($code == 'VG') return 'British Virgin Islands';
        if ($code == 'BN') return 'Brunei Darussalam';
        if ($code == 'BG') return 'Bulgaria';
        if ($code == 'BF') return 'Burkina Faso';
        if ($code == 'BI') return 'Burundi';
        if ($code == 'KH') return 'Cambodia';
        if ($code == 'CM') return 'Cameroon';
        if ($code == 'CA') return 'Canada';
        if ($code == 'CV') return 'Cape Verde';
        if ($code == 'KY') return 'Cayman Islands';
        if ($code == 'CF') return 'Central African Republic';
        if ($code == 'TD') return 'Chad';
        if ($code == 'CL') return 'Chile';
        if ($code == 'CN') return 'China';
        if ($code == 'CX') return 'Christmas Island';
        if ($code == 'CC') return 'Cocos (Keeling) Islands';
        if ($code == 'CO') return 'Colombia';
        if ($code == 'KM') return 'Comoros the';
        if ($code == 'CD') return 'Congo';
        if ($code == 'CG') return 'Congo the';
        if ($code == 'CK') return 'Cook Islands';
        if ($code == 'CR') return 'Costa Rica';
        if ($code == 'CI') return 'Cote d\'Ivoire';
        if ($code == 'HR') return 'Croatia';
        if ($code == 'CU') return 'Cuba';
        if ($code == 'CY') return 'Cyprus';
        if ($code == 'CZ') return 'Czech Republic';
        if ($code == 'DK') return 'Denmark';
        if ($code == 'DJ') return 'Djibouti';
        if ($code == 'DM') return 'Dominica';
        if ($code == 'DO') return 'Dominican Republic';
        if ($code == 'EC') return 'Ecuador';
        if ($code == 'EG') return 'Egypt';
        if ($code == 'SV') return 'El Salvador';
        if ($code == 'GQ') return 'Equatorial Guinea';
        if ($code == 'ER') return 'Eritrea';
        if ($code == 'EE') return 'Estonia';
        if ($code == 'ET') return 'Ethiopia';
        if ($code == 'FO') return 'Faroe Islands';
        if ($code == 'FK') return 'Falkland Islands (Malvinas)';
        if ($code == 'FJ') return 'Fiji the Fiji Islands';
        if ($code == 'FI') return 'Finland';
        if ($code == 'FR') return 'France, French Republic';
        if ($code == 'GF') return 'French Guiana';
        if ($code == 'PF') return 'French Polynesia';
        if ($code == 'TF') return 'French Southern Territories';
        if ($code == 'GA') return 'Gabon';
        if ($code == 'GM') return 'Gambia the';
        if ($code == 'GE') return 'Georgia';
        if ($code == 'DE') return 'Germany';
        if ($code == 'GH') return 'Ghana';
        if ($code == 'GI') return 'Gibraltar';
        if ($code == 'GR') return 'Greece';
        if ($code == 'GL') return 'Greenland';
        if ($code == 'GD') return 'Grenada';
        if ($code == 'GP') return 'Guadeloupe';
        if ($code == 'GU') return 'Guam';
        if ($code == 'GT') return 'Guatemala';
        if ($code == 'GG') return 'Guernsey';
        if ($code == 'GN') return 'Guinea';
        if ($code == 'GW') return 'Guinea-Bissau';
        if ($code == 'GY') return 'Guyana';
        if ($code == 'HT') return 'Haiti';
        if ($code == 'HM') return 'Heard Island and McDonald Islands';
        if ($code == 'VA') return 'Holy See (Vatican City State)';
        if ($code == 'HN') return 'Honduras';
        if ($code == 'HK') return 'Hong Kong';
        if ($code == 'HU') return 'Hungary';
        if ($code == 'IS') return 'Iceland';
        if ($code == 'IN') return 'India';
        if ($code == 'ID') return 'Indonesia';
        if ($code == 'IR') return 'Iran';
        if ($code == 'IQ') return 'Iraq';
        if ($code == 'IE') return 'Ireland';
        if ($code == 'IM') return 'Isle of Man';
        if ($code == 'IL') return 'Israel';
        if ($code == 'IT') return 'Italy';
        if ($code == 'JM') return 'Jamaica';
        if ($code == 'JP') return 'Japan';
        if ($code == 'JE') return 'Jersey';
        if ($code == 'JO') return 'Jordan';
        if ($code == 'KZ') return 'Kazakhstan';
        if ($code == 'KE') return 'Kenya';
        if ($code == 'KI') return 'Kiribati';
        if ($code == 'KP') return 'Korea';
        if ($code == 'KR') return 'Korea';
        if ($code == 'KW') return 'Kuwait';
        if ($code == 'KG') return 'Kyrgyz Republic';
        if ($code == 'LA') return 'Lao';
        if ($code == 'LV') return 'Latvia';
        if ($code == 'LB') return 'Lebanon';
        if ($code == 'LS') return 'Lesotho';
        if ($code == 'LR') return 'Liberia';
        if ($code == 'LY') return 'Libyan Arab Jamahiriya';
        if ($code == 'LI') return 'Liechtenstein';
        if ($code == 'LT') return 'Lithuania';
        if ($code == 'LU') return 'Luxembourg';
        if ($code == 'MO') return 'Macao';
        if ($code == 'MK') return 'Macedonia';
        if ($code == 'MG') return 'Madagascar';
        if ($code == 'MW') return 'Malawi';
        if ($code == 'MY') return 'Malaysia';
        if ($code == 'MV') return 'Maldives';
        if ($code == 'ML') return 'Mali';
        if ($code == 'MT') return 'Malta';
        if ($code == 'MH') return 'Marshall Islands';
        if ($code == 'MQ') return 'Martinique';
        if ($code == 'MR') return 'Mauritania';
        if ($code == 'MU') return 'Mauritius';
        if ($code == 'YT') return 'Mayotte';
        if ($code == 'MX') return 'Mexico';
        if ($code == 'FM') return 'Micronesia';
        if ($code == 'MD') return 'Moldova';
        if ($code == 'MC') return 'Monaco';
        if ($code == 'MN') return 'Mongolia';
        if ($code == 'ME') return 'Montenegro';
        if ($code == 'MS') return 'Montserrat';
        if ($code == 'MA') return 'Morocco';
        if ($code == 'MZ') return 'Mozambique';
        if ($code == 'MM') return 'Myanmar';
        if ($code == 'NA') return 'Namibia';
        if ($code == 'NR') return 'Nauru';
        if ($code == 'NP') return 'Nepal';
        if ($code == 'AN') return 'Netherlands Antilles';
        if ($code == 'NL') return 'Netherlands the';
        if ($code == 'NC') return 'New Caledonia';
        if ($code == 'NZ') return 'New Zealand';
        if ($code == 'NI') return 'Nicaragua';
        if ($code == 'NE') return 'Niger';
        if ($code == 'NG') return 'Nigeria';
        if ($code == 'NU') return 'Niue';
        if ($code == 'NF') return 'Norfolk Island';
        if ($code == 'MP') return 'Northern Mariana Islands';
        if ($code == 'NO') return 'Norway';
        if ($code == 'OM') return 'Oman';
        if ($code == 'PK') return 'Pakistan';
        if ($code == 'PW') return 'Palau';
        if ($code == 'PS') return 'Palestinian Territory';
        if ($code == 'PA') return 'Panama';
        if ($code == 'PG') return 'Papua New Guinea';
        if ($code == 'PY') return 'Paraguay';
        if ($code == 'PE') return 'Peru';
        if ($code == 'PH') return 'Philippines';
        if ($code == 'PN') return 'Pitcairn Islands';
        if ($code == 'PL') return 'Poland';
        if ($code == 'PT') return 'Portugal, Portuguese Republic';
        if ($code == 'PR') return 'Puerto Rico';
        if ($code == 'QA') return 'Qatar';
        if ($code == 'RE') return 'Reunion';
        if ($code == 'RO') return 'Romania';
        if ($code == 'RU') return 'Russian Federation';
        if ($code == 'RW') return 'Rwanda';
        if ($code == 'BL') return 'Saint Barthelemy';
        if ($code == 'SH') return 'Saint Helena';
        if ($code == 'KN') return 'Saint Kitts and Nevis';
        if ($code == 'LC') return 'Saint Lucia';
        if ($code == 'MF') return 'Saint Martin';
        if ($code == 'PM') return 'Saint Pierre and Miquelon';
        if ($code == 'VC') return 'Saint Vincent and the Grenadines';
        if ($code == 'WS') return 'Samoa';
        if ($code == 'SM') return 'San Marino';
        if ($code == 'ST') return 'Sao Tome and Principe';
        if ($code == 'SA') return 'Saudi Arabia';
        if ($code == 'SN') return 'Senegal';
        if ($code == 'RS') return 'Serbia';
        if ($code == 'SC') return 'Seychelles';
        if ($code == 'SL') return 'Sierra Leone';
        if ($code == 'SG') return 'Singapore';
        if ($code == 'SK') return 'Slovakia (Slovak Republic)';
        if ($code == 'SI') return 'Slovenia';
        if ($code == 'SB') return 'Solomon Islands';
        if ($code == 'SO') return 'Somalia, Somali Republic';
        if ($code == 'ZA') return 'South Africa';
        if ($code == 'GS') return 'South Georgia and the South Sandwich Islands';
        if ($code == 'ES') return 'Spain';
        if ($code == 'LK') return 'Sri Lanka';
        if ($code == 'SD') return 'Sudan';
        if ($code == 'SR') return 'Suriname';
        if ($code == 'SJ') return 'Svalbard & Jan Mayen Islands';
        if ($code == 'SZ') return 'Swaziland';
        if ($code == 'SE') return 'Sweden';
        if ($code == 'CH') return 'Switzerland, Swiss Confederation';
        if ($code == 'SY') return 'Syrian Arab Republic';
        if ($code == 'TW') return 'Taiwan';
        if ($code == 'TJ') return 'Tajikistan';
        if ($code == 'TZ') return 'Tanzania';
        if ($code == 'TH') return 'Thailand';
        if ($code == 'TL') return 'Timor-Leste';
        if ($code == 'TG') return 'Togo';
        if ($code == 'TK') return 'Tokelau';
        if ($code == 'TO') return 'Tonga';
        if ($code == 'TT') return 'Trinidad and Tobago';
        if ($code == 'TN') return 'Tunisia';
        if ($code == 'TR') return 'Turkey';
        if ($code == 'TM') return 'Turkmenistan';
        if ($code == 'TC') return 'Turks and Caicos Islands';
        if ($code == 'TV') return 'Tuvalu';
        if ($code == 'UG') return 'Uganda';
        if ($code == 'UA') return 'Ukraine';
        if ($code == 'AE') return 'United Arab Emirates';
        if ($code == 'GB') return 'United Kingdom';
        if ($code == 'US') return 'United States of America';
        if ($code == 'UM') return 'United States Minor Outlying Islands';
        if ($code == 'VI') return 'United States Virgin Islands';
        if ($code == 'UY') return 'Uruguay, Eastern Republic of';
        if ($code == 'UZ') return 'Uzbekistan';
        if ($code == 'VU') return 'Vanuatu';
        if ($code == 'VE') return 'Venezuela';
        if ($code == 'VN') return 'Vietnam';
        if ($code == 'WF') return 'Wallis and Futuna';
        if ($code == 'EH') return 'Western Sahara';
        if ($code == 'YE') return 'Yemen';
        if ($code == 'XK') return 'Kosovo';
        if ($code == 'ZM') return 'Zambia';
        if ($code == 'ZW') return 'Zimbabwe';
        return '';
    }
}
