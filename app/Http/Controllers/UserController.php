<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Exports\UserExport;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Jurusan;

use Validator;

use Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::all();
        $jurusan_table = Jurusan::all();
        return view('admin.index', compact('user', 'jurusan_table'));
    }

    public function siswa()
    {
        $user = User::all();
        $jurusan_table = Jurusan::all();
        return view('user.detail', compact('user', 'jurusan_table'));
    }

    public function profile()
    {
        $user = User::all();
        $jurusan_table = Jurusan::all();
        return view('user.profile', compact('user', 'jurusan_table'));
    }

    public function userexport()
    {
        return Excel::download(new UserExport, 'user.xlsx');
    }

    public function userimport(Request $request)
    {
        $file = request()->file('file');
        $nama_file = $file->getClientOriginalName();
        $file->move('DataExcel', $nama_file);

        Excel::import(new UserImport, public_path('/DataExcel/'.$nama_file));
        return redirect('/list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = User::all();
        $jurusan_table = Jurusan::all();

        return view ('admin.create', compact('user', 'jurusan_table'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        if($request->input('password'))
        {
            $input['password'] = bcrypt($input['password']);
        }
         $validator = Validator::make($input, [
            'photo' => 'requred|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if($request->hasFile('photo'))
        {
            $destination_path = 'storage/images/user';
            $image = $request->file('photo');
            $image_name = time()."_".$image->getClientOriginalName();
            $path = $request->file('photo')->storeAs($destination_path, $image_name);
            $input['photo'] = $image_name;
        }

        User::create($input);


        return redirect('/list');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $user = User::findOrFail($id);
        $jurusan = Jurusan::all();
        return view('admin.detail', compact('user', 'jurusan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $jurusan_table = Jurusan::all();
        return view('admin.edit', compact('user', 'jurusan_table'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->all();

        if($request->input('password'))
        {
            $data['password'] = bcrypt($data['password']);
        }
        else
        {
            $data = Arr::except($data, ['password']);
        }
        
        if($request->hasFile('photo'))
        {
            $destination_path = 'storage/images/user';
            $image = $request->file('photo');
            $image_name = time()."_".$image->getClientOriginalName();
            $path = $request->file('photo')->storeAs($destination_path, $image_name);
            $data['photo'] = $image_name;
        }
        
        $user->update($data);
        return redirect('/user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $data = User::findOrFail($id);
        $data->delete();
        return redirect('/list');
    }
}