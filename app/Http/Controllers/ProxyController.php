<?php

namespace App\Http\Controllers;

use CURLFile;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProxyController extends Controller
{
  public function forwardRequest(Request $request)
  {
    $this->validate($request, [
      "destination" => "required|string"
    ]);

    $data = $request->all();
    $files = $request->allFiles();
    $headers = $request->header();

    unset($headers["content-type"]);
    unset($headers["content-length"]);
    // dd($headers);

    $destination = $request->get("destination");
    unset($data["destination"]);
    // dd($headers);

    $requestHeaders = [];

    foreach ($headers as $header => $value) {
      $requestHeaders[$header] = $value[0];
    }

    $postFields = [];

    foreach ($data as $key => $value) {
      if (is_string($value)) {
        $postFields[$key] = $value;
      }
    }

    foreach ($files as $key => $file) {
      // dd($file->getRealPath());
      $filename = md5(time()) . "." . $file->getClientOriginalExtension();
      $mime = $file->getMimeType();
      $file->move(storage_path('app/temp/'), $filename);
      $postFields[$key] = new CURLFile(storage_path("app/temp/{$filename}"), $mime);
    }

    // dd($postFields);
    Log::info(json_encode($requestHeaders));

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $destination,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $request->method(),
      CURLOPT_POSTFIELDS => $postFields,
      CURLOPT_HTTPHEADER => array(
        'Authorization: ' . (isset($requestHeaders['authorization']) ? $requestHeaders['authorization'] : "")
      ),
    ));

    $response = curl_exec($curl);
    // dd(curl_error($curl));

    curl_close($curl);

    // dd($response);
    return response($response);
  }

  public function forwarded(Request $request)
  {
    $input = $request->all();
    $files = $request->allFiles();

    foreach ($files as $file) {
      Log::info("saving file");
      $file->move(storage_path("app/images"), md5(time()) . "." . $file->getClientOriginalExtension());
    }
    // dd($input);

    return response()->json($input);
  }

  private function sendFormRequest($method, $url, $headers, $data)
  {
    $client = new Client();
    $options = [
      "timeout" => 4,
      "headers" => $headers,
      "form_params" => $data,
      "verify" => false,
      "allow_redirects" => false
    ];

    // dd($options);

    $response = $client->request($method, $url, $options);

    return $response;
  }

  private function sendMultipartRequest($method, $url, $headers, $data, $files)
  {
    $client = new Client();
    // $headers["Content-Type"] = "multipart/form-data";
    $multipart = [];

    foreach ($data as $key => $value) {
      if (is_string($value)) {
        $multipart[] = [
          "name" => $key,
          "contents" => $value
        ];
      }
    }

    // dd($multipart);

    foreach ($files as $file) {
      // dd($file);
      $multipart[] = [
        "name" => $file["name"],
        "contents" => $file["file"],
        "filename" => $file["filename"],
        "headers" => $file["headers"],
      ];
    }

    // dd($multipart);

    $options = [
      "timeout" => 4,
      "headers" => $headers,
      "multipart" => $multipart,
      "verify" => false,
      "allow_redirects" => false
    ];

    // dd($options);

    $response = $client->request($method, $url, $options);

    return $response;
  }
}
