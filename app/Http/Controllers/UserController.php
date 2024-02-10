<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;


class UserController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $title = 'User';
        $users = User::with('roles')->get();
        $roles = Role::where('id', '!=', '1')->get();
        $rolez = Role::get();
        
        if ($users != null) {
            $data = json_decode($users, true);
            $result = [];
            foreach ($data as $item) {
                $user = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'email_verified_at' => $item['email_verified_at'],
                    'is_active' => $item['is_active'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                    'role_id' => $item['roles'][0]['id'],
                    'role_name' => $item['roles'][0]['name']
                ];
                $result[] = $user;
            }
            $data2 = json_decode($roles, true);
            $result2 = [];
            foreach ($data2 as $item2) {
                $user2 = [
                    'id' => $item2['id'],
                    'name' => $item2['name'],
                    'guard_name' => $item2['guard_name'],
                    'created_at' => $item2['created_at'],
                    'updated_at' => $item2['updated_at']
                ];
                $result2[] = $user2;
            }
            $data3 = json_decode($rolez, true);
            $result3 = [];
            foreach ($data3 as $item3) {
                $user3 = [
                    'id' => $item3['id'],
                    'name' => $item3['name'],
                    'guard_name' => $item3['guard_name'],
                    'created_at' => $item3['created_at'],
                    'updated_at' => $item3['updated_at']
                ];
                $result3[] = $user3;
            }
        } else {
            $result = [];
            $result2 = [];
            $result3 = [];
        }

        return view('admin_user', [
            'title' => $title,
            'users' => $result,
            'roles' => $result2,
            'rolez' => $result3
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'create_name'      => 'required|max:50',
            'create_email'     => 'required|email',
            'create_password'  => 'required'
        ]);

        // get params
        $user_id    = Auth::user()->id;
        $module     = 'Admin';
        $scene      = 'User';
        $activity   = 'Create - ' . $request->create_email;
        $ip         = $request->ip();
        // ---
        $name       = $request->create_name;
        $email      = $request->create_email;
        $password   = Hash::make($request->create_password);
        $role       = $request->create_role;
        $status     = $request->create_status;

        // // temp variable
        // $temp = [
        //     'user_id'   => $user_id,
        //     'module'    => $module,
        //     'scene'     => $scene,
        //     'activity'  => $activity,
        //     'ip'        => $ip,
        // // ---
        //     'name'      => $name,
        //     'email'     => $email,
        //     'password'  => $password,
        //     'role'      => $role,
        //     'status'    => $status
        // ];

        // check email user
        $user_check = User::where('email', $email)->first();
        if ($user_check) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // create user
        $user_create = User::create([
            'name'  => $name,
            'email' => $email,
            'password' => $password,
            'is_active' => $status,
        ]);
        $user_create->assignRole($role);

        if (!$user_create) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $request->validate([
            'edit_id'   => 'required',
            'edit_name' => 'required|max:50'
        ]);

        // get user
        $user = User::where('email', $request->edit_email)->first();

        // get params
        $user_id    = Auth::user()->id;
        $module     = 'Admin';
        $scene      = 'User';
        $activity   = 'Edit - ' . $request->edit_email;
        $ip         = $request->ip();
        // ---
        $id         = $request->edit_id;
        $name       = $request->edit_name;
        $email      = $request->edit_email;
        $password   = ($request->edit_password == null) ? $user->password : Hash::make($request->edit_password);
        $role       = $request->edit_role;
        $status     = $request->edit_status;

        // // temp variable
        // $temp = [
        //     'user_id'   => $user_id,
        //     'module'    => $module,
        //     'scene'     => $scene,
        //     'activity'  => $activity,
        //     'ip'        => $ip,
        // // ---
        //     'id'        => $id,
        //     'name'      => $name,
        //     'email'     => $email,
        //     'password'  => $password,
        //     'role'      => $role,
        //     'status'    => $status
        // ];

        // update user
        $user_update = User::find($id);
        if (!$user_update) {
            Alert::error('Failed', 'Update User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        $user_update->name = $name;
        $user_update->email = $email;
        $user_update->password = $password;
        $user_update->is_active = $status;
        $user_update->save();
        $user_update->roles()->detach();
        $user_update->assignRole($role);

        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Update User')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
    }

    public function detail($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json($user);
    }
}






































