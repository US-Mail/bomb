<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class InstallController extends Controller
{

    public function show(Request $request)
    {

        try {
            $settings = DB::select("SELECT * FROM settings")[0];
        } catch (\Illuminate\Database\QueryException $e) {
            $settings = false;
        } catch (\ErrorException $e) {
            $settings = false;
        }

        if (!$settings) {
            return view('install');
        }
        return abort(404);
    }

    public function install(Request $request)
    {


        try {
            $settings = DB::select("SELECT * FROM settings")[0];
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            $settings = false;
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            $settings = false;
        }

        if (isset($settings->did_run_before)) {
            if ($settings->did_run_before) {
                return abort(404);
            }
        }
        return $this->doInstallationTasks($request);
    }

    function doInstallationTasks($request)
    {


        $seed = Artisan::call('optimize:clear');
        $seed = Artisan::call('migrate --seed');
        $output = Artisan::output();

        $created = DB::table('admins')->insert([
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'role' => 'admin'
        ]);


        return redirect()->route('admin.index');
    }
}
