<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Client;
use App\Models\MasterInfo;
use App\Models\TypeOfWork;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DepartmentWebController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('masters.masterList', [
            'title' => 'Список мастеров',
            'masters' => Client::where('type', 'master')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('masters.masterCreate', [
            'title' => 'Создать мастера',
            'departments' => Department::all()->sortByDesc('rating'),
            'works' => TypeOfWork::all(),
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:191|string',
            'phone' => 'required|min:17|max:18',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.departments.create')
                ->withErrors($validator)
                ->withInput();
        }

        Department::create([
            'uuid' => Str::uuid(),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'rating' => 0,
        ]);

        return redirect()->route('auth.departments.index');
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
