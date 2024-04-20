<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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

    public function store(Request $request)
    {
        // auth
        $auth = Auth::user();

        $validator = Validator::make($request->all(), [
            'create_title'          => 'required',
            'create_description'    => 'required',
            'create_image_1'        => 'image|mimes:jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // get profile
        $profile = DB::connection('mysql2')->table('profiles')->where('eml', $auth->email)->first();

        // get params
        $user_id            = Auth::user()->id;
        $module             = 'Kretech';
        $scene              = 'Article';
        $activity           = 'Create - ' . $request->create_id;
        $ip                 = $request->ip();
        $api_url            = env('API_URL');
        $destination_url    = env('API_URL') . 'data/upload_image.php';
        // ---
        $email              = $profile->eml;
        $code               = $profile->cod;
        $id                 = $request->create_id;
        $title              = $request->create_title;
        $category           = $request->create_category;
        $description        = $request->create_description;
        $status             = $request->create_status;
        $created_at         = Date('Y-m-d H:i:s');
        $updated_at         = Date('Y-m-d H:i:s');
        $image_1            = $request->file('create_image_1');
        $image_1_name       = isset($image_1) ? 'kretech_img_article_' . $id . '_thumbnail' . '.' . $image_1->extension() : '';
        $image_default  = public_path('assets/img/kretech_img_content_article_thumbnail_default.jpg');

        // temp variable
        $temp = [
            'user_id'           => $user_id,
            'module'            => $module,
            'scene'             => $scene,
            'activity'          => $activity,
            'ip'                => $ip,
            // ---
            'email'             => $email,
            'code'              => $code,
            'id'                => $id,
            'title'             => $title,
            'category'          => $category,
            'description'       => $description,
            'status'            => $status,
            'created_at'        => $created_at,
            'updated_at'        => $updated_at,
            'image_1'           => $image_1_name
        ];

        // check image 1
        if ($image_1 !== null || $image_1 != '') {
            /** CURL article image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $image_1_upload = $request->file('create_image_1');
            $image_1_name_upload = isset($image_1_upload) ? $code . '-art-' . $id . '.' . $image_1_upload->extension() : '';
            $image_1_upload_path = $image_1_upload->path();
            $data = array(
                'article_file_1' => new \CURLFile($image_1_upload_path, $image_1_upload->getClientMimeType(), $image_1_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Article Image Thumbnail')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $image_1->move(public_path('assets/img'), $image_1_name);
        } else if ($image_1 === null) {
            /** CURL article image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $image_1_default = public_path('assets\img\kretech_img_content_article_thumbnail_default.jpg');
            $image_1_upload = new UploadedFile(
                $image_1_default,
                'kretech_img_content_article_thumbnail_default.jpg',
                mime_content_type($image_1_default),
                filesize($image_1_default),
                false
            );
            $image_1_name_upload = isset($image_1_upload) ? $code . '-art-' . $id . '.' . $image_1_upload->extension() : '';
            $image_1_upload_path = $image_1_upload->path();
            $data = array(
                'article_file_1' => new \CURLFile($image_1_upload_path, $image_1_upload->getClientMimeType(), $image_1_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Article Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $image_set_1 = public_path('assets/img/kretech_img_content_article_thumbnail_' . $id . '.jpg');
            if (!copy($image_default, $image_set_1)) {
                Alert::error('Failed', 'Set Article Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // store article json
        $store_article_json = Http::post($api_url . 'profile/article/store/' . $code, [
            'id'            => $id,
            'title'         => $title,
            'category'      => $category,
            'description'   => $description,
            'status'        => $status,
        ]);

        if ($store_article_json != 'success store article') {
            Alert::error('Failed', 'Create Article')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Create Article')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Create Article')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
    }

    public function upload_image(Request $request): JsonResponse
    {
        $id = Auth::user()->id;

        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName =  'kretech_img_content_article_' . $id . '_' . $fileName . '.' . $extension;
      
            $request->file('upload')->move(public_path('assets/img'), $fileName);
      
            $url = asset('assets/img/' . $fileName);
  
            return response()->json([
                'fileName'  => $fileName,
                'uploaded'  => 1,
                'url'       => $url
            ]);
        }
    }

    public function edit($id)
    {
        $user_id = Auth::user()->id;

        // get user from table users
        $user = User::where('id', $user_id)->first();
            
        // verify user from table profile
        $profile = DB::connection('mysql2')->table('profiles')->where('eml', $user->email)->first();
        if ($profile == null) {
            return response('article not found', 404);
        }
        
        // declare variable
        $api_url = env('API_URL');
        $code = $profile->cod;
        
        // get data article from api
        $get_article = Http::get($api_url . 'profile/' . $code)->json()['article'];

        // check response
        if ($get_article == '[]') {
            return response('article not found', 404);
        }

        // get article by id 
        foreach ($get_article as $article) {
            if ($article['id'] == $id) {
                $articles = $article;
            }
        }

        return response()->json($articles);
    }

    public function update(Request $request)
    {
        // auth
        $auth = Auth::user();

        $validator = Validator::make($request->all(), [
            'edit_title'        => 'required',
            'edit_description'  => 'required',
            'edit_image_1'      => 'image|mimes:jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // get profile
        $profile = DB::connection('mysql2')->table('profiles')->where('eml', $auth->email)->first();

        // get params
        $user_id                = Auth::user()->id;
        $module                 = 'Kretech';
        $scene                  = 'Article';
        $activity               = 'Edit - ' . $request->edit_id;
        $ip                     = $request->ip();
        $api_url                = env('API_URL');
        $destination_url        = env('API_URL') . 'data/upload_image.php';
        // ---
        $email                  = $profile->eml;
        $code                   = $profile->cod;
        $id                     = $request->edit_id;
        $title                  = $request->edit_title;
        $category               = $request->edit_category;
        $description            = $request->edit_description;
        $status                 = $request->edit_status;
        $created_at             = $request->edit_created_at;
        $updated_at             = Date('Y-m-d H:i:s');
        $image_1                = $request->file('edit_image_1');
        $image_1_name           = isset($image_1) ? 'kretech_img_article_' . $id . '_thumbnail' . '.' . $image_1->extension() : '';
        $image_default          = public_path('assets/img/kretech_img_content_article_thumbnail_default.jpg');

        // temp variable
        $temp = [
            'user_id'           => $user_id,
            'module'            => $module,
            'scene'             => $scene,
            'activity'          => $activity,
            'ip'                => $ip,
            // ---
            'email'             => $email,
            'code'              => $code,
            'id'                => $id,
            'title'             => $title,
            'category'          => $category,
            'description'       => $description,
            'status'            => $status,
            'created_at'        => $created_at,
            'updated_at'        => $updated_at,
            'image_1'           => $image_1_name
        ];

        // check image 1
        if ($image_1 !== null || $image_1 != '') {
            /** CURL article image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $image_1_upload = $request->file('edit_image_1');
            $image_1_name_upload = isset($image_1_upload) ? $code . '-art-' . $id . '.' . $image_1_upload->extension() : '';
            $image_1_upload_path = $image_1_upload->path();
            $data = array(
                'article_file_1' => new \CURLFile($image_1_upload_path, $image_1_upload->getClientMimeType(), $image_1_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Article Image Thumbnail')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $image_1->move(public_path('assets/img'), $image_1_name);
        } else if ($image_1 === null) {
            /** CURL article image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $image_1_default = public_path('assets\img\kretech_img_content_article_thumbnail_default.jpg');
            $image_1_upload = new UploadedFile(
                $image_1_default,
                'kretech_img_content_article_thumbnail_default.jpg',
                mime_content_type($image_1_default),
                filesize($image_1_default),
                false
            );
            $image_1_name_upload = isset($image_1_upload) ? $code . '-art-' . $id . '.' . $image_1_upload->extension() : '';
            $image_1_upload_path = $image_1_upload->path();
            $data = array(
                'article_file_1' => new \CURLFile($image_1_upload_path, $image_1_upload->getClientMimeType(), $image_1_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Article Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $image_set_1 = public_path('assets/img/kretech_img_content_article_thumbnail_' . $id . '.jpg');
            if (!copy($image_default, $image_set_1)) {
                Alert::error('Failed', 'Set Article Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // update article json
        $update_article_json = Http::post($api_url . 'profile/article/update/' . $code, [
            'id'            => $id,
            'title'         => $title,
            'category'      => $category,
            'description'   => $description,
            'status'        => $status,
            'created_at'    => $created_at,
        ]);

        if ($update_article_json != 'success update article') {
            Alert::error('Failed', 'Edit Article')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Edit Article')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Edit Article')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
    }

    public function detail($id)
    {
        $user_id = Auth::user()->id;

        // get user from table users
        $user = User::where('id', $user_id)->first();
            
        // verify user from table profile
        $profile = DB::connection('mysql2')->table('profiles')->where('eml', $user->email)->first();
        if ($profile == null) {
            return response('article not found', 404);
        }
        
        // declare variable
        $api_url = env('API_URL');
        $code = $profile->cod;
        
        // get data article from api
        $get_article = Http::get($api_url . 'profile/' . $code)->json()['article'];

        // check response
        if ($get_article == '[]') {
            return response('article not found', 404);
        }

        // get article by id 
        foreach ($get_article as $article) {
            if ($article['id'] == $id) {
                $articles = $article;
            }
        }

        return response()->json($articles);
    }
}
