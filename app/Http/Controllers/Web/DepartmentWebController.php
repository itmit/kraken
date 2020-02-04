<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DerartmentWebController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('departments.departmentsList', [
            'title' => 'Список Отделов Исполнителей',
            'departments' => Department::all()->sortByDesc('rating')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cases.caseCreate', [
            'title' => 'Добавить кейс'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->case_head = trim($request->case_head);
        $request->case_body = trim($request->case_body);

        $validator = Validator::make($request->all(), [
            'case_head' => 'required|min:3|max:191|string',
            'case_body' => 'required|min:3|max:100000',
            'case_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.cases.create')
                ->withErrors($validator)
                ->withInput();
        }


        $path = $request->file('case_picture')->store('public/newsPictures');
        $url = Storage::url($path);

        Cases::create([
            'uuid' => Str::uuid(),
            'head' => $request->input('case_head'),
            'body' => $request->input('case_body'),
            'picture' => $url,
        ]);

        return redirect()->route('auth.cases.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Cases::where('id', '=', $request->id)->delete();
        return response()->json(['succses'=>'Удалено'], 200); 
    }
}