<?php

namespace Modules\FightOfLegends\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fol\LogActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Auth;
use Response;
use Validator;

class WebLegendController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->input('status');
        
        if ($status == '1') {
            Alert()->success('Legend Created', 'Success')->autoclose(2000);
            return redirect()->route('fol.web.legend.index');
        } else if ($status == '2') {
            Alert()->success('Legend Edited', 'Success')->autoclose(2000);
            return redirect()->route('fol.web.legend.index');
        }

        // get all legend
        $folderPath = '/app/web/backadminv2/storage/app/public/json/web_legend';
        $jsonFiles = glob($folderPath . '/*.json');
        
        if ($jsonFiles == null) {
            $all_legend = '';
        } else {
            foreach ($jsonFiles as $jsonFile) {
                $jsonData = file_get_contents($jsonFile);
                $decodedData = json_decode($jsonData, true);
                $all_legend[] = $decodedData;
            }
            usort($all_legend, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }

        $title  = 'Legend';
        $data   = [
            'title'         => $title,
            'all_legend'    => $all_legend
        ];

        return view('FightOfLegends::web_legend/index', $data);
    }
    
    public function create()
    {
        $title = 'Create';

        // get all legend
        $get_legend = file_get_contents('https://fol-v1-api.mncgames.com/web/legend?title=all');
        $get_legend = str_replace(['<\/', '<\/'], ['</', '</'], $get_legend);
        $get_legend = json_decode($get_legend, true);
        usort($get_legend, function ($a, $b) {
            return $a["id"] - $b["id"];
        });

        // get last id
        $last_id = 0;
        foreach ($get_legend as $item) {
            if ($item['id'] > $last_id) {
                $last_id = $item['id'];
            }
        }

        return view('FightOfLegends::web_legend/create', [
            'title'     => $title,
            'last_id'   => $last_id
        ]);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'legend_name'           => 'required',
            'legend_class'          => 'required',
            'legend_desc'           => 'required',
            'legend_img_card'       => 'required|image|mimes:jpeg,jpg|max:2048',
            'legend_img_detail_bg'  => 'required|image|mimes:jpeg,jpg|max:2048'
        ]);
        
        $auth_name  = Auth::user()->name;
        $auth_pic   = Auth::user()->picture;
        
        $exp_gm_name    = Auth::user()->name;
        $exp_gm_name    = explode(' ', $exp_gm_name);

        if (empty($exp_gm_name[1])) {
            $gm_name = $exp_gm_name[0];
        } else {
            $pre_gm_name = $exp_gm_name[0];
            $pos_gm_name = $exp_gm_name[1];
            $gm_name = $pre_gm_name . ' ' . $pos_gm_name;
        }

        // get field
        $id                 = $request->input('legend_id');
        $classes            = $request->input('legend_classes_1') . '-' . $request->input('legend_classes_2') . '-' . $request->input('legend_classes_3');
        $name               = strtolower($request->input('legend_name'));
        $class              = $request->input('legend_class');
        $desc               = ($request->input('legend_desc') == null) ? '-' : $request->input('legend_desc');
        $abil_title         = ($request->input('legend_abil_title') == null) ? '-' : $request->input('legend_abil_title');
        $abil_desc          = ($request->input('legend_abil_desc') == null) ? '-' : $request->input('legend_abil_desc');
        $abil_link          = ($request->input('legend_abil_link') == null) ? '-' : $request->input('legend_abil_link');
        $skin_title         = ($request->input('legend_skin_title') == null) ? '-' : $request->input('legend_skin_title');
        $detail_guide_link  = ($request->input('legend_detail_guide_link') == null) ? '-' : $request->input('legend_detail_guide_link');
        $status             = $request->input('legend_status');
        $created_at         = date('Y-m-d H:i:s');

        // // temp variable
        // $temp = $id . ' ~ ' . $classes . ' ~ ' . $name . ' ~ ' . $class . ' ~ ' . $desc . ' ~ ' . $abil_title . ' ~ ' . $abil_desc . ' ~ ' . $abil_link . ' ~ ' . $skin_title . ' ~ ' . $detail_guide_link . ' ~ ' . $status . ' ~ ' . $created_at;

        if ($request->hasFile('legend_img_highlight')) {
            $filename = isset($_FILES['legend_img_highlight']['name']) ? 'hm_lgn_' . $name . '.' . $request->file('legend_img_highlight')->extension() : '';
            $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;

            /** CURL photo */
            $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
            $curl = curl_init();
            
            // Set URL tujuan
            curl_setopt($curl, CURLOPT_URL, $destinationUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            
            $file = $request->file('legend_img_highlight');
            $filePath = $file->path();

            $data = array(
                'legend_img_highlight' => new \CURLFile($filePath, $file->getClientMimeType(), $filename)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($curl);
            /** CURL photo break */
        }
        if ($request->hasFile('legend_img_slider')) {
            $filename = isset($_FILES['legend_img_slider']['name']) ? 'hm_lgns_' . $name . '.' . $request->file('legend_img_slider')->extension() : '';
            $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;

            /** CURL photo */
            $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
            $curl = curl_init();
            
            // Set URL tujuan
            curl_setopt($curl, CURLOPT_URL, $destinationUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            
            $file = $request->file('legend_img_slider');
            $filePath = $file->path();

            $data = array(
                'legend_img_slider' => new \CURLFile($filePath, $file->getClientMimeType(), $filename)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($curl);
            /** CURL photo break */
        }
        if ($request->hasFile('legend_img_top')) {
            $filename = isset($_FILES['legend_img_top']['name']) ? 'lgnt_' . $name . '.' . $request->file('legend_img_top')->extension() : '';
            $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;

            /** CURL photo */
            $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
            $curl = curl_init();
            
            // Set URL tujuan
            curl_setopt($curl, CURLOPT_URL, $destinationUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            
            $file = $request->file('legend_img_top');
            $filePath = $file->path();

            $data = array(
                'legend_img_top' => new \CURLFile($filePath, $file->getClientMimeType(), $filename)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($curl);
            /** CURL photo break */
        }
        if ($request->hasFile('legend_img_card')) {
            $filename = isset($_FILES['legend_img_card']['name']) ? 'lgn_' . $name . '.' . $request->file('legend_img_card')->extension() : '';
            $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;

            /** CURL photo */
            $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
            $curl = curl_init();
            
            // Set URL tujuan
            curl_setopt($curl, CURLOPT_URL, $destinationUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            
            $file = $request->file('legend_img_card');
            $filePath = $file->path();

            $data = array(
                'legend_img_card' => new \CURLFile($filePath, $file->getClientMimeType(), $filename)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($curl);
            /** CURL photo break */
        }
        if ($request->hasFile('legend_img_detail_bg')) {
            $filename = isset($_FILES['legend_img_detail_bg']['name']) ? 'lgndt_bg_top_' . $name . '.' . $request->file('legend_img_detail_bg')->extension() : '';
            $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;

            /** CURL photo */
            $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
            $curl = curl_init();
            
            // Set URL tujuan
            curl_setopt($curl, CURLOPT_URL, $destinationUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            
            $file = $request->file('legend_img_detail_bg');
            $filePath = $file->path();

            $data = array(
                'legend_img_detail_bg' => new \CURLFile($filePath, $file->getClientMimeType(), $filename)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($curl);
            /** CURL photo break */
        }
        if ($request->hasFile('legend_img_abil')) {
            foreach ($_FILES['legend_img_abil']['name'] as $index => $file) {
                $filename = !empty($file) ? 'lgndt_ind_' . $name . '_' . ($index + 1) . '.' . pathinfo($file, PATHINFO_EXTENSION) : '';
                $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;
                
                /** CURL photo */
                $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
                $curl = curl_init();
                
                // Set URL tujuan
                curl_setopt($curl, CURLOPT_URL, $destinationUrl);
                curl_setopt($curl, CURLOPT_POST, true);
                
                // $file = $request->file('legend_img_abil');
                $filePath = $_FILES["legend_img_abil"]["tmp_name"][$index];
    
                $data = array(
                    'legend_img_abil' => new \CURLFile($filePath, $_FILES["legend_img_abil"]["type"][$index], $filename)
                );
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    
                $result = curl_exec($curl);
                /** CURL photo break */   
            }
        }
        if ($request->hasFile('legend_img_skin')) {
            foreach ($_FILES['legend_img_skin']['name'] as $index => $file) {
                $filename = !empty($file) ? 'lgndt_sk_' . $name . '_' . ($index + 1) . '.' . pathinfo($file, PATHINFO_EXTENSION) : '';
                $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;
                
                /** CURL photo */
                $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
                $curl = curl_init();
                
                // Set URL tujuan
                curl_setopt($curl, CURLOPT_URL, $destinationUrl);
                curl_setopt($curl, CURLOPT_POST, true);
                
                // $file = $request->file('legend_img_skin');
                $filePath = $_FILES["legend_img_skin"]["tmp_name"][$index];
    
                $data = array(
                    'legend_img_skin' => new \CURLFile($filePath, $_FILES["legend_img_skin"]["type"][$index], $filename)
                );
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    
                $result = curl_exec($curl);
                /** CURL photo break */   
            }
        }
        
        $jsonData = [
            'id'                => (int)$id,
            'classes'           => $classes,
            'name'              => $name,
            'class'             => $class,
            'description'       => $desc,
            'abil_title'        => $abil_title,
            'abil_desc'         => $abil_desc,
            'abil_link'         => $abil_link,
            'skin_title'        => $skin_title,
            'detail_guide_link' => $detail_guide_link,
            'status'            => $status,
            'created_at'        => $created_at,
            'updated_at'        => date('Y-m-d H:i:s')
        ];
        
        $saveJson = Storage::disk('public')->put('/json/web_legend/' . $name . '-' . $id .'.json', json_encode($jsonData));
        
        if ($saveJson) {
            LogActivity::addToLog('Create Web Legend', $auth_name, $auth_pic, 'Create Web Legend', 'Web Legend - ' . $id);
            Alert()->success('Legend Created', 'Success')->autoclose(2000);
            return redirect()->route('fol.web.legend.index');
        } else {
            Alert()->error('Legend Not Created', 'Failed')->autoclose(2000);
            return redirect()->route('fol.web.legend.create');
        }
    }

    public function edit(Request $request, $id)
    {
        $title = 'Edit';

        // get all news
        $folderPath = '/app/web/backadminv2/storage/app/public/json/web_legend';
        $jsonFiles = glob($folderPath . '/*.json');
        
        if ($jsonFiles != null) {
            foreach ($jsonFiles as $jsonFile) {
                $jsonData = file_get_contents($jsonFile);
                $decodedData = json_decode($jsonData, true);
                $all_legend[] = $decodedData;
            }
        }
        
        // get filename
        $foundElement = null;
        foreach ($all_legend as $element) {
            if ($element['id'] == $id) {
                $foundElement = $element;
                break;
            }
        }
        if ($foundElement !== null) {
            $filename = $foundElement['name'] . '-' . $foundElement['id'];
        }

        // get legend by filename
        $folderPath = '/app/web/backadminv2/storage/app/public/json/web_legend';
        $jsonData = file_get_contents($folderPath . '/' . $filename . '.json');
        $legend = json_decode($jsonData, true);
        
        return view('FightOfLegends::web_legend/edit', [
            'title'     => $title,
            'legend'    => $legend
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'legend_img_card'       => 'image|mimes:jpeg,jpg|max:2048',
            'legend_img_detail_bg'  => 'image|mimes:jpeg,jpg|max:2048'
        ]);

        $auth_name = Auth::user()->name;
        $auth_pic = Auth::user()->picture;

        $exp_gm_name = Auth::user()->name;
        $exp_gm_name = explode(' ', $exp_gm_name);

        $pre_gm_name = $exp_gm_name[0];
        $pos_gm_name = $exp_gm_name[1];
        $gm_name = $pre_gm_name . ' ' . $pos_gm_name;

        // get field
        $id                 = $request->input('legend_id');
        $classes            = $request->input('legend_classes_1') . '-' . $request->input('legend_classes_2') . '-' . $request->input('legend_classes_3');
        $name               = strtolower($request->input('legend_name'));
        $class              = $request->input('legend_class');
        $desc               = ($request->input('legend_desc') == null) ? '-' : $request->input('legend_desc');
        $abil_title         = ($request->input('legend_abil_title') == null) ? '-' : $request->input('legend_abil_title');
        $abil_desc          = ($request->input('legend_abil_desc') == null) ? '-' : $request->input('legend_abil_desc');
        $abil_link          = ($request->input('legend_abil_link') == null) ? '-' : $request->input('legend_abil_link');
        $skin_title         = ($request->input('legend_skin_title') == null) ? '-' : $request->input('legend_skin_title');
        $detail_guide_link  = ($request->input('legend_detail_guide_link') == null) ? '-' : $request->input('legend_detail_guide_link');
        $status             = $request->input('legend_status');
        $created_at         = date('Y-m-d H:i:s');

        // // temp variable
        // $temp = $id . ' ~ ' . $classes . ' ~ ' . $name . ' ~ ' . $class . ' ~ ' . $desc . ' ~ ' . $abil_title . ' ~ ' . $abil_desc . ' ~ ' . $abil_link . ' ~ ' . $skin_title . ' ~ ' . $detail_guide_link . ' ~ ' . $status . ' ~ ' . $created_at;

        // update image if exist
        if ($request->hasFile('legend_img_highlight')) {
            $filename = isset($_FILES['legend_img_highlight']['name']) ? 'hm_lgn_' . $name . '.' . $request->file('legend_img_highlight')->extension() : '';
            $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;

            /** CURL photo */
            $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
            $curl = curl_init();
            
            // Set URL tujuan
            curl_setopt($curl, CURLOPT_URL, $destinationUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            
            $file = $request->file('legend_img_highlight');
            $filePath = $file->path();

            $data = array(
                'legend_img_highlight' => new \CURLFile($filePath, $file->getClientMimeType(), $filename)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($curl);
            /** CURL photo break */
        }
        if ($request->hasFile('legend_img_slider')) {
            $filename = isset($_FILES['legend_img_slider']['name']) ? 'hm_lgns_' . $name . '.' . $request->file('legend_img_slider')->extension() : '';
            $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;

            /** CURL photo */
            $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
            $curl = curl_init();
            
            // Set URL tujuan
            curl_setopt($curl, CURLOPT_URL, $destinationUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            
            $file = $request->file('legend_img_slider');
            $filePath = $file->path();

            $data = array(
                'legend_img_slider' => new \CURLFile($filePath, $file->getClientMimeType(), $filename)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($curl);
            /** CURL photo break */
        }
        if ($request->hasFile('legend_img_top')) {
            $filename = isset($_FILES['legend_img_top']['name']) ? 'lgnt_' . $name . '.' . $request->file('legend_img_top')->extension() : '';
            $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;

            /** CURL photo */
            $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
            $curl = curl_init();
            
            // Set URL tujuan
            curl_setopt($curl, CURLOPT_URL, $destinationUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            
            $file = $request->file('legend_img_top');
            $filePath = $file->path();

            $data = array(
                'legend_img_top' => new \CURLFile($filePath, $file->getClientMimeType(), $filename)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($curl);
            /** CURL photo break */
        }
        if ($request->hasFile('legend_img_card')) {
            $filename = isset($_FILES['legend_img_card']['name']) ? 'lgn_' . $name . '.' . $request->file('legend_img_card')->extension() : '';
            $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;

            /** CURL photo */
            $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
            $curl = curl_init();
            
            // Set URL tujuan
            curl_setopt($curl, CURLOPT_URL, $destinationUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            
            $file = $request->file('legend_img_card');
            $filePath = $file->path();

            $data = array(
                'legend_img_card' => new \CURLFile($filePath, $file->getClientMimeType(), $filename)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($curl);
            /** CURL photo break */
        }
        if ($request->hasFile('legend_img_detail_bg')) {
            $filename = isset($_FILES['legend_img_detail_bg']['name']) ? 'lgndt_bg_top_' . $name . '.' . $request->file('legend_img_detail_bg')->extension() : '';
            $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;

            /** CURL photo */
            $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
            $curl = curl_init();
            
            // Set URL tujuan
            curl_setopt($curl, CURLOPT_URL, $destinationUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            
            $file = $request->file('legend_img_detail_bg');
            $filePath = $file->path();

            $data = array(
                'legend_img_detail_bg' => new \CURLFile($filePath, $file->getClientMimeType(), $filename)
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($curl);
            /** CURL photo break */
        }
        if ($request->hasFile('legend_img_abil')) {
            foreach ($_FILES['legend_img_abil']['name'] as $index => $file) {
                $filename = !empty($file) ? 'lgndt_ind_' . $name . '_' . ($index + 1) . '.' . pathinfo($file, PATHINFO_EXTENSION) : '';
                $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;
                
                /** CURL photo */
                $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
                $curl = curl_init();
                
                // Set URL tujuan
                curl_setopt($curl, CURLOPT_URL, $destinationUrl);
                curl_setopt($curl, CURLOPT_POST, true);
                
                // $file = $request->file('legend_img_abil');
                $filePath = $_FILES["legend_img_abil"]["tmp_name"][$index];
    
                $data = array(
                    'legend_img_abil' => new \CURLFile($filePath, $_FILES["legend_img_abil"]["type"][$index], $filename)
                );
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    
                $result = curl_exec($curl);
                /** CURL photo break */   
            }
        }
        if ($request->hasFile('legend_img_skin')) {
            foreach ($_FILES['legend_img_skin']['name'] as $index => $file) {
                $filename = !empty($file) ? 'lgndt_sk_' . $name . '_' . ($index + 1) . '.' . pathinfo($file, PATHINFO_EXTENSION) : '';
                $fileUrl = 'https://static-live.mncgames.com/fol/images/web/' . $filename;
                
                /** CURL photo */
                $destinationUrl = 'https://www-live.mncgames.com/fol/data/player_attachment/upload_2.php';
                $curl = curl_init();
                
                // Set URL tujuan
                curl_setopt($curl, CURLOPT_URL, $destinationUrl);
                curl_setopt($curl, CURLOPT_POST, true);
                
                // $file = $request->file('legend_img_skin');
                $filePath = $_FILES["legend_img_skin"]["tmp_name"][$index];
    
                $data = array(
                    'legend_img_skin' => new \CURLFile($filePath, $_FILES["legend_img_skin"]["type"][$index], $filename)
                );
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    
                $result = curl_exec($curl);
                /** CURL photo break */   
            }
        }

        $jsonData = [
            'id'                => (int)$id,
            'classes'           => $classes,
            'name'              => $name,
            'class'             => $class,
            'description'       => $desc,
            'abil_title'        => $abil_title,
            'abil_desc'         => $abil_desc,
            'abil_link'         => $abil_link,
            'skin_title'        => $skin_title,
            'detail_guide_link' => $detail_guide_link,
            'status'            => $status,
            'created_at'        => $created_at,
            'updated_at'        => date('Y-m-d H:i:s')
        ];
        
        $saveJson = Storage::disk('public')->put('/json/web_legend/' . $name . '-' . $id . '.json', json_encode($jsonData));
        
        if ($saveJson) {
            LogActivity::addToLog('Edit Web Legend', $auth_name, $auth_pic, 'Edit Web Legend', 'Web Legend - ' . $id);
            Alert()->success('Legend Saved', 'Success')->autoclose(2000);
            return redirect()->route('fol.web.legend.index');
        } else {
            Alert()->error('Legend Saved', 'Failed')->autoclose(2000);
            return redirect()->route('fol.web.legend.edit', [$legend['id']]);
        }
    }

    public function detail($id)
    {
        $title = 'Legend Detail';

        // get news by filename
        $folderPath = '/app/web/backadminv2/storage/app/public/json/web_legend';
        $jsonData = file_get_contents($folderPath . '/' . $id . '.json');
        $legend_detail = json_decode($jsonData, true);

        $data = array(
            'id'            => $legend_detail['id'],
            'name'          => $legend_detail['name'],
            'class'         => $legend_detail['class'],
            'description'   => $legend_detail['description'],
            'status'        => $legend_detail['status'],
            'created_at'    => $legend_detail['created_at']
        );

        return json_encode($data);
    }
}
