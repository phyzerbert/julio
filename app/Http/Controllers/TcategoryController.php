<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tcategory;

class TcategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        config(['site.page' => 'tcategory']);
        $data = Tcategory::paginate(15);
        return view('admin.settings.tcategory', compact('data'));
    }

    public function edit(Request $request){
        $request->validate([
            'name'=>'required',
        ]);
        $item = Tcategory::find($request->get("id"));
        $item->name = $request->get("name");
        $item->description = $request->get("description");
        $item->save();
        return back()->with('success', __('page.updated_successfully'));
    }

    public function create(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        
        Tcategory::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
        ]);
        return back()->with('success', __('page.created_successfully'));
    }

    public function delete($id){
        $item = Tcategory::find($id);
        if(!$item){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }
}
