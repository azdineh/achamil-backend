<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Iman\Streamer\VideoStreamer;
use Intervention\Image\Facades\Image;

use Srmklive\PayPal\Services\PayPal as PayPalClient;

use App\Mail\AchamilMail;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('admin/abcd', function () {
    Log::info('------> req received');
    return Response("abcd");
});



Route::get('achamil/p12', function () {
    return view('achamil.index');
});


Route::post('admin/uploadlesson.old', function (Request $request) {
    //Log::info('------> req received');

    if ($request->hasFile('pdf') ) {
        Log::info('------> PDF exist');
        $file = $request->file('pdf');
        $pathPdf = $file->store('pdf', 'uploads'); // The second 'uploads' refers to the disk name configured in the filesystems.php file
     
        
        
        $fileMp4 = $request->file('mp4');
        $pathMp4 = $fileMp4->store('mp4', 'uploads');
        // Perform any additional operations or validations here

        $lesson_title=$request->input('lesson_title');
        $id_unite=$request->input('id_unite');
        $id_sub_unite=$request->input('id_sub_unite');
        $lesson_order=$request->input('lesson_order');

        DB::insert('insert into lessons values (?, ?, ?, ?, ?, ?,?)', [null, $lesson_title,"",$lesson_order,$pathPdf,$pathMp4,$id_sub_unite]);

        $unite = DB::select('select * from unities where  id= ?', [1]);

        return response()->json(['message' => 'File uploaded successfully',"unite"=>$unite[0]->title]);
    }

    return response()->json(['error' => 'No file uploaded'], 400);
  
});

Route::post('admin/uploadlesson', function (Request $request) {
        
        Log::info('------> uploadlesson req received');

        // Start a transaction
            DB::beginTransaction();

            try {
                $lesson_title=$request->input('title');
                $lesson_sub_title=$request->input('sub_title');
                $lesson_order=$request->input('order_lesson');
                $khotata_file_name=$request->input('khotata_file_name');
                $video_file_name=$request->input('video_file_name');
                $id_unity=$request->input('id_unity');

                DB::insert('insert into lessons values (?, ?, ?, ?, ?, ?,?)', 
                    [null, $lesson_title,"",$lesson_order,$khotata_file_name,$video_file_name,$id_unity]);

                // Get the ID of the last inserted row
                $insertedId=-1;
                $insertedId = DB::getPdo()->lastInsertId();

                // Commit the transaction
                DB::commit();
                return response()->json(['insertedId' => $insertedId]);

                // Now, $insertedId contains the ID of the inserted row
            } catch (Exception $e) {
                // Handle any exceptions that occurred during the transaction
                DB::rollBack();

                // Handle the error gracefully
                // For example: return a response with an error message
                return response()->json(['error' => 'Failed to insert data']);
            }
  
});

Route::post('admin/updatelesson', function (Request $request) {
        
    Log::info('------> update lesson request received');

    $jsonLesson = $request->all(); // JSON data is now in PHP array

        $logMessage = print_r($jsonLesson, true);
        Log::info("-----------------> ".$logMessage);

    unset($jsonLesson['questions']);
    unset($jsonLesson['is_khotata_stored']);
    unset($jsonLesson['is_video_stored']);

    // Start a transaction
        DB::beginTransaction();

        try {
            DB::table('lessons')
                ->where('id', $jsonLesson['id'])
                ->update($jsonLesson);

            // Commit the transaction
            DB::commit();
            return response()->json([["message"=>"ok"]]);

            // Now, $insertedId contains the ID of the inserted row
        } catch (Exception $e) {
            // Handle any exceptions that occurred during the transaction
            DB::rollBack();

            // Handle the error gracefully
            // For example: return a response with an error message
            return response()->json([["message"=>"error",'error' => $e]]);
        }

});

