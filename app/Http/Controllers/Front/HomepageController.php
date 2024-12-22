<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\post;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    //
    public function index(){
        $lastData = $this->lastData();
        $data = post ::where('status','publish')->where('id','!=',$lastData->id)->orderBy('id','desc')->paginate(3);
        return view('components.front.home-page',compact('data','lastData'));
    }

    private function lastData(){
        $data = post ::where('status','publish')->orderBy('id','desc')->latest()->first();
        return $data;
    }
}
