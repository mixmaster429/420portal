<?php

/**
 * Carbon helper.
 *
 * @param $time
 * @param $tz
 *
 * @return Carbon\Carbon
 */
use Illuminate\Support\Facades\File;

function is_Mobile()
{
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    if (isset($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    }
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = [
            'nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'zte',
            'mobile',
        ];
        if (preg_match('/('.implode('|', $clientkeywords).')/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if ((false !== strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml')) && (false === strpos($_SERVER['HTTP_ACCEPT'], 'text/html') || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }

    return false;
}

function convertNameToSlug($str) {
    $str = strtolower(trim(preg_replace('/[\s-]+/', '-', preg_replace('/[^A-Za-z0-9-]+/', '-', preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), '-'));
    $str = str_replace("---", '-', $str);
    return str_replace('#', '', $str);
}

function get_filename($str) {
    $str = strtolower(trim(preg_replace('/[\s-]+/', '_', preg_replace('/[^A-Za-z0-9-]+/', '_', preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), '_'));
    return str_replace('#', '', $str);
}

function upload_file($file, $dir)
{
    $file_name = time();
    $file_name .= rand();
    $file_name = sha1($file_name);
    $file->move(public_path().'/uploads/'.$dir, $file_name.'.'.$file->getClientOriginalExtension());
    $local_url = $file_name.'.'.$file->getClientOriginalExtension();

    return 'uploads/'.$dir.'/'.$local_url;
}

function remove_file($file)
{
    if (File::delete($file)) {
        return true;
    }

    return false;
}

function sendResponse($result, $message) {
    $response = [
        'success' => true,
        'data'    => $result,
        'message' => $message,
    ];
    return response()->json($response, 200);
}

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
function sendError($error, $errorMessages = [], $code = 400) {
    $response = [
        'success' => false,
        'message' => $error,
    ];
    if(!empty($errorMessages)){
        $response['data'] = $errorMessages;
    }
    return response()->json($response);
}

function site_url($url) {
    $client_url = 'https://www.420portal.com';
    if(substr($url, 0, 1) != '/') {
        $client_url = 'https://www.420portal.com/';
    }
    return $client_url.$url;
}
