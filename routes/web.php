<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Iman\Streamer\VideoStreamer;

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
    Log::info('------> savequestion req received :: filename'.$fullfilename);

    // Save the JSON data to the storage/app directory
    Storage::disk('appdata')->put($fullfilename, $jsonData);

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
        // Read the file contents
        $fileContents = Storage::disk("khotatat")->get($filename);
        $contentType = Storage::disk("khotatat")->mimeType($filename);
        // Return the file contents as a response
        return response($fileContents, 200,[
            'Content-Type'        => $contentType,
            'Content-Length'      => Storage::disk("khotatat")->size($filename),
            'Accept-Ranges'       => 'bytes',
            'Content-Disposition' => 'inline; filename="' .  $filename . '"',
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








