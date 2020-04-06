<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Donatur;
use App\User;
use DataTables;

class DonaturController extends Controller
{
    public function __construct(){
        $this->Donatur = new Donatur;
        $this->title = 'Donatur';
        $this->pasth = 'donatur';
    }

    public function index()
    {
        // $data = $this->Donatur->getData();
        // dd(DataTables::of($data)->make());
        return view('usr_pengurus.donaturs.index');
    }

    public function getDonatur(Request $request){
        $data = $this->Donatur->getData();
        return \DataTables::of($data)->addIndexColumn()
            ->addColumn('Actions', function($data){
                return '
                <form action="'. route('donatur.destroy', $data->id_donatur).'" method="post" class="sa-remove">
                    <input type="hidden" name="_method" value="DELETE">
                    <a href="' . route('donatur.show', $data->id_donatur) .'" class="btn btn-light btn-sm"><i class="fa fa-eye"></i><span>&nbsp;Show</span></a>
                    <a href="'.route('donatur.edit', $data->id_donatur).'" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i><span>&nbsp;Edit</span></a>
                    <button  class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Delete</button>
                </form>
                    ';
            })
            ->rawColumns(['Actions'])
            ->make(true);
    }

    public function create()
    {
        return view('usr_pengurus.donaturs.create');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
            'nama_depan' => 'required',
            'nama_belakang' => 'required',
            'no_hp' => 'required|min:11',
            'alamat' => 'required',
            'umur' => 'required'
        ]);

        $user = User::create([
            'name' => $request->nama_depan,
            'email' => $request->email,
            'role' => 'donatur',
            'password' => Hash::make($request->password),
        ]);

        $donaturs = Donatur::create([
            'id_user' => $user->id,
            'nama_depan' => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'umur' => $request->umur
        ]);

        return redirect()->route('donatur.index');
    }


    public function edit($donatur){
        $donaturs = Donatur::find($donatur);
        return view('usr_pengurus.donaturs.edit', compact('donaturs'));
    }

    public function show($donatur){
        $donaturs = Donatur::find($donatur);
        return view('usr_pengurus.donaturs.detail', compact('donaturs'));
    }

    public function update(Request $request, $donatur){
        $this->validate($request, [
            'nama_depan' => 'required',
            'nama_belakang' => 'required',
            'no_hp' => 'required',
            'alamat' => 'required',
            'umur' => 'required'
        ]);

        $donaturs = Donatur::findOrfail($donatur);
        $donaturs->update([
            'nama_depan' => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'umur' => $request->umur
        ]);

        return redirect()->route('donatur.index');
    }

       public function destroy($donatur){
        $donaturs = Donatur::findOrFail($donatur);
        $id_user = $donaturs->id_user;
        $donaturs->delete();

        $user = User::findOrFail($id_user);
        $user->delete();

        return redirect()->route('donatur.index');
    }

    
}

