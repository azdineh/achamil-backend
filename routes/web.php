<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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


Route::get('/abc', function () {
    return 'abc';
});


Route::post('admin/getinfo', function (Request $request) {
    // Access the parameters sent in the request
    //$param1 = $request->input('user');
    //$param2 = $request->input('param2');

    // Process the parameters and return a response
   return response()->json(array([
        //'user' => $param1
        'user'=>"zerrrr"
    ]));
  
});

Route::post('admin/uploadpdf', function (Request $request) {
    if ($request->hasFile('pdf')) {
        $file = $request->file('pdf');
        $pathPdf = $file->store('pdf', 'uploads'); // The second 'uploads' refers to the disk name configured in the filesystems.php file
     
        
        
        $fileMp4 = $request->file('mp4');
        $pathMp4 = $fileMp4->store('mp4', 'uploads');
        // Perform any additional operations or validations here

        $lesson_title=$request->input('lesson_title');
        $id_unite=$request->input('id_unite');
        $id_sub_unite=$request->input('id_sub_unite');

        DB::insert('insert into lessons values (?, ?, ?, ?, ?, ?)', [null, $lesson_title,"",$pathPdf,$pathMp4,$id_sub_unite]);

        $unite = DB::select('select * from unities where  id= ?', [1]);

        return response()->json(['message' => 'File uploaded successfully',"unite"=>$unite[0]->title]);
    }

    return response()->json(['error' => 'No file uploaded'], 400);
  
});

Route::post('admin/fetchlessons', function (Request $request) {
    //Log::info('------> File path');
    
    $id_unite = $request->input('id_unite');
   
    $unites = DB::select('select * from lessons where  id_unite= ?', [$id_unite]);

/*     for ($i = 0; $i < count($unites); $i++) {
        //$url = Storage::disk('uploads')->url($unites[$i]->url_pdf);
        $url = asset(($unites[$i]->url_pdf));
        $unites[$i]->url_pdf=$url;
    } */

    return response()->json($unites);
  
});

Route::get('admin/getPDFFile', function (Request $request) {
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

Route::get('admin/getVideo', function (Request $request) {

    $filename = $request->query('mp4_url');
    //$filename = "4j0cLHvI7lhQYi31WL7RPuoCFOTJEu7YIUk7hHAA.mp4";

    // Log::info('------> public path : '.asset('4j0cLHvI7lhQYi31WL7RPuoCFOTJEu7YIUk7hHAA.mp4'));

    //$path = storage_path('app/uploads/' . $filename);
    

    if (!Storage::disk("uploads")->exists($filename)) {
        abort(404);
    }

    Log::info('------> file path : '. $filename);

    $file = Storage::disk("uploads")->get($filename);
    $type = Storage::disk("uploads")->mimeType($filename);

    $response = response($file, 200, [
        'Content-Type'        => $type,
        'Content-Length'      => Storage::disk("uploads")->size($filename),
        'Accept-Ranges'       => 'bytes',
        'Content-Disposition' => 'inline; filename="' .  "12008.mp4" . '"',
    ]);

    return $response;
});


Route::get('/flutter/{any}', function () {
    return File::get(public_path('achamil/index.html'));
})->where('any', '.*');