Route::post('/admin/savequestion', function (Request $request) {
        
    Log::info('------> savequestion req received');
    // Validate the incoming request to ensure it contains JSON data
    $request->validate([
        'json_data' => 'required|json',
    ]);

    // Get the JSON data from the request
    $jsonData = $request->input('json_data');
    $main_unity = $request->input('main_unity');

    // Generate a unique filename to store the JSON data (optional)
    $fullfilename = 'exercices/'.$main_unity.'/'.'lesson_'.$request->input('id_lesson'). '.json';
    //Log::info('------> savequestion req received :: filename'.$fullfilename);

    // Save the JSON data to the storage/app directory
    Storage::disk('appdata')->put($fullfilename, $jsonData);


    $jsonData = $request->all(); // php array
    $logMessage = print_r($jsonData, true);
    Log::info("-----------------> ".$logMessage);

    $flag_lesson = DB::select('select * from flags where id_lesson= ? ', [$request->input('id_lesson')]);
    if(empty($flag_lesson)){
        DB::table('flags')
        ->insert(['id'=>null,'action'=>"update",'id_lesson'=> $request->input('id_lesson'),'laste_date_state'=> now()]);

    }
    else{
        DB::table('flags')
        ->where('id_lesson', $request->input('id_lesson'))
        ->where('action', "update")
        ->update(['laste_date_state'=> now()]);
    }
;

    // Optionally, you can also save the data in a specific subdirectory within storage/app
    // For example, to save in storage/app/json_data/
    // Storage::put('json_data/' . $filename, $jsonData);

    // You can return a response to acknowledge the successful storage if needed
    return response()->json([['message' => 'ok']]);

});

Route::post('admin/fetchlessons', function (Request $request) {
       
    $id_unity = $request->input('id_unity');
    $main_unity = $request->input('main_unity');
   
    $lessons = DB::select('select * from lessons where id_unity= ? order by order_lesson asc', [$id_unity]);

     for ($i = 0; $i < count($lessons); $i++) {
        //$url = Storage::disk('uploads')->url($unites[$i]->url_pdf);
        //$url = asset(($unites[$i]->url_pdf));
        //$unites[$i]->url_pdf=$url;
        $filename = 'exercices/'.$main_unity.'/lesson_'.$lessons[$i]->id.'.json';
        if (Storage::disk('appdata')->exists($filename)) {
            $jsonContent = Storage::disk('appdata')->get($filename);
            // Parse the JSON content into a PHP array or object
            $jsonData = json_decode($jsonContent, false); // Use true for array, false for object
            $lessons[$i]->questions=$jsonData->questions;            
        }

    } 

    return response()->json($lessons);
  
});



Route::post('admin/fetchfilesnames', function (Request $request) {
    //Log::info('------> File path');
    //Storage::disk("uploads")
    $disk = $request->input('disk');
    //$folderPath = "mokbil";
    $folderPath = $request->input('main_unity');
    $files = Storage::disk($disk)->files($folderPath);
    // If you want only the file names without the full path, you can use the `basename` function
    $fileNames = array_map('basename', $files);


    return response()->json($fileNames);
  
});

Route::post('admin/deletelessonOld', function (Request $request) {
    //Log::info('------> File path');
    
    $id_lesson = $request->input('id_lesson');

    $lesson = DB::select('select * from lessons where  id= ?', [$id_lesson]);

    $filepdf = $lesson[0]->url_pdf;
    
    if (Storage::disk("uploads")->exists($filepdf)) {
        Storage::disk("uploads")->delete($filepdf);
        //echo "File deleted successfully.";
    } else {
        //echo "File not found.";
    }

    $filemp4 = $lesson[0]->url_mp4;
    if (Storage::disk("uploads")->exists($filemp4)) {
        Storage::disk("uploads")->delete($filemp4);
        //echo "File deleted successfully.";
    } else {
        //echo "File not found.";
    }


    $rows_deleted = DB::delete('delete from lessons where id=?',[$id_lesson]);

    return response()->json([['rows_deleted'=>$rows_deleted]]);
  
});

Route::post('admin/deletelesson', function (Request $request) {
    //Log::info('------> File path');
    
    $id_lesson = $request->input('id_lesson');
    $main_unity = $request->input('main_unity');

    $rows_deleted = DB::delete('delete from lessons where id=?',[$id_lesson]);

    $filename = 'exercices/'.$main_unity.'/lesson_'.$id_lesson.'.json';
    if (Storage::disk('appdata')->exists($filename)) {
        Storage::disk('appdata')->delete($filename);
    }
    
    return response()->json([['rows_deleted'=>$rows_deleted]]);
  
});

