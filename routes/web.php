<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    $id_unite = $request->input('id_unite');
   
    $unites = DB::select('select * from lessons where  id_unite= ?', [$id_unite]);

    return response()->json($unites);
  
});
