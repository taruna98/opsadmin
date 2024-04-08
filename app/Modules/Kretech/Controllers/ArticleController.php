<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Response;
use Yajra\DataTables\DataTables;

class ArticleController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'Kretech Article';
        $id = Auth::user()->id;

        // get user from table users
        $user = User::where('id', $id)->first();

        // verify user from table profile
        $profile = DB::connection('mysql2')->table('profiles')->where('eml', $user->email)->first();
        if ($profile == null) {
          Alert::error('Failed', 'Profile Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
        }

        // declare variable
        $api_url = env('API_URL');
        $code = $profile->cod;

        // get data article from api
        $get_article = Http::get($api_url . 'profile/' . $code)->json()['article'];

        // check response
        if ($get_article == '[]') {
            Alert::error('Failed', 'Article Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // show data if ajax request
        if ($request->ajax()) {
            return response()->json($get_article);
        }

        // get last id article
        $last_id = null;
        foreach ($get_article as $article) {
            if ($last_id === null || $article['id'] > $last_id) {
                $last_id = $article['id'];
            }
        }
        // set id for create article last id + 1
        $set_id = str_pad((int)$last_id + 1, strlen($last_id), '0', STR_PAD_LEFT);

        return view('Kretech::kretech_article', [
            'title'     => $title,
            'set_id'    => $set_id
        ]);
    }
}