Route::get('admin/getPDFFileOld', function (Request $request) {
    //$filePath = '/path/to/storage/' . $filename; // Adjust the path to your storage location
    //Log::info('------> File path');
    //$filename = $request->input('url_pdf');
    $filename = $request->query('pdf_url');
    //$filePath = storage_path('app/uploads/' . $filename);
    //$filePath = Storage::disk('uploads')->path($filename);

    //Log::info('------> File path :'.$filename);

    // Check if the file exists
    if (Storage::disk("uploads")->exists($filename)) {
        // Read the file contents
        $fileContents = Storage::disk("uploads")->get($filename);
        //Log::info('------> file type :'.$fileContents );
        // Set the appropriate content type for the response
        $contentType = Storage::disk("uploads")->mimeType($filename);
        

        // Return the file contents as a response
        return response($fileContents, 200)
            ->header('Content-Type', $contentType);
    }

    // If the file doesn't exist, return an error response
    return response()->json(['error' => 'File not found.'], 404);
  
});

Route::get('admin/getPDFFile', function (Request $request) {

    $filename = $request->query('pdf_url');
    //$filename="mokbil/".$filename;
    $filename = $request->input('main_unity')."/".$filename;


    // Check if the file exists
    if (Storage::disk("khotatat")->exists($filename)) {

        //$pdf = Image::make(Storage::disk("khotatat")->path($filename));

        //$width = $pdf->width();
        //$height = $pdf->height();

        // Read the file contents
        $fileContents = Storage::disk("khotatat")->get($filename);
        $contentType = Storage::disk("khotatat")->mimeType($filename);
        // Return the file contents as a response
        return response($fileContents, 200,[
            'Content-Type'        => $contentType,
            'Content-Length'      => Storage::disk("khotatat")->size($filename),
            'Accept-Ranges'       => 'bytes',
            'Content-Disposition' => 'inline; filename="' .  $filename . '"'

        ]);
           // ->header('Content-Type', $contentType);
    }

    // If the file doesn't exist, return an error response
    return response()->json(['error' => 'File not found.'], 404);
  
});

Route::get('admin/getVideo', function (Request $request) {

    $filename = $request->query('mp4_url');
    //$filename="mokbil/".$filename;
    $filename = $request->input('main_unity')."/".$filename;

    Log::info('------> file path : '. $filename);
   

    if (!Storage::disk("videos")->exists($filename)) {
        abort(404);
    }

    

    $file = Storage::disk("videos")->get($filename);
    $type = Storage::disk("videos")->mimeType($filename);

    $response = response($file, 200, [
        'Content-Type'        => $type,
        'Content-Length'      => Storage::disk("videos")->size($filename),
        'Accept-Ranges'       => 'bytes',
        'Content-Disposition' => 'inline; filename="' .  $filename . '"',
    ]);

    return $response;
});

Route::get('admin/downloadVideo', function (Request $request) {

    //$videoFileName="abcd (2).mp4";

    $filename = $request->query('mp4_url');
    $mainunity = $request->input('main_unity');
    $filePath = Storage::disk('videos')->path($mainunity."/" . $filename);

    if (!file_exists($filePath)) {
        abort(404);
    }

    return response()->download($filePath);
});


Route::get('admin/getbasedata', function (Request $request) {

    $mainUnity = 'mokbil';
    $file_name=$mainUnity."_unities.json";
    $filePath = Storage::disk('appdata')->path($file_name);    

    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->download($filePath);
});


Route::get('admin/getbasedata2', function (Request $request) {

    Log::info('Get Base data ');
    
    $mainUnity = 'mokbil';
    $file_name=$mainUnity."_unities.json";
    $filePath = Storage::disk('appdata')->path($file_name);  
    Log::info('Get Base data 2');  

    if (!file_exists($filePath)) {
        abort(404);
    }
    Log::info('Get Base data 3');
    // Step 1: Read the JSON content from the file
    $jsonContent = file_get_contents($filePath);
    //$jsonContent = Storage::disk('appdata')->get($file_name);    

    // Step 2: Convert the content to a PHP array
    $dataArray = json_decode($jsonContent, true);
    
    // Step 3: Return the PHP array as a JSON response
     return response()->json($dataArray);

});


