<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Dept;
use App\Member;

//
class DeptsController extends Controller
{
    /**************************************
     *
     **************************************/
    public function index()
    {
        $depts = Dept::orderBy('updated_at', 'desc')->paginate(5);
        return view('depts/index')->with('depts', $depts );
    }

    /**************************************
     *
     **************************************/
    public function create()
    {
        return view('depts/create')->with('dept', new Dept());
    }
    /**************************************
     *
     **************************************/    
    public function store(Request $request)
    {
        $inputs = $request->all();
        DB::beginTransaction();
        try {
            $dept = new Dept();
            $dept->fill($inputs);
            $result = $dept->save();
//debug_dump($dept->id );
            if($result && isset($inputs["member"]) ){
                $this->save_members($inputs["member"], $dept->id);
            }
//exit();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }        
        return redirect()->route('depts.index');
    }    
    /**************************************
     *
     **************************************/ 
    private function save_members($members, $dept_id){
        foreach ($members as $member){
            if(empty($member) == false){
                $data= [
                    "dept_id" => $dept_id,
                    "name" => $member,
                    "mail" => "",
                ];
                $member = new Member();
                $member->fill($data );
                $member->save();                
//debug_dump($member );
            }
        }
    }

}
