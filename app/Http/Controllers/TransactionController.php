<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Tcategory;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        config(['site.page' => 'transaction']);
        $tcategories = Tcategory::all();
        $mod = new Transaction();
        $total = array();
        $tcategory = $keyword = $period = $type = '';
        if($request->keyword != ''){
            $keyword = $request->keyword;
            $tcategory_array = Tcategory::where('name', 'like', "%$keyword%")->pluck('id');
            $mod = $mod->where(function($query) use($keyword, $tcategory_array){
                return $query->where('reference_no', 'like', "%$keyword%")
                            ->orWhere('note', 'like', "%$keyword%")
                            ->orWhere('supplier_customer', 'like', "%$keyword%")
                            ->orWhereIn('tcategory_id', $tcategory_array)
                            ->orWhere('timestamp', 'like', "%$keyword%");
            });
        }
        if($request->tcategory != ''){
            $tcategory = $request->tcategory;
            $mod = $mod->where('tcategory_id', $tcategory);
        }      
        if($request->type != ''){
            $type = $request->type;
            $mod = $mod->where('type', $type);
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10)." 00:00:00";
            $to = substr($period, 14, 10)." 23:59:59";
            if($from == $to){
                $mod = $mod->whereDate('timestamp', $to);
            }else{                
                $mod = $mod->whereBetween('timestamp', [$from, $to]);
            } 
        }
        $pagesize = 15;
        if($request->get('pagesize') != ''){
            $pagesize = $request->get('pagesize');
        }
        $data = $mod->orderBy('timestamp', 'desc')->paginate($pagesize);
        $collection = $mod->get();
        $total['expense'] = $collection->where('type', 1)->sum('amount');
        $total['incoming'] = $collection->where('type', 2)->sum('amount');
        return view('transaction.index', compact('data', 'tcategories', 'total', 'type', 'tcategory', 'keyword', 'period', 'pagesize'));
    }

    public function daily(Request $request) {
        config(['site.page' => 'daily_transaction']);
        $tcategories = Tcategory::all();
        $last_transaction = Transaction::orderBy('timestamp', 'desc')->first();
        if(isset($last_transaction)){
            $period = date('Y-m-d', strtotime($last_transaction->timestamp));
        }else{
            $period = date('Y-m-d');
        }

        $mod = new Transaction();
        $total = array();
        $tcategory = $keyword = $type = '';
        if($request->keyword != ''){
            $keyword = $request->keyword;
            $tcategory_array = Tcategory::where('name', 'like', "%$keyword%")->pluck('id');
            $mod = $mod->where(function($query) use($keyword, $tcategory_array){
                return $query->where('reference_no', 'like', "%$keyword%")
                            ->orWhere('note', 'like', "%$keyword%")
                            ->orWhere('supplier_customer', 'like', "%$keyword%")
                            ->orWhereIn('tcategory_id', $tcategory_array)
                            ->orWhere('timestamp', 'like', "%$keyword%");
            });            
        }
        if($request->tcategory != ''){
            $tcategory = $request->tcategory;
            $mod = $mod->where('tcategory_id', $tcategory);
        }        
        if($request->type != ''){
            $type = $request->type;
            $mod = $mod->where('type', $type);
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
        }
        if($request->get('change_date') != ""){
            $change_date = $request->get('change_date');
            if($change_date == "1"){
                $period = date('Y-m-d', strtotime($period .' -1 day'));
            }else if($change_date == "2"){
                $period = date('Y-m-d', strtotime($period .' +1 day'));
            }
        }
        $mod = $mod->whereDate('timestamp', $period);
        $pagesize = 15;
        if($request->get('pagesize') != ''){
            $pagesize = $request->get('pagesize');
        }
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        $collection = $mod->get();
        $total['expense'] = $collection->where('type', 1)->sum('amount');
        $total['incoming'] = $collection->where('type', 2)->sum('amount');
        return view('transaction.daily', compact('data', 'tcategories', 'total', 'type', 'tcategory', 'keyword', 'period', 'pagesize'));
    }

    public function create(Request $request) {
        config(['site.page' => 'transaction']);
        $tcategories = Tcategory::all();
        return view('transaction.create', compact('tcategories'));
    }

    public function save(Request $request){
        $request->validate([
            'reference_no' => 'required',
            'date' => 'required',
            'amount' => 'required',
        ]);
        $item = new Transaction();
        $item->reference_no = $request->get("reference_no");
        $item->timestamp = $request->get("date") . ":00";
        $item->type = $request->get("type");
        $item->supplier_customer = $request->supplier_customer;
        $item->amount = $request->get("amount");
        $item->tcategory_id = $request->get("tcategory");
        $item->note = $request->get("note");
        if($request->has("attachment")){
            $picture = request()->file('attachment');
            $imageName = "transaction_".time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/transaction_images/'), $imageName);
            $item->attachment = 'images/uploaded/transaction_images/'.$imageName;
        }
        $item->save();
        return back()->with('success', __('page.created_successfully'));
    }

    public function edit (Request $request, $id){
        config(['site.page' => 'transaction']);

    }

    public function update(Request $request){
        $request->validate([
            'date'=>'required',
            'amount'=>'required',
        ]);
        // dd($request->all());
        $item = Transaction::find($request->get("id"));
        $item->reference_no = $request->get("reference_no");
        $item->timestamp = $request->get("date") . ":00";
        $item->amount = $request->get("amount");
        $item->supplier_customer = $request->supplier_customer;
        $item->tcategory_id = $request->get("tcategory");
        $item->note = $request->get("note");
        if($request->has("attachment")){
            $picture = request()->file('attachment');
            $imageName = "transaction_".time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/transaction_images/'), $imageName);
            $item->attachment = 'images/uploaded/transaction_images/'.$imageName;
        }
        $item->save();
        return back()->with('success', __('page.updated_successfully'));
    }


    public function delete($id){
        $item = Transaction::find($id);
        $item->delete();       
        return back()->with('success', __('page.deleted_successfully'));
    }
}