Route::get('admin/getimage', function (Request $request) {

    $image_name =  $request->query('image_name');
    $main_unity =  $request->query('main_unity');
    $image_path= $main_unity."/".$image_name;
    // Check if the image exists in the storage disk
    if (Storage::disk('questions')->exists($image_path)) {
        $image = Storage::disk('questions')->get($image_path);
        
        // Determine the image MIME type
        $mimeType = Storage::disk('questions')->mimeType($image_path);
        
        // Create a response with the image content and appropriate headers
        return Response::make($image, 200, ['Content-Type' => $mimeType,'File-Name'=>$image_name]);
    } else {
        abort(404);
    }
});


Route::post('admin/saveinscription', function (Request $request) {
        
    Log::info('------> inscription req received');



    $jsonInsc = $request->all(); // JSON data is now in PHP array

    $logMessage = print_r($jsonInsc, true);
    Log::info("-----------------> ".$logMessage);

    // Start a transaction
        DB::beginTransaction();
        try {
            unset($jsonInsc['user']['used_unities']);
            $jsonInsc['user']['id']=null;
            DB::table('ach_users')->insert([
                $jsonInsc['user']
            ]);
            // Get the ID of the last inserted row
            $insertedIdUser=-1;
            $insertedIdUser = DB::getPdo()->lastInsertId();

            $jsonInsc['id_user']=$insertedIdUser;
            $jsonInsc['id_account']=$jsonInsc['account']['id'];


            $jsonInsc['id']=null;
            $jsonInsc['curr_nb_cnx']=1;

            DB::table('devices')->insert([
                "id"=>null,
                "phone_id"=>$jsonInsc['user']['main_user_phone_id'],
                "id_user"=>$insertedIdUser
            ]);

            unset($jsonInsc['user']);
            unset($jsonInsc['account']);
            DB::table('inscriptions')->insert([
                $jsonInsc
            ]);
            $insertedIdInscription=-1;
            $insertedIdInscription = DB::getPdo()->lastInsertId();


            // Commit the transaction
            DB::commit();
            return response()->json([["message"=>"ok",
                        'user_inserted_id' => $insertedIdUser,'inscription_inserted_id' => $insertedIdInscription]]);

            // Now, $insertedId contains the ID of the inserted row
        } catch (Exception $e) {
            // Handle any exceptions that occurred during the transaction
            DB::rollBack();

            // Handle the error gracefully
            // For example: return a response with an error message
            return response()->json([["message"=>"error",'error' => $e]]);
        }

});


Route::post('admin/updateinscription', function (Request $request) {
        
    Log::info('------> inscription req received');

    $jsonInsc = $request->all(); // JSON data is now in PHP array

    // Start a transaction
        DB::beginTransaction();

        try {
            unset($jsonInsc['user']['used_unities']);
            DB::table('ach_users')
                ->where('id', $jsonInsc['user']['id'])
                ->update($jsonInsc['user']);


            $jsonInsc['id_user']=$jsonInsc['user']['id'];
            $jsonInsc['id_account']=$jsonInsc['account']['id'];
            unset($jsonInsc['user']);
            unset($jsonInsc['account']);

            DB::table('inscriptions')
                ->where('id', $jsonInsc['id'])
                ->update($jsonInsc);

            // Commit the transaction
            DB::commit();
            return response()->json([["message"=>"ok"]]);

            // Now, $insertedId contains the ID of the inserted row
        } catch (Exception $e) {
            // Handle any exceptions that occurred during the transaction
            DB::rollBack();

            // Handle the error gracefully
            // For example: return a response with an error message
            return response()->json([["message"=>"error",'error' => $e]]);
        }

});


Route::post('admin/getinscription', function (Request $request) {
       
    $id_inscription = $request->input('id_inscription');
    Log::info('------> get inscription req received '.$id_inscription);
   
    $inscription = DB::select('select * from inscriptions where id= ?', [$id_inscription]);

    //Log::info("Insc ---->".dd($inscription));
   
    if(empty($inscription)){
        return response()->json([]);
    }else{
        $user = DB::select('select * from ach_users where id= ?', [$inscription[0]->id_user]);
        $account = DB::select('select * from accounts where id= ?', [$inscription[0]->id_account]);
        //$inscription=$inscription->toArray();
        //$inscription['user']=$user;
        //$inscription['account']=$account;
        $inscription[0]->user=$user[0];
        $inscription[0]->account=$account[0];
        unset($inscription[0]->id_user);
        unset($inscription[0]->id_account);
    
        return response()->json([$inscription[0]]);
    }


  
});


