<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class ApiController extends Controller
{
    function showApi(Request $request)
    {

        $apis = json_decode(Setting::all()->first()->apis);

        return view('admin.manage_api.index')->with([
            'apis' => $apis
        ]);
    }

    function deleteApi(Request $request)
    {
        $apis = Setting::all()->first()->apis;
        $id = $request->id;
        $json = json_decode($apis);
        $found = false;
        foreach ($json as $api) {
            if ($api->id == $id) {
                $index = array_search($api, $json);
                unset($json[$index]);
                $apis = json_encode($this->formatApi(array_values($json)));
                Setting::all()->first()->update([
                    'apis' => $apis,
                ]);
                $found = true;
                return back()->with("success", "DELETED SUCCESSFULLY!");
            }
        }
        if (!$found) {
            return back()->with("fail", "No API found on this ID.");
        }
    }

    function editApi(Request $request)
    {
        $id = $request->id;
        $json = json_decode(Setting::all()->first()->apis);
        foreach ($json as $api) {
            if ($api->id == $id) {
                return view('admin.manage_api.edit')->with([
                    'api' => $api
                ]);
            }
        }
    }

    function addApi()
    {
        $lastId = $this->getLastId(json_decode(Setting::all()->first()->apis));
        return view('admin.manage_api.add')->with([
            "lastId" => $lastId
        ]);
    }

    function formatApi($apis)
    {

        $lastId = $this->getLastId($apis);

        $newArr = [];

        for ($i = 0; $i <= $lastId; $i++) {
            foreach ($apis as $api) {
                if ($i == $api->id) {
                    array_push($newArr, $api);
                }
            }
        }

        return $newArr;
    }


    function getLastId($apis)
    {
        $lastId = 0;
        foreach ($apis as $api) {
            if ($api->id >= $lastId) {
                $lastId = $api->id;
            }
        }

        return $lastId;
    }


    function insertApi(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'url' => 'required',
        ]);

        $json = json_decode(Setting::all()->first()->apis);
        foreach ($json as $api) {
            if ($api->id == $request->input('id')) {
                return back()->with('fail', 'ID already exists!');
            }
        }

        $obj = new stdClass;


        $obj->{'id'} = $request->input('id');
        $obj->{'name'} = $request->input('name');
        $obj->{'url'} = $request->input('url') ?? '';
        $obj->{'method'} = $request->input('method');
        $obj->{'body'} = $request->input('body') ?? '';
        $obj->{'headers'} = ($request->input('isJson') === 'on')
            ? ["content-type" => "application/json"]
            : ["content-type" => "application/x-www-form-urlencoded"];

        array_push($json, $obj);

        $new_arr = json_encode($this->formatApi(array_values($json)));
        Setting::all()->first()->update([
            "apis" => $new_arr,
        ]);

        return back()->with('success', 'inserted successfully');
    }

    function updateApi(Request $request)
    {
        $json = json_decode(Setting::all()->first()->apis);
        foreach ($json as $api) {
            if ($api->id == $request->input('id')) {

                unset($json[array_search($api, $json)]);

                $obj = new stdClass;


                $obj->{'id'} = $request->input('id');
                $obj->{'name'} = $request->input('name');
                $obj->{'url'} = $request->input('url') ?? '';
                $obj->{'method'} = $request->input('method');
                $obj->{'body'} = $request->input('body') ?? '';
                $obj->{'headers'} = ($request->input('isJson') === 'on')
                    ? ["content-type" => "application/json"]
                    : ["content-type" => "application/x-www-form-urlencoded"];

                array_push($json, $obj);

                $new_arr = json_encode($this->formatApi(array_values($json)));
                Setting::all()->first()->update([
                    'apis' => $new_arr,
                ]);

                return redirect(route('api.index'))->with('success', 'Updated successfully');
            }
        }
    }

    function advancedEdit(Request $request)
    {
        $file = Setting::all()->first()->apis;
        $formatted = json_encode(json_decode($file), JSON_PRETTY_PRINT);
        return view('admin.manage_api.advanced', [
            'json' => $formatted
        ]);
    }

    function saveRawJson(Request $request)
    {
        $inp = $request->json;
        try {
            $minified = json_encode(json_decode($inp));
        } catch (\Exception $e) {
            return Redirect::route('api.index')->with("fail", "Invalid JSON");
        }

        $saved = Setting::all()->first()->update([
            'apis'=>$minified,
        ]);
        if ($saved) {
            return Redirect::route('api.index')->with("success", "JSON Config Saved!");
        } else {
            return Redirect::route('api.index')->with('fail', 'Oops! Something went wrong...');
        }
    }
}
