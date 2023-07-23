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
    //Log::info('------> req received');

        $lesson_title=$request->input('lesson_title');
        $lesson_sub_title=$request->input('lesson_sub_title');
        $lesson_order=$request->input('lesson_order');
        $khotata_file_name=$request->input('khotata_file_name');
        $video_file_name=$request->input('video_file_name');
        $exerice_link=$request->input('exercice_link');
        $id_unite=$request->input('id_unite');
        $id_sub_unite=$request->input('id_sub_unite');


        DB::insert('insert into lessons values (?, ?, ?, ?, ?, ?,?,?)', 
            [null, $lesson_title,"",$lesson_order,$khotata_file_name,$video_file_name,$exerice_link,$id_sub_unite]);

        $unite = DB::select('select * from unities where  id= ?', [1]);

        return response()->json(['message' => 'File uploaded successfully',"unite"=>$unite[0]->title]);
    

    return response()->json(['error' => 'No file uploaded'], 400);
  
});

Route::post('admin/fetchlessons', function (Request $request) {
       
    $id_unite = $request->input('id_unite');
   
    $unites = DB::select('select * from lessons where  id_unite= ? order by order_lesson asc', [$id_unite]);

/*     for ($i = 0; $i < count($unites); $i++) {
        //$url = Storage::disk('uploads')->url($unites[$i]->url_pdf);
        $url = asset(($unites[$i]->url_pdf));
        $unites[$i]->url_pdf=$url;
    } */

    return response()->json($unites);
  
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

    $rows_deleted = DB::delete('delete from lessons where id=?',[$id_lesson]);

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
        return response($fileContents, 200)
            ->header('Content-Type', $contentType);
    }

    // If the file doesn't exist, return an error response
    return response()->json(['error' => 'File not found.'], 404);
  
});

Route::get('admin/getVideo', function (Request $request) {

    $filename = $request->query('mp4_url');
    //$filename="mokbil/".$filename;
    $filename = $request->input('main_unity')."/".$filename;
   

    if (!Storage::disk("videos")->exists($filename)) {
        abort(404);
    }

    Log::info('------> file path : '. $filename);

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