Route::post('admin/logininscription', function (Request $request) {

    Log::info('------> login req received ');
       
    $email = $request->input('email');
    $pwd = $request->input('pwd');
    $localphoneid=$request->input('local_phone_id');
    $user = DB::select('select * from ach_users where email=? and pwd=?', [$email,$pwd]);

    if (empty($user)) {
        //inscriptin array is empty
        return response()->json([]);
    } else {
        $inscription = DB::select('select * from inscriptions where id_user= ? and state <> "cancelled"', [$user[0]->id]);
        if (empty($inscription)) {
            //inscriptin array is empty
            return response()->json([]);
        }else{
            $account = DB::select('select * from accounts where id= ?', [$inscription[0]->id_account]);
            $inscription[0]->user=$user[0];
            $inscription[0]->account=$account[0];
            unset($inscription[0]->id_user);
            unset($inscription[0]->id_account);

            $curr_nb_cnx=$inscription[0]->curr_nb_cnx;
            if( $curr_nb_cnx <= $account[0]->appareils){
                
                $r=DB::select('select * from devices where phone_id=? and id_user=?',[$localphoneid,$user[0]->id]);
                if(empty($r)){
                    DB::table('devices')->insert(
                    ["id"=>null,"phone_id"=>$localphoneid,"id_user"=> $user[0]->id] );
                    DB::table('inscriptions')
                    ->where('id', $inscription[0]->id)
                    ->update(
                        ['curr_nb_cnx'=>$curr_nb_cnx+1]
                    );
                }else{
                    
                }

                
                return response()->json([$inscription[0]]);

            }else{
                // exced number of connection
                $inscription[0]->id=-2;
                return response()->json([$inscription[0]]);
            }
            
        }
    }

  
});

Route::post('admin/logoutinscription', function (Request $request) {
        
    Log::info('------> logout req received');

    $jsonUser = $request->all(); // JSON data is now in PHP array
    $user=$jsonUser['user']; 
    $local_phone_id=$jsonUser['local_phone_id'];

    $inscription = DB::select('select * from inscriptions where id_user= ? and state <> "cancelled"', [$user['id']]);

    try {
           
    $curr_nb_cnx=$inscription[0]->curr_nb_cnx;

    DB::table('devices')
    ->where('phone_id', $local_phone_id)
    ->where('id_user', $user['id'])
    ->delete();

    DB::table('inscriptions')
        ->where('id', $inscription[0]->id)
        ->update(
            ['curr_nb_cnx'=>$curr_nb_cnx-1]
        );
        return response()->json([['message'=>'ok']]);
    }catch(Exception $e){
        return response()->json([['message'=>'nook','error'=>$e]]);
    }

});


Route::post('admin/getAllinscriptions', function (Request $request) {
       
    //$id_inscription = $request->input('id_inscription');
    Log::info('------> getAllinscriptions  req received ');
    $inscriptions_view = DB::select('select * from inscriptions_view');

    for ($i = 0; $i < count($inscriptions_view); $i++){
        $user = new \stdClass();
        $account = new \stdClass();
        $inscriptions_view[$i]->id=$inscriptions_view[$i]->id_inscription;
        unset($inscriptions_view[$i]->id_inscription);
        $user->id=$inscriptions_view[$i]->id_user;
        $user->fullname=$inscriptions_view[$i]->fullname;
        $user->email=$inscriptions_view[$i]->email;
        $user->phone=$inscriptions_view[$i]->phone;
        $user->main_user_phone_id=$inscriptions_view[$i]->main_user_phone_id;
        $user->pwd=$inscriptions_view[$i]->pwd;
        unset($inscriptions_view[$i]->id_user);
        unset($inscriptions_view[$i]->fullname);
        unset($inscriptions_view[$i]->email);
        unset($inscriptions_view[$i]->phone);
        unset($inscriptions_view[$i]->main_user_phone_id);
        unset($inscriptions_view[$i]->pwd);
        $inscriptions_view[$i]->user=$user;
        
        $account->id=$inscriptions_view[$i]->id_account;
        $account->type=$inscriptions_view[$i]->type;
        $account->price=$inscriptions_view[$i]->price;
        $account->days_of_activation=$inscriptions_view[$i]->days_of_activation;
        $account->appareils=$inscriptions_view[$i]->appareils;
        unset($inscriptions_view[$i]->id_account);
        unset($inscriptions_view[$i]->type);
        unset($inscriptions_view[$i]->price);
        unset($inscriptions_view[$i]->phone);
        unset($inscriptions_view[$i]->days_of_activation);
        unset($inscriptions_view[$i]->appareils);
        $inscriptions_view[$i]->account=$account;

    }

    return response()->json($inscriptions_view);
  
});

