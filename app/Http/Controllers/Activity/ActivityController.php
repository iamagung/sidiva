<?php

namespace App\Http\Controllers\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use DataTables;

class ActivityController extends Controller
{
    function __construct(){
		$this->title = 'Log Activity';
	}

    public function main(Request $request) {
        if(request()->ajax()){
            $data = Activity::orderBy('id_activity','DESC')->get();
			return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('modifyPengguna', function($row){
					return User::where('id',$row->user_id)->first()->name;
				})
                ->addColumn('modifyLevel', function($row){
					$level = User::where('id',$row->user_id)->first()->level;
                    return strtoupper($level);
				})
                ->addColumn('modifyWaktu', function($row){
					$text = date('Y-m-d H:i:s', strtotime($row->created_at));
                    return strtoupper($text);
				})
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.activity.main', $data);
    }
}
