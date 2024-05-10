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

        // check article background detail update
        $check_art_bg_detail = LogActivity::select('activity')->where('user_id', $id)->where('scene', 'Article')->where('activity', 'like', '% Article Background Detail')->orderBy('created_at', 'desc')->first();
        if ($check_art_bg_detail) {
            $check_art_bg_detail = explode('-', $check_art_bg_detail->activity)[2];
            $check_art_bg_detail = explode(' ', $check_art_bg_detail)[1];
            $delete_art_bg_detail = ($check_art_bg_detail == 'Delete') ? 1 : 0;
        } else {
            $delete_art_bg_detail = 0;
        }

        return view('Kretech::kretech_article', [
            'title'                 => $title,
            'set_id'                => $set_id,
            'delete_art_bg_detail'  => $delete_art_bg_detail
        ]);
    }

    public function store(Request $request)
    {
        // auth
        $auth = Auth::user();

        $validator = Validator::make($request->all(), [
            'create_title'          => 'required',
            'create_description'    => 'required',
            'create_bg_detail'      => 'image|mimes:jpg|max:2048',
            'create_image_1'        => 'image|mimes:jpg|max:2048'
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
        $bg_detail          = $request->file('create_bg_detail');
        $bg_detail_name     = isset($bg_detail) ? 'kretech_img_profile_bg_article_dtl_' . $code . '_' . $id . '.' . $bg_detail->extension() : '';
        $bg_detail_default  = public_path('assets/img/kretech_img_profile_bg_article_dtl_default.jpg');
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
            'bg_detail'         => $bg_detail_name,
            'image_1'           => $image_1_name
        ];

        // check bg detail
        if ($bg_detail !== null || $bg_detail != '') {
            /** CURL article detail background */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $bg_detail_upload = $request->file('create_bg_detail');
            $bg_detail_name_upload = isset($bg_detail_upload) ? $code . '-bg-article-dtl-' . $id . '.' . $bg_detail_upload->extension() : '';
            $bg_detail_upload_path = $bg_detail_upload->path();
            $data = array(
                'background_detail_article_file_1' => new \CURLFile($bg_detail_upload_path, $bg_detail_upload->getClientMimeType(), $bg_detail_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Article Detail Background')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $bg_detail->move(public_path('assets/img'), $bg_detail_name);
        } else if ($bg_detail === null) {
            /** CURL article detail background */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $bg_detail_default = public_path('assets\img\kretech_img_profile_bg_article_dtl_default.jpg');
            $bg_detail_upload = new UploadedFile(
                $bg_detail_default,
                'kretech_img_profile_bg_article_dtl_default.jpg',
                mime_content_type($bg_detail_default),
                filesize($bg_detail_default),
                false
            );
            $bg_detail_name_upload = isset($bg_detail_upload) ? $code . '-bg-article-dtl-' . $id . '.' . $bg_detail_upload->extension() : '';
            $bg_detail_upload_path = $bg_detail_upload->path();
            $data = array(
                'background_detail_article_file_1' => new \CURLFile($bg_detail_upload_path, $bg_detail_upload->getClientMimeType(), $bg_detail_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Article Detail Background Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $bg_detail_set_1 = public_path('assets/img/kretech_img_profile_bg_article_dtl_' . $code . '_' . $id . '.jpg');
            if (!copy($bg_detail_default, $bg_detail_set_1)) {
                Alert::error('Failed', 'Set Article Detail Background Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

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
                $articles           = $article;
                $articles['cod']    = $code;
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
            'edit_bg_detail'    => 'image|mimes:jpg|max:2048',
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
        $bg_detail              = $request->file('edit_bg_detail');
        $bg_detail_name         = isset($bg_detail) ? 'kretech_img_profile_bg_article_dtl_' . $code . '_' . $id . '.' . $bg_detail->extension() : '';
        $bg_detail_default      = public_path('assets/img/kretech_img_profile_bg_article_dtl_default.jpg');
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
            'bg_detail'         => $bg_detail_name,
            'image_1'           => $image_1_name
        ];

        // check bg detail
        if ($bg_detail !== null || $bg_detail != '') {
            /** CURL article detail background */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $bg_detail_upload = $request->file('edit_bg_detail');
            $bg_detail_name_upload = isset($bg_detail_upload) ? $code . '-bg-article-dtl-' . $id . '.' . $bg_detail_upload->extension() : '';
            $bg_detail_upload_path = $bg_detail_upload->path();
            $data = array(
                'background_detail_article_file_1' => new \CURLFile($bg_detail_upload_path, $bg_detail_upload->getClientMimeType(), $bg_detail_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Article Detail Background')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $bg_detail->move(public_path('assets/img'), $bg_detail_name);

            // var log activity
            $activity   = 'Edit - ' . $auth->email . ' - Change Article Background Detail';

            // save log activity
            $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
            if (!$save_log_activity) {
                Alert::error('Failed', 'Update Profile')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        } else if ($bg_detail === null) {
            /** CURL article detail background */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $bg_detail_default = public_path('assets\img\kretech_img_profile_bg_article_dtl_default.jpg');
            $bg_detail_upload = new UploadedFile(
                $bg_detail_default,
                'kretech_img_profile_bg_article_dtl_default.jpg',
                mime_content_type($bg_detail_default),
                filesize($bg_detail_default),
                false
            );
            $bg_detail_name_upload = isset($bg_detail_upload) ? $code . '-bg-article-dtl-' . $id . '.' . $bg_detail_upload->extension() : '';
            $bg_detail_upload_path = $bg_detail_upload->path();
            $data = array(
                'background_detail_article_file_1' => new \CURLFile($bg_detail_upload_path, $bg_detail_upload->getClientMimeType(), $bg_detail_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Article Detail Background Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $bg_detail_set_1 = public_path('assets/img/kretech_img_profile_bg_article_dtl_' . $code . '_' . $id . '.jpg');
            if (!copy($bg_detail_default, $bg_detail_set_1)) {
                Alert::error('Failed', 'Set Article Detail Background Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }

            // var log activity
            $activity   = 'Edit - ' . $auth->email . ' - Change Article Background Detail Default';
            
            // save log activity
            $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
            if (!$save_log_activity) {
                Alert::error('Failed', 'Update Profile')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

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
                $articles           = $article;
                $articles['cod']    = $code;
            }
        }

        return response()->json($articles);
    }

    public function file(Request $request)
    {
        // auth
        $auth = Auth::user();
        
        // request ajax delete image
        if ($request->ajax()) {

            // get profile
            $profile = DB::connection('mysql2')->table('profiles')->where('eml', $auth->email)->first();
    
            // get field
            $user_id                = $auth->id;
            $module                 = 'Kretech';
            $ip                     = $request->ip();
            $destination_url        = env('API_URL') . 'data/upload_image.php';
            // ---
            $code                   = $profile->cod;
            $src_art_bg_detail_def = asset('assets/img/kretech_img_profile_bg_article_dtl_default.jpg');
    
            if ($request->input('action') == 'delete_article_background_detail') {
                $scene      = 'article';
                $activity   = 'Edit - ' . $auth->email . ' - Delete Article Background Detail';
                $art_id     = $request->input('art_id');
        
                /** CURL article background detail */
                $curl = curl_init();
                // Set destination URL
                curl_setopt($curl, CURLOPT_URL, $destination_url);
                curl_setopt($curl, CURLOPT_POST, true);
                $art_bg_detail_default = public_path('assets\img\kretech_img_profile_bg_article_dtl_default.jpg');
                $art_bg_detail_upload = new UploadedFile(
                    $art_bg_detail_default,
                    'kretech_img_profile_bg_article_dtl_default.jpg',
                    mime_content_type($art_bg_detail_default),
                    filesize($art_bg_detail_default),
                    false
                );
                
                $art_bg_detail_name_upload = isset($art_bg_detail_upload) ? $code . '-bg-article-dtl-' . $art_id . '.' . $art_bg_detail_upload->extension() : '';
                $art_bg_detail_upload_path = $art_bg_detail_upload->path();
                $data = array(
                    'background_detail_article_file_1' => new \CURLFile($art_bg_detail_upload_path, $art_bg_detail_upload->getClientMimeType(), $art_bg_detail_name_upload)
                );

                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                $result = curl_exec($curl);
                /** CURL photo break */
                if ($result === false) {
                    Alert::error('Failed', 'Set Article Background Detail Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                    return redirect()->back();
                }
        
                // delete article background detail
                $path_art_bg_detail_default = public_path('assets/img/kretech_img_profile_bg_article_dtl_default.jpg');
                $path_art_bg_detail_profile = public_path('assets/img/kretech_img_profile_bg_article_dtl_' . $code . '_' . $art_id . '.jpg');
            
                if (!File::copy($path_art_bg_detail_default, $path_art_bg_detail_profile)) {
                    return response()->json(['message' => 'Article Background Detail Anda gagal dihapus!'], 500);
                }
        
                // save log activity
                $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
                if (!$save_log_activity) {
                    Alert::error('Failed', 'Delete Article Background Detail')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                    return redirect()->back();
                }
        
                return response()->json(['message' => 'success', 'src' => $src_art_bg_detail_def], 200);
            }
        }
    }
}