Route::post('admin/deleteuser', function (Request $request) {
    //Log::info('------> File path');
    //delete user implies inscription deletion

    $jsonUser = $request->all(); // php array
    
    try{
        $rows_deleted = DB::delete('delete from ach_users where id=?',[$jsonUser['id']]);
        return response()->json([["message"=>"ok",'rows_deleted'=>$rows_deleted]]);
    }catch(Exception $e){
        return response()->json([["message"=>"error",'error' => $e]]);
    }
    
  
});


Route::get('admin/paymentsuccess', function (Request $request) {
    //Log::info('------> File path');
    //delete user implies inscription deletion

    $order_id = 12345; // Replace with your actual order ID
    $amount = 100.00; // Replace with the actual payment amount

    return view('paypalsuccess', compact('order_id', 'amount'));
    
 
});

Route::get('admin/paymentcancel', function (Request $request) {
    //Log::info('------> File path');
    //delete user implies inscription deletion

    $order_id = 12345; // Replace with your actual order ID
    $amount = 100.00; // Replace with the actual payment amount

    return view('paypalcancel', compact('order_id', 'amount'));
    
 
});




Route::get('admin/paypalconfirmpayment',function (Request $request) {

        //Log::info('------> Payment request :\n'.$request);
        //Log::info("Id inscription: ".$request['id_inscription']);
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $jsonData = $request->all(); // php array
        $logMessage = print_r($jsonData, true);
        Log::info("-----------------> ".$logMessage);
        
        //$inscription= json_decode("".$request['inscription'],true);
        $id_insc=$request['inscID'];
        //Log::info("Id inscription: ".$id_insc);

        $inscription = DB::select('select * from inscriptions where id=?', [$id_insc]);

        if (empty($inscription)) {
            //inscriptin array is empty
            return view('paypalcancel');
        } else{
                $account = DB::select('select * from accounts where id= ?', [$inscription[0]->id_account]);
                $user = DB::select('select * from ach_users where id= ?', [$inscription[0]->id_user]);
                $inscription[0]->user=$user[0];
                $inscription[0]->account=$account[0];
                $inscription[0]->date_activation=$request['date_act'];
                $inscription[0]->inscription_ref=$request['inscRef'];
                $inscription[0]->payment_method="منصة Paypal";
                $inscription[0]->payment_id=$request['paymentId'];
                unset($inscription[0]->id_user);
                unset($inscription[0]->id_account);    

                
                $response = $provider->capturePaymentOrder($request['token']);
                if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                    //update inscription state
                    DB::table('inscriptions')
                        ->where('id', $inscription[0]->id)
                        ->update(
                            [
                                'state'=>'activated',
                                'date_activation'=>$request['date_act']
                            ]
                        );
                    //send email
                    Mail::to($inscription[0]->user->email)->send(new AchamilMail(json_decode(json_encode($inscription[0]),true)));
                    return view('paypalsuccess');
                } else {
                    return view('paypalcancel');
                }
        }



 
});


