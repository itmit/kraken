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

class DepartmentWebController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('departments.derartmentsList', [
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
        return view('departments.departmentCreate', [
            'title' => 'Создать отдел исполнителя'
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
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.departments.create')
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->password)
        ]);

        Department::create([
            'department_id' => $user->id,
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
