<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Client\RequestException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Response;
use Yajra\DataTables\DataTables;

class PortfolioController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'Kretech Portfolio';
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
        
        // get data portfolio from api
        $get_portfolio = Http::get($api_url . 'profile/' . $code)->json()['portfolio'];

        // check response
        if ($get_portfolio == '[]') {
          Alert::error('Failed', 'Portfolio Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
        }

        // show data if ajax request
        if ($request->ajax()) {
            return response()->json($get_portfolio);
        }

        // get last id portfolio
        $last_id = null;
        foreach ($get_portfolio as $portfolio) {
            if ($last_id === null || $portfolio['id'] > $last_id) {
                $last_id = $portfolio['id'];
            }
        }
        // set id for create portfolio last id + 1
        $set_id = str_pad((int)$last_id + 1, strlen($last_id), '0', STR_PAD_LEFT);

        // check portfolio background detail update
        $check_port_bg_detail = LogActivity::select('activity')->where('user_id', $id)->where('scene', 'Portfolio')->where('activity', 'like', '% Portfolio Background Detail')->orderBy('created_at', 'desc')->first();
        if ($check_port_bg_detail) {
            $check_port_bg_detail = explode('-', $check_port_bg_detail->activity)[2];
            $check_port_bg_detail = explode(' ', $check_port_bg_detail)[1];
            $delete_port_bg_detail = ($check_port_bg_detail == 'Delete') ? 1 : 0;
        } else {
            $delete_port_bg_detail = 0;
        }

        return view('Kretech::kretech_portfolio', [
            'title'                 => $title,
            'set_id'                => $set_id,
            'delete_port_bg_detail' => $delete_port_bg_detail
        ]);
    }

    public function store(Request $request)
    {
        // auth
        $auth = Auth::user();

        $validator = Validator::make($request->all(), [
            'create_title'              => 'required',
            'create_link'               => 'required',
            'create_client'             => 'required',
            'create_bg_detail'          => 'image|mimes:jpg|max:2048',
            'create_content_image_1'    => 'image|mimes:jpg|max:2048',
            'create_content_image_2'    => 'image|mimes:jpg|max:2048',
            'create_content_image_3'    => 'image|mimes:jpg|max:2048',
            'create_content_image_4'    => 'image|mimes:jpg|max:2048',
            'create_content_image_5'    => 'image|mimes:jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // get profile
        $profile = DB::connection('mysql2')->table('profiles')->where('eml', $auth->email)->first();

        // get params
        $user_id                = Auth::user()->id;
        $module                 = 'Kretech';
        $scene                  = 'Portfolio';
        $activity               = 'Create - ' . $request->create_id;
        $ip                     = $request->ip();
        $api_url                = env('API_URL');
        $destination_url        = env('API_URL') . 'data/upload_image.php';
        // ---
        $email                  = $profile->eml;
        $code                   = $profile->cod;
        $id                     = $request->create_id;
        $title                  = $request->create_title;
        $category               = $request->create_category;
        $client                 = $request->create_client;
        $link                   = $request->create_link;
        $content_titles         = [
            $request->create_content_title_1,
            $request->create_content_title_2,
            $request->create_content_title_3,
            $request->create_content_title_4,
            $request->create_content_title_5
        ];
        $content_title          = implode('|', array_filter($content_titles, function($value) {
            return $value !== null;
        }));
        $content_descs          = [
            $request->create_content_description_1,
            $request->create_content_description_2,
            $request->create_content_description_3,
            $request->create_content_description_4,
            $request->create_content_description_5
        ];
        $content_desc           = implode('|', array_filter($content_descs, function($value) {
            return $value !== null;
        }));
        $status                 = $request->create_status;
        $created_at             = Date('Y-m-d H:i:s');
        $updated_at             = Date('Y-m-d H:i:s');
        $bg_detail              = $request->file('create_bg_detail');
        $bg_detail_name         = isset($bg_detail) ? 'kretech_img_profile_bg_portfolio_dtl_' . $code . '_' . $id . '.' . $bg_detail->extension() : '';
        $bg_detail_default      = public_path('assets/img/kretech_img_profile_bg_portfolio_dtl_default.jpg');
        $content_image_1        = $request->file('create_content_image_1');
        $content_image_1_name   = isset($content_image_1) ? 'kretech_img_content_portfolio_' . $id . '_item_1' . '.' . $content_image_1->extension() : '';
        $content_image_2        = $request->file('create_content_image_2');
        $content_image_2_name   = isset($content_image_2) ? 'kretech_img_content_portfolio_' . $id . '_item_2' . '.' . $content_image_2->extension() : '';
        $content_image_3        = $request->file('create_content_image_3');
        $content_image_3_name   = isset($content_image_3) ? 'kretech_img_content_portfolio_' . $id . '_item_3' . '.' . $content_image_3->extension() : '';
        $content_image_4        = $request->file('create_content_image_4');
        $content_image_4_name   = isset($content_image_4) ? 'kretech_img_content_portfolio_' . $id . '_item_4' . '.' . $content_image_4->extension() : '';
        $content_image_5        = $request->file('create_content_image_5');
        $content_image_5_name   = isset($content_image_5) ? 'kretech_img_content_portfolio_' . $id . '_item_5' . '.' . $content_image_5->extension() : '';
        $content_image_default  = public_path('assets/img/kretech_img_content_portfolio_default.jpg');

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
            'client'            => $client,
            'link'              => $link,
            'content_title'     => $content_title,
            'content_desc'      => $content_desc,
            'status'            => $status,
            'created_at'        => $created_at,
            'updated_at'        => $updated_at,
            'bg_detail'         => $bg_detail_name,
            'content_image_1'   => $content_image_1_name,
            'content_image_2'   => $content_image_2_name,
            'content_image_3'   => $content_image_3_name,
            'content_image_4'   => $content_image_4_name,
            'content_image_5'   => $content_image_5_name
        ];

        // check bg detail
        if ($bg_detail !== null || $bg_detail != '') {
            /** CURL portfolio detail background */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $bg_detail_upload = $request->file('create_bg_detail');
            $bg_detail_name_upload = isset($bg_detail_upload) ? $code . '-bg-portfolio-dtl-' . $id . '.' . $bg_detail_upload->extension() : '';
            $bg_detail_upload_path = $bg_detail_upload->path();
            $data = array(
                'background_detail_portfolio_file_1' => new \CURLFile($bg_detail_upload_path, $bg_detail_upload->getClientMimeType(), $bg_detail_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Detail Background')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $bg_detail->move(public_path('assets/img'), $bg_detail_name);
        } else if ($bg_detail === null) {
            /** CURL portfolio detail background */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $bg_detail_default = public_path('assets\img\kretech_img_profile_bg_portfolio_dtl_default.jpg');
            $bg_detail_upload = new UploadedFile(
                $bg_detail_default,
                'kretech_img_profile_bg_portfolio_dtl_default.jpg',
                mime_content_type($bg_detail_default),
                filesize($bg_detail_default),
                false
            );
            $bg_detail_name_upload = isset($bg_detail_upload) ? $code . '-bg-portfolio-dtl-' . $id . '.' . $bg_detail_upload->extension() : '';
            $bg_detail_upload_path = $bg_detail_upload->path();
            $data = array(
                'background_detail_portfolio_file_1' => new \CURLFile($bg_detail_upload_path, $bg_detail_upload->getClientMimeType(), $bg_detail_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Detail Background Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $bg_detail_set_1 = public_path('assets/img/kretech_img_profile_bg_portfolio_dtl_' . $code . '_' . $id . '.jpg');
            if (!copy($bg_detail_default, $bg_detail_set_1)) {
                Alert::error('Failed', 'Set Portfolio Detail Background Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // check content image 1
        if ($content_image_1 !== null || $content_image_1 != '') {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_1_upload = $request->file('create_content_image_1');
            $content_image_1_name_upload = isset($content_image_1_upload) ? $code . '-port-' . $id . '-1' . '.' . $content_image_1_upload->extension() : '';
            $content_image_1_upload_path = $content_image_1_upload->path();
            $data = array(
                'portfolio_file_1' => new \CURLFile($content_image_1_upload_path, $content_image_1_upload->getClientMimeType(), $content_image_1_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Item 1')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_1->move(public_path('assets/img'), $content_image_1_name);
        } else if ($request->create_content_title_1 != null && $content_image_1 === null) {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_1_default = public_path('assets\img\kretech_img_content_portfolio_default.jpg');
            $content_image_1_upload = new UploadedFile(
                $content_image_1_default,
                'kretech_img_content_portfolio_default.jpg',
                mime_content_type($content_image_1_default),
                filesize($content_image_1_default),
                false
            );
            $content_image_1_name_upload = isset($content_image_1_upload) ? $code . '-port-' . $id . '-1' . '.' . $content_image_1_upload->extension() : '';
            $content_image_1_upload_path = $content_image_1_upload->path();
            $data = array(
                'portfolio_file_1' => new \CURLFile($content_image_1_upload_path, $content_image_1_upload->getClientMimeType(), $content_image_1_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_set_1 = public_path('assets/img/kretech_img_content_portfolio_' . $id . '_item_1.jpg');
            if (!copy($content_image_default, $content_image_set_1)) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // check content image 2
        if ($content_image_2 !== null || $content_image_2 != '') {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_2_upload = $request->file('create_content_image_2');
            $content_image_2_name_upload = isset($content_image_2_upload) ? $code . '-port-' . $id . '-2' . '.' . $content_image_2_upload->extension() : '';
            $content_image_2_upload_path = $content_image_2_upload->path();
            $data = array(
                'portfolio_file_2' => new \CURLFile($content_image_2_upload_path, $content_image_2_upload->getClientMimeType(), $content_image_2_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Item 2')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_2->move(public_path('assets/img'), $content_image_2_name);
        } else if ($request->create_content_title_2 != null && $content_image_2 === null) {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_2_default = public_path('assets\img\kretech_img_content_portfolio_default.jpg');
            $content_image_2_upload = new UploadedFile(
                $content_image_2_default,
                'kretech_img_content_portfolio_default.jpg',
                mime_content_type($content_image_2_default),
                filesize($content_image_2_default),
                false
            );
            $content_image_2_name_upload = isset($content_image_2_upload) ? $code . '-port-' . $id . '-2' . '.' . $content_image_2_upload->extension() : '';
            $content_image_2_upload_path = $content_image_2_upload->path();
            $data = array(
                'portfolio_file_2' => new \CURLFile($content_image_2_upload_path, $content_image_2_upload->getClientMimeType(), $content_image_2_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_set_2 = public_path('assets/img/kretech_img_content_portfolio_' . $id . '_item_2.jpg');
            if (!copy($content_image_default, $content_image_set_2)) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // check content image 3
        if ($content_image_3 !== null || $content_image_3 != '') {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_3_upload = $request->file('create_content_image_3');
            $content_image_3_name_upload = isset($content_image_3_upload) ? $code . '-port-' . $id . '-3' . '.' . $content_image_3_upload->extension() : '';
            $content_image_3_upload_path = $content_image_3_upload->path();
            $data = array(
                'portfolio_file_3' => new \CURLFile($content_image_3_upload_path, $content_image_3_upload->getClientMimeType(), $content_image_3_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Item 3')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_3->move(public_path('assets/img'), $content_image_3_name);
        } else if ($request->create_content_title_3 != null && $content_image_3 === null) {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_3_default = public_path('assets\img\kretech_img_content_portfolio_default.jpg');
            $content_image_3_upload = new UploadedFile(
                $content_image_3_default,
                'kretech_img_content_portfolio_default.jpg',
                mime_content_type($content_image_3_default),
                filesize($content_image_3_default),
                false
            );
            $content_image_3_name_upload = isset($content_image_3_upload) ? $code . '-port-' . $id . '-3' . '.' . $content_image_3_upload->extension() : '';
            $content_image_3_upload_path = $content_image_3_upload->path();
            $data = array(
                'portfolio_file_3' => new \CURLFile($content_image_3_upload_path, $content_image_3_upload->getClientMimeType(), $content_image_3_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_set_3 = public_path('assets/img/kretech_img_content_portfolio_' . $id . '_item_3.jpg');
            if (!copy($content_image_default, $content_image_set_3)) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // check content image 4
        if ($content_image_4 !== null || $content_image_4 != '') {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_4_upload = $request->file('create_content_image_4');
            $content_image_4_name_upload = isset($content_image_4_upload) ? $code . '-port-' . $id . '-4' . '.' . $content_image_4_upload->extension() : '';
            $content_image_4_upload_path = $content_image_4_upload->path();
            $data = array(
                'portfolio_file_4' => new \CURLFile($content_image_4_upload_path, $content_image_4_upload->getClientMimeType(), $content_image_4_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Item 4')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_4->move(public_path('assets/img'), $content_image_4_name);
        } else if ($request->create_content_title_4 != null && $content_image_4 === null) {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_4_default = public_path('assets\img\kretech_img_content_portfolio_default.jpg');
            $content_image_4_upload = new UploadedFile(
                $content_image_4_default,
                'kretech_img_content_portfolio_default.jpg',
                mime_content_type($content_image_4_default),
                filesize($content_image_4_default),
                false
            );
            $content_image_4_name_upload = isset($content_image_4_upload) ? $code . '-port-' . $id . '-4' . '.' . $content_image_4_upload->extension() : '';
            $content_image_4_upload_path = $content_image_4_upload->path();
            $data = array(
                'portfolio_file_4' => new \CURLFile($content_image_4_upload_path, $content_image_4_upload->getClientMimeType(), $content_image_4_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            $content_image_set_4 = public_path('assets/img/kretech_img_content_portfolio_' . $id . '_item_4.jpg');
            if (!copy($content_image_default, $content_image_set_4)) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // check content image 5
        if ($content_image_5 !== null || $content_image_5 != '') {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_5_upload = $request->file('create_content_image_5');
            $content_image_5_name_upload = isset($content_image_5_upload) ? $code . '-port-' . $id . '-5' . '.' . $content_image_5_upload->extension() : '';
            $content_image_5_upload_path = $content_image_5_upload->path();
            $data = array(
                'portfolio_file_5' => new \CURLFile($content_image_5_upload_path, $content_image_5_upload->getClientMimeType(), $content_image_5_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Item 5')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_5->move(public_path('assets/img'), $content_image_5_name);
        } else if ($request->create_content_title_5 != null && $content_image_5 === null) {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_5_default = public_path('assets\img\kretech_img_content_portfolio_default.jpg');
            $content_image_5_upload = new UploadedFile(
                $content_image_5_default,
                'kretech_img_content_portfolio_default.jpg',
                mime_content_type($content_image_5_default),
                filesize($content_image_5_default),
                false
            );
            $content_image_5_name_upload = isset($content_image_5_upload) ? $code . '-port-' . $id . '-5' . '.' . $content_image_5_upload->extension() : '';
            $content_image_5_upload_path = $content_image_5_upload->path();
            $data = array(
                'portfolio_file_5' => new \CURLFile($content_image_5_upload_path, $content_image_5_upload->getClientMimeType(), $content_image_5_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            $content_image_set_5 = public_path('assets/img/kretech_img_content_portfolio_' . $id . '_item_5.jpg');
            if (!copy($content_image_default, $content_image_set_5)) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // $source_dir = public_path('assets/img');
        // $destination_dir = public_path('assets/img');
        // $files = File::files($source_dir);
        // foreach ($files as $file) {
        //     $copy_image = File::copy($file, $destination_dir . '/' . $file->getFilename());
        //     if ($copy_image) {
        //         $new_image_name = 'kretech_img_content_portfolio_' . $id . '_item_1.jpg';
        //         $rename_image = File::move($destination_dir . '/' . $file->getFilename(), $destination_dir . '/' . $new_image_name);
        //         if (!$rename_image) {
        //             Alert::error('Failed', 'Set Portfolio Item Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        //             return redirect()->back();
        //         }
        //     } else {
        //         Alert::error('Failed', 'Set Portfolio Item Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        //         return redirect()->back();
        //     }
        // }

        // store portfolio json
        $store_portfolio_json = Http::post($api_url . 'profile/portfolio/store/' . $code, [
            'id'            => $id,
            'title'         => $title,
            'category'      => $category,
            'client'        => $client,
            'link'          => $link,
            'content_title' => $content_title,
            'content_desc'  => $content_desc,
            'status'        => $status,
        ]);

        if ($store_portfolio_json != 'success store portfolio') {
            Alert::error('Failed', 'Create Portfolio')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Create Portfolio')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Create Portfolio')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
    }

    public function edit($id)
    {
        $user_id = Auth::user()->id;

        // get user from table users
        $user = User::where('id', $user_id)->first();
            
        // verify user from table profile
        $profile = DB::connection('mysql2')->table('profiles')->where('eml', $user->email)->first();
        if ($profile == null) {
            return response('portfolio not found', 404);
        }
        
        // declare variable
        $api_url = env('API_URL');
        $code = $profile->cod;
        
        // get data portfolio from api
        $get_portfolio = Http::get($api_url . 'profile/' . $code)->json()['portfolio'];

        // check response
        if ($get_portfolio == '[]') {
            return response('portfolio not found', 404);
        }

        // get portfolio by id 
        foreach ($get_portfolio as $portfolio) {
            if ($portfolio['id'] == $id) {
                $portfolios         = $portfolio;
                $portfolios['cod']  = $code;
            }
        }

        return response()->json($portfolios);
    }
    
    public function update(Request $request)
    {
        // auth
        $auth = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'edit_title'            => 'required',
            'edit_link'             => 'required',
            'edit_client'           => 'required',
            'edit_bg_detail'        => 'image|mimes:jpg|max:2048',
            'edit_content_image_1'  => 'image|mimes:jpg|max:2048',
            'edit_content_image_2'  => 'image|mimes:jpg|max:2048',
            'edit_content_image_3'  => 'image|mimes:jpg|max:2048',
            'edit_content_image_4'  => 'image|mimes:jpg|max:2048',
            'edit_content_image_5'  => 'image|mimes:jpg|max:2048'
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // get profile
        $profile = DB::connection('mysql2')->table('profiles')->where('eml', $auth->email)->first();
        
        // get params
        $user_id                = Auth::user()->id;
        $module                 = 'Kretech';
        $scene                  = 'Portfolio';
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
        $client                 = $request->edit_client;
        $link                   = $request->edit_link;
        $content_titles         = [
            $request->edit_content_title_1,
            $request->edit_content_title_2,
            $request->edit_content_title_3,
            $request->edit_content_title_4,
            $request->edit_content_title_5
        ];
        $content_title          = implode('|', array_filter($content_titles, function($value) {
            return $value !== null;
        }));
        $content_descs          = [
            $request->edit_content_description_1,
            $request->edit_content_description_2,
            $request->edit_content_description_3,
            $request->edit_content_description_4,
            $request->edit_content_description_5
        ];
        $content_desc           = implode('|', array_filter($content_descs, function($value) {
            return $value !== null;
        }));
        $status                 = $request->edit_status;
        $created_at             = $request->edit_created_at;
        $updated_at             = Date('Y-m-d H:i:s');
        $bg_detail              = $request->file('edit_bg_detail');
        $bg_detail_name         = isset($bg_detail) ? 'kretech_img_profile_bg_portfolio_dtl_' . $code . '_' . $id . '.' . $bg_detail->extension() : '';
        $bg_detail_default      = public_path('assets/img/kretech_img_profile_bg_portfolio_dtl_default.jpg');
        $content_image_1        = $request->file('edit_content_image_1');
        $content_image_1_name   = isset($content_image_1) ? 'kretech_img_content_portfolio_' . $id . '_item_1' . '.' . $content_image_1->extension() : '';
        $content_image_2        = $request->file('edit_content_image_2');
        $content_image_2_name   = isset($content_image_2) ? 'kretech_img_content_portfolio_' . $id . '_item_2' . '.' . $content_image_2->extension() : '';
        $content_image_3        = $request->file('edit_content_image_3');
        $content_image_3_name   = isset($content_image_3) ? 'kretech_img_content_portfolio_' . $id . '_item_3' . '.' . $content_image_3->extension() : '';
        $content_image_4        = $request->file('edit_content_image_4');
        $content_image_4_name   = isset($content_image_4) ? 'kretech_img_content_portfolio_' . $id . '_item_4' . '.' . $content_image_4->extension() : '';
        $content_image_5        = $request->file('edit_content_image_5');
        $content_image_5_name   = isset($content_image_5) ? 'kretech_img_content_portfolio_' . $id . '_item_5' . '.' . $content_image_5->extension() : '';
        $content_image_default  = public_path('assets/img/kretech_img_content_portfolio_default.jpg');

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
            'client'            => $client,
            'link'              => $link,
            'content_title'     => $content_title,
            'content_desc'      => $content_desc,
            'status'            => $status,
            'created_at'        => $created_at,
            'updated_at'        => $updated_at,
            'bg_detail'         => $bg_detail_name,
            'content_image_1'   => $content_image_1_name,
            'content_image_2'   => $content_image_2_name,
            'content_image_3'   => $content_image_3_name,
            'content_image_4'   => $content_image_4_name,
            'content_image_5'   => $content_image_5_name
        ];

        // check bg detail
        if ($bg_detail !== null || $bg_detail != '') {
            /** CURL portfolio detail background */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $bg_detail_upload = $request->file('edit_bg_detail');
            $bg_detail_name_upload = isset($bg_detail_upload) ? $code . '-bg-portfolio-dtl-' . $id . '.' . $bg_detail_upload->extension() : '';
            $bg_detail_upload_path = $bg_detail_upload->path();
            $data = array(
                'background_detail_portfolio_file_1' => new \CURLFile($bg_detail_upload_path, $bg_detail_upload->getClientMimeType(), $bg_detail_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Detail Background')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $bg_detail->move(public_path('assets/img'), $bg_detail_name);

            // var log activity
            $activity   = 'Edit - ' . $auth->email . ' - Change Portfolio Background Detail';

            // save log activity
            $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
            if (!$save_log_activity) {
                Alert::error('Failed', 'Update Profile')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        } else if ($bg_detail === null) {
            /** CURL portfolio detail background */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $bg_detail_default = public_path('assets\img\kretech_img_profile_bg_portfolio_dtl_default.jpg');
            $bg_detail_upload = new UploadedFile(
                $bg_detail_default,
                'kretech_img_profile_bg_portfolio_dtl_default.jpg',
                mime_content_type($bg_detail_default),
                filesize($bg_detail_default),
                false
            );
            $bg_detail_name_upload = isset($bg_detail_upload) ? $code . '-bg-portfolio-dtl-' . $id . '.' . $bg_detail_upload->extension() : '';
            $bg_detail_upload_path = $bg_detail_upload->path();
            $data = array(
                'background_detail_portfolio_file_1' => new \CURLFile($bg_detail_upload_path, $bg_detail_upload->getClientMimeType(), $bg_detail_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Detail Background Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $bg_detail_set_1 = public_path('assets/img/kretech_img_profile_bg_portfolio_dtl_' . $code . '_' . $id . '.jpg');
            if (!copy($bg_detail_default, $bg_detail_set_1)) {
                Alert::error('Failed', 'Set Portfolio Detail Background Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }

            // var log activity
            $activity   = 'Edit - ' . $auth->email . ' - Change Portfolio Background Detail Default';
            
            // save log activity
            $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
            if (!$save_log_activity) {
                Alert::error('Failed', 'Update Profile')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // check content image 1
        if ($content_image_1 !== null || $content_image_1 != '') {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_1_upload = $request->file('edit_content_image_1');
            $content_image_1_name_upload = isset($content_image_1_upload) ? $code . '-port-' . $id . '-1' . '.' . $content_image_1_upload->extension() : '';
            $content_image_1_upload_path = $content_image_1_upload->path();
            $data = array(
                'portfolio_file_1' => new \CURLFile($content_image_1_upload_path, $content_image_1_upload->getClientMimeType(), $content_image_1_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Item 1')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_1->move(public_path('assets/img'), $content_image_1_name);
        } else if ($request->edit_content_title_1 != null && $content_image_1 === null) {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_1_default = public_path('assets\img\kretech_img_content_portfolio_default.jpg');
            $content_image_1_upload = new UploadedFile(
                $content_image_1_default,
                'kretech_img_content_portfolio_default.jpg',
                mime_content_type($content_image_1_default),
                filesize($content_image_1_default),
                false
            );
            $content_image_1_name_upload = isset($content_image_1_upload) ? $code . '-port-' . $id . '-1' . '.' . $content_image_1_upload->extension() : '';
            $content_image_1_upload_path = $content_image_1_upload->path();
            $data = array(
                'portfolio_file_1' => new \CURLFile($content_image_1_upload_path, $content_image_1_upload->getClientMimeType(), $content_image_1_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_set_1 = public_path('assets/img/kretech_img_content_portfolio_' . $id . '_item_1.jpg');
            if (!copy($content_image_default, $content_image_set_1)) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // check content image 2
        if ($content_image_2 !== null || $content_image_2 != '') {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_2_upload = $request->file('edit_content_image_2');
            $content_image_2_name_upload = isset($content_image_2_upload) ? $code . '-port-' . $id . '-2' . '.' . $content_image_2_upload->extension() : '';
            $content_image_2_upload_path = $content_image_2_upload->path();
            $data = array(
                'portfolio_file_2' => new \CURLFile($content_image_2_upload_path, $content_image_2_upload->getClientMimeType(), $content_image_2_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Item 2')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_2->move(public_path('assets/img'), $content_image_2_name);
        } else if ($request->edit_content_title_2 != null && $content_image_2 === null) {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_2_default = public_path('assets\img\kretech_img_content_portfolio_default.jpg');
            $content_image_2_upload = new UploadedFile(
                $content_image_2_default,
                'kretech_img_content_portfolio_default.jpg',
                mime_content_type($content_image_2_default),
                filesize($content_image_2_default),
                false
            );
            $content_image_2_name_upload = isset($content_image_2_upload) ? $code . '-port-' . $id . '-2' . '.' . $content_image_2_upload->extension() : '';
            $content_image_2_upload_path = $content_image_2_upload->path();
            $data = array(
                'portfolio_file_2' => new \CURLFile($content_image_2_upload_path, $content_image_2_upload->getClientMimeType(), $content_image_2_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_set_2 = public_path('assets/img/kretech_img_content_portfolio_' . $id . '_item_2.jpg');
            if (!copy($content_image_default, $content_image_set_2)) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // check content image 3
        if ($content_image_3 !== null || $content_image_3 != '') {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_3_upload = $request->file('edit_content_image_3');
            $content_image_3_name_upload = isset($content_image_3_upload) ? $code . '-port-' . $id . '-3' . '.' . $content_image_3_upload->extension() : '';
            $content_image_3_upload_path = $content_image_3_upload->path();
            $data = array(
                'portfolio_file_3' => new \CURLFile($content_image_3_upload_path, $content_image_3_upload->getClientMimeType(), $content_image_3_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Item 3')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_3->move(public_path('assets/img'), $content_image_3_name);
        } else if ($request->edit_content_title_3 != null && $content_image_3 === null) {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_3_default = public_path('assets\img\kretech_img_content_portfolio_default.jpg');
            $content_image_3_upload = new UploadedFile(
                $content_image_3_default,
                'kretech_img_content_portfolio_default.jpg',
                mime_content_type($content_image_3_default),
                filesize($content_image_3_default),
                false
            );
            $content_image_3_name_upload = isset($content_image_3_upload) ? $code . '-port-' . $id . '-3' . '.' . $content_image_3_upload->extension() : '';
            $content_image_3_upload_path = $content_image_3_upload->path();
            $data = array(
                'portfolio_file_3' => new \CURLFile($content_image_3_upload_path, $content_image_3_upload->getClientMimeType(), $content_image_3_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_set_3 = public_path('assets/img/kretech_img_content_portfolio_' . $id . '_item_3.jpg');
            if (!copy($content_image_default, $content_image_set_3)) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // check content image 4
        if ($content_image_4 !== null || $content_image_4 != '') {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_4_upload = $request->file('edit_content_image_4');
            $content_image_4_name_upload = isset($content_image_4_upload) ? $code . '-port-' . $id . '-4' . '.' . $content_image_4_upload->extension() : '';
            $content_image_4_upload_path = $content_image_4_upload->path();
            $data = array(
                'portfolio_file_4' => new \CURLFile($content_image_4_upload_path, $content_image_4_upload->getClientMimeType(), $content_image_4_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Item 4')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_4->move(public_path('assets/img'), $content_image_4_name);
        } else if ($request->edit_content_title_4 != null && $content_image_4 === null) {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_4_default = public_path('assets\img\kretech_img_content_portfolio_default.jpg');
            $content_image_4_upload = new UploadedFile(
                $content_image_4_default,
                'kretech_img_content_portfolio_default.jpg',
                mime_content_type($content_image_4_default),
                filesize($content_image_4_default),
                false
            );
            $content_image_4_name_upload = isset($content_image_4_upload) ? $code . '-port-' . $id . '-4' . '.' . $content_image_4_upload->extension() : '';
            $content_image_4_upload_path = $content_image_4_upload->path();
            $data = array(
                'portfolio_file_4' => new \CURLFile($content_image_4_upload_path, $content_image_4_upload->getClientMimeType(), $content_image_4_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_set_4 = public_path('assets/img/kretech_img_content_portfolio_' . $id . '_item_4.jpg');
            if (!copy($content_image_default, $content_image_set_4)) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }

        // check content image 5
        if ($content_image_5 !== null || $content_image_5 != '') {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_5_upload = $request->file('edit_content_image_5');
            $content_image_5_name_upload = isset($content_image_5_upload) ? $code . '-port-' . $id . '-5' . '.' . $content_image_5_upload->extension() : '';
            $content_image_5_upload_path = $content_image_5_upload->path();
            $data = array(
                'portfolio_file_5' => new \CURLFile($content_image_5_upload_path, $content_image_5_upload->getClientMimeType(), $content_image_5_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Item 5')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_5->move(public_path('assets/img'), $content_image_5_name);
        } else if ($request->edit_content_title_5 != null && $content_image_5 === null) {
            /** CURL portfolio image */
            $curl = curl_init();
            // Set destination URL
            curl_setopt($curl, CURLOPT_URL, $destination_url);
            curl_setopt($curl, CURLOPT_POST, true);
            $content_image_5_default = public_path('assets\img\kretech_img_content_portfolio_default.jpg');
            $content_image_5_upload = new UploadedFile(
                $content_image_5_default,
                'kretech_img_content_portfolio_default.jpg',
                mime_content_type($content_image_5_default),
                filesize($content_image_5_default),
                false
            );
            $content_image_5_name_upload = isset($content_image_5_upload) ? $code . '-port-' . $id . '-5' . '.' . $content_image_5_upload->extension() : '';
            $content_image_5_upload_path = $content_image_5_upload->path();
            $data = array(
                'portfolio_file_5' => new \CURLFile($content_image_5_upload_path, $content_image_5_upload->getClientMimeType(), $content_image_5_name_upload)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($curl);
            /** CURL photo break */
            if ($result === false) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
            $content_image_set_5 = public_path('assets/img/kretech_img_content_portfolio_' . $id . '_item_5.jpg');
            if (!copy($content_image_default, $content_image_set_5)) {
                Alert::error('Failed', 'Set Portfolio Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                return redirect()->back();
            }
        }
        
        // update portfolio json
        $update_portfolio_json = Http::post($api_url . 'profile/portfolio/update/' . $code, [
            'id'            => $id,
            'title'         => $title,
            'category'      => $category,
            'client'        => $client,
            'link'          => $link,
            'content_title' => $content_title,
            'content_desc'  => $content_desc,
            'status'        => $status,
            'created_at'    => $created_at,
        ]);

        if ($update_portfolio_json != 'success update portfolio') {
            Alert::error('Failed', 'Edit Portfolio')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // var log activity
        $activity   = 'Edit - ' . $request->edit_id;

        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Edit Portfolio')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Edit Portfolio')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
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
            return response('portfolio not found', 404);
        }
        
        // declare variable
        $api_url = env('API_URL');
        $code = $profile->cod;
        
        // get data portfolio from api
        $get_portfolio = Http::get($api_url . 'profile/' . $code)->json()['portfolio'];

        // check response
        if ($get_portfolio == '[]') {
            return response('portfolio not found', 404);
        }

        // get portfolio by id 
        foreach ($get_portfolio as $portfolio) {
            if ($portfolio['id'] == $id) {
                $portfolios         = $portfolio;
                $portfolios['cod']  = $code;
            }
        }

        return response()->json($portfolios);
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
            $src_port_bg_detail_def = asset('assets/img/kretech_img_profile_bg_portfolio_dtl_default.jpg');
    
            if ($request->input('action') == 'delete_portfolio_background_detail') {
                $scene      = 'Portfolio';
                $activity   = 'Edit - ' . $auth->email . ' - Delete Portfolio Background Detail';
                $port_id    = $request->input('port_id');
        
                /** CURL portfolio background detail */
                $curl = curl_init();
                // Set destination URL
                curl_setopt($curl, CURLOPT_URL, $destination_url);
                curl_setopt($curl, CURLOPT_POST, true);
                $port_bg_detail_default = public_path('assets\img\kretech_img_profile_bg_portfolio_dtl_default.jpg');
                $port_bg_detail_upload = new UploadedFile(
                    $port_bg_detail_default,
                    'kretech_img_profile_bg_portfolio_dtl_default.jpg',
                    mime_content_type($port_bg_detail_default),
                    filesize($port_bg_detail_default),
                    false
                );
                
                $port_bg_detail_name_upload = isset($port_bg_detail_upload) ? $code . '-bg-portfolio-dtl-' . $port_id . '.' . $port_bg_detail_upload->extension() : '';
                $port_bg_detail_upload_path = $port_bg_detail_upload->path();
                $data = array(
                    'background_detail_portfolio_file_1' => new \CURLFile($port_bg_detail_upload_path, $port_bg_detail_upload->getClientMimeType(), $port_bg_detail_name_upload)
                );

                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                $result = curl_exec($curl);
                /** CURL photo break */
                if ($result === false) {
                    Alert::error('Failed', 'Set Portfolio Background Detail Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                    return redirect()->back();
                }
        
                // delete portfolio background detail
                $path_port_bg_detail_default = public_path('assets/img/kretech_img_profile_bg_portfolio_dtl_default.jpg');
                $path_port_bg_detail_profile = public_path('assets/img/kretech_img_profile_bg_portfolio_dtl_' . $code . '_' . $port_id . '.jpg');
            
                if (!File::copy($path_port_bg_detail_default, $path_port_bg_detail_profile)) {
                    return response()->json(['message' => 'Portfolio Background Detail Anda gagal dihapus!'], 500);
                }
        
                // save log activity
                $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
                if (!$save_log_activity) {
                    Alert::error('Failed', 'Delete Portfolio Background Detail')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
                    return redirect()->back();
                }
        
                return response()->json(['message' => 'success', 'src' => $src_port_bg_detail_def], 200);
            }
        }
    }
}