Route::post('admin/manualconfirmpayment',function (Request $request) {

    $jsonData = $request->all(); // php array
    $logMessage = print_r($jsonData, true);
    Log::info("-----------------> ".$logMessage);
    
    //$inscription= json_decode("".$request['inscription'],true);
    $id_insc=$request['inscID'];
    $date_activ=$request['date_activ'];
    $ref_insc=$request['inscRef'];
    //Log::info("Id inscription: ".$id_insc);

    $inscription= DB::select('select * from inscriptions_view where id_inscription=?',[$id_insc]);

    if(empty($inscription)){
        return response()->json([["message"=>"error",'error' => "no inscription"]]);
    }else{

        $user = new \stdClass();
        $account = new \stdClass();
        $inscription[0]->id=$inscription[0]->id_inscription;
        $inscription[0]->date_activation=$date_activ;
        $inscription[0]->inscription_ref=$ref_insc;
        $inscription[0]->payment_method="تحويل بنكي";
        unset($inscription[0]->id_inscription);
        $user->id=$inscription[0]->id_user;
        $user->fullname=$inscription[0]->fullname;
        $user->email=$inscription[0]->email;
        $user->phone=$inscription[0]->phone;
        $user->main_user_phone_id=$inscription[0]->main_user_phone_id;
        $user->pwd=$inscription[0]->pwd;
        unset($inscription[0]->id_user);
        unset($inscription[0]->fullname);
        unset($inscription[0]->email);
        unset($inscription[0]->phone);
        unset($inscription[0]->main_user_phone_id);
        unset($inscription[0]->pwd);
        $inscription[0]->user=$user;
        
        $account->id=$inscription[0]->id_account;
        $account->type=$inscription[0]->type;
        $account->price=$inscription[0]->price;
        $account->days_of_activation=$inscription[0]->days_of_activation;
        $account->appareils=$inscription[0]->appareils;
        unset($inscription[0]->id_account);
        unset($inscription[0]->type);
        unset($inscription[0]->price);
        unset($inscription[0]->phone);
        unset($inscription[0]->days_of_activation);
        unset($inscription[0]->appareils);
        $inscription[0]->account=$account;
            //update inscription state
    DB::table('inscriptions')
    ->where('id', $inscription[0]->id)
    ->update(
       [ 'state'=>'activated',
         'date_activation'=>$inscription[0]->date_activation]
        );
    //send email
    Mail::to($inscription[0]->user->email)->send(new AchamilMail(json_decode(json_encode($inscription[0]),true)));
    return response()->json([["message"=>"ok"]]);

    }

    
});





Route::post('admin/fetchalllessons',function (Request $request) {

    //Log::info('------> Payment request :\n'.$request);
    //Log::info("Id inscription: ".$request['id_inscription']);

    $main_unity="mokbil";
    $lessons = DB::select('select * from lessons');

    for ($i = 0; $i < count($lessons); $i++) {
        //$url = Storage::disk('uploads')->url($unites[$i]->url_pdf);
        //$url = asset(($unites[$i]->url_pdf));
        //$unites[$i]->url_pdf=$url;
        $filename = 'exercices/'.$main_unity.'/lesson_'.$lessons[$i]->id.'.json';
        if (Storage::disk('appdata')->exists($filename)) {
            $jsonContent = Storage::disk('appdata')->get($filename);
            // Parse the JSON content into a PHP array or object
            $jsonData = json_decode($jsonContent, false); // Use true for array, false for object
            $lessons[$i]->questions=$jsonData->questions;            
        }

    } 

    return response()->json($lessons);

    //return response()->json($lessons);


});

Route::post('admin/fetchlesson',function (Request $request) {

    //Log::info('------> Payment request :\n'.$request);
    //Log::info("Id inscription: ".$request['id_inscription']);

    $main_unity="mokbil";
    $lessons = DB::select('select * from lessons where id=?',[$request['id_lesson']]);

    for ($i = 0; $i < count($lessons); $i++) {
        //$url = Storage::disk('uploads')->url($unites[$i]->url_pdf);
        //$url = asset(($unites[$i]->url_pdf));
        //$unites[$i]->url_pdf=$url;
        $filename = 'exercices/'.$main_unity.'/lesson_'.$lessons[$i]->id.'.json';
        if (Storage::disk('appdata')->exists($filename)) {
            $jsonContent = Storage::disk('appdata')->get($filename);
            // Parse the JSON content into a PHP array or object
            $jsonData = json_decode($jsonContent, false); // Use true for array, false for object
            $lessons[$i]->questions=$jsonData->questions;            
        }

    } 

    return response()->json($lessons);

    //return response()->json($lessons);


});



Route::post('admin/fetchunityflags',function (Request $request) {

    //Log::info('------> Payment request :\n'.$request);
    //Log::info("Id inscription: ".$request['id_inscription']);

    //$unity_id=$request['id'];
    $flags = DB::select('select * from flags order by id asc');
    
    if(empty($flags)){
        return response()->json([]);
    }else{
        return response()->json($flags);
    }

});












