<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Service;
class NotificationsController extends Controller
{
    //
    
    public $type = "service_account";
public $project_id = "kassol-app";
public $private_key_id = "3fe5376e77ea523573724054a9fc9619f60cb88e";
public $private_key = "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDUB1o/u/DL2Spt\nH44E7gBRkdjOy6xuxHVF9sGn820qAWH342j3/HwEVAbB1VXinQDACqUykppP3kh0\ncp/0AWTeINIeEPA81dat/YXCnQffzWZagAOMP1pjWRe8eJhIP3jNdXU8egYQNtEv\nk2XESjw1IfI2YufM7UFxv1FKfgEHM94Pt5o4qdAaBj36+8vjyMZZ8325uS42FgVv\nolxXuTtgdYT9UVbxVBs78gH/UmWGTO8BgO56WVRlLTVzS/Me7xnUWEXzIPwPAFn5\nknp8MUzbJmCSNiFVbWz9bwl0E8yDb/D0EWgovndSMTJk0eenZxazyQR739WU40xf\ny8Bs9HfBAgMBAAECggEAEkn0Ee1PLvYzf6gqIui4pFCVR/kvjXpqtzc6qxcHQolN\n/wjhpApmVCU/U/bn6p0qVGZWq48TunHtBAZjH7QDFFn2ZduGLWkwlF966zeKV1ar\nToEeTTmlvU4RCxPH5ut6qxZHn4jS/xELaDQpog3ZNagQ8XoEjpukeoBLLcbCpfE+\nMAgk50JYTUBz+4qdn59l6Y9eFz2j8MXHGiofq4QpXxZ21XzhviWWxBGe1aPW3eAK\nCF3NRQ5tiPK8qsM456hdsdIuo5UP6K3VynrfMduHGsd8qDUCoLZLZP03VHi7ozt5\nvgXGXwOeLmuoVsCiFLgxKdGM4qKSkIW80GOmJIwOwQKBgQDvjMZTWai9ur/PFvOS\nNBkDeoYzpw+/ezbqLDIzJSG+SsMt3PSVRgGThIdKIuRX49dGQm/mB4VK4zv9XlQZ\n3ERo3oOa+MYjFPSl2hLwKz+PBnjMiXAUoHs0E8N2/I4CNqksOR11T5hOQesSNMDo\nqFf7i36H/RolykW2m8Ni40ZnOQKBgQDilsM5ZyrBJd0tuZKFNkZP2ViQdu/AxNjc\n3MPm0zpLtkmB7G65eKH3Fugp+B09muaySIQe03tLsr93r4iBl78NyZeaGNh+PGad\nsT4pmSRq/UXDge2L1hlMjc2czmCyQTk04WCGn/+/0WEgfHbxE+DIvKjrni4EEFeD\nSJAbl8zMyQKBgDRE51Fwkt0zTn8FZwhTzdFwfq/umAUUAZt/IUT/qSk4bvYm7EdW\nCyoBFPQcQO7cjDCMdpYetfrtMj3Kw1cRNOwdAWJjPfiRrgAyYUd+aFPw+ZLHumkF\ny1xFo7TmzqW0/5ziqYbbY0RQJdbHJACgGvKMMYPCul3ckPGTc0K0jLwhAoGBAJrK\nyZVWcDPAzSdmhbzxRflYjpMqXUFAeLHxMvdmR4Xfgrn6Le4Qhf0JrhK1yCwEvSMA\nPRnd+XXOJ9T4mnWFl+QwWpvP613Zn3SM192NG/7nBRi5kvEbku7kVJmRtycvPYht\niwaNGScOP570yECLBt27XF6SEfd+vnoWQTRIcIVZAoGBAK8gwAyQGJXXwpIZFHPn\nzlVII9ic69W41Ukx96Y76pKeA/4+3/RwecB2dG5J9UnT4At+1NfUcyYIEJn2tdmo\noe+sJZuDNzcE7AC4PEU2PeN4rWROH6wC1B5gs1nupYwG9VsmkPeGBlxAhPuiLWll\nHyHVIGajgaMfCmNKq+FP1n31\n-----END PRIVATE KEY-----\n";
public $client_email = "firebase-adminsdk-c585f@kassol-app.iam.gserviceaccount.com";
public $client_id = "104880607320193342622";
public $auth_uri = "https://accounts.google.com/o/oauth2/auth";
public $token_uri = "https://oauth2.googleapis.com/token";
public $auth_provider_x509_cert_url = "https://www.googleapis.com/oauth2/v1/certs";
public $client_x509_cert_url = "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-c585f%40kassol-app.iam.gserviceaccount.com";
public $universe_domain = "googleapis.com";

    
    public function index(){
        $notes=Notification::with('user')->paginate(10);
        return view('notifications.index',compact('notes'));
    }
    public function create_notification(){
        $users=User::doesntHave('service')->get();
        $services=Service::get();
        // return $services;
        return view('notifications.create',compact('users','services'));
    }
    public function store_notification(Request $request){
        $data=Validator::make($request->all(),[
            'title'=>'required',
            'description'=>'required',
            'type'=>'required',
            'user_id'=>'nullable'
        ])->validate();
        $store_data=[
                'user_id'=>$request->user_id??$request->service??null,
                'content'=>$request->description,
                'title'=>$request->title,
                'type'=>$request->type,
            ];
            // return $store_data;
        $new=Notification::create($store_data);
        
        
        if ($request->type == 'private') {
        // Retrieve OAuth2 access token from Google
        $tokenUrl   = "https://oauth2.googleapis.com/token";
        $privateKey = $this->private_key;
        $jwt        = $this->generateJwt($privateKey, $this->client_email);

        $tokenRequestData = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenRequestData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if ($response === false) {
            // Optionally log or handle the curl error here
        }
        $responseData = json_decode($response, true);

        if (isset($responseData['access_token'])) {
            $accessToken = $responseData['access_token'];
        } else {
            return response()->json(['error' => 'Failed to retrieve access token.']);
        }
        curl_close($ch);

        // Build FCM Notification payload
        $notification = [
            'title' => $request->title,
            'body'  => $request->description,
        ];
$user =User::where('id',$request->user_id)->first();
// return $user->fcm_token;
        $messagePayload = [
            'message' => [
                'token'        => $user->fcm_token,
                'notification' => $notification,
            ]
        ];

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        // Send the FCM notification request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/kassol-app/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messagePayload));

        $result = curl_exec($ch);
        curl_close($ch);
        return redirect()->route('get_notifications');
// return $result;
        // Optionally, you can handle/log $result here

        $new_not['user_id'] = $request->user_id;
    }
    
    
        else if($request->type=='public')
         {
         $tokenUrl = "https://oauth2.googleapis.com/token";

        $privateKey=$this->private_key;
                $jwt = $this->generateJwt($privateKey, $this->client_email);


        $tokenRequestData = array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        );

        $accessTokenTwo = null;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenRequestData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if ($response === false) {
            // return response()->json(['error' => curl_error($ch)], curl_errno($ch));
        }

         $responseData = json_decode($response, true);

        if (isset($responseData['access_token'])) {
            $accessToken = $responseData['access_token'];
            $accessTokenTwo = $accessToken;
        } else {
            return response()->json(['error' => 'Failed to retrieve access token.']);
        }

        curl_close($ch);

        // Send FCM Notification
        $msg = array(
            'title' => $request->title,
            'body' => $request->content,
        );

        $topic = 'notifications';

        $fields = array(
            'message' => array(
                'token' => '/topics/' . $topic,
                'notification' => $msg,
            ),
        );

        $serverKey = $accessTokenTwo;

        $headers = array(
            'Authorization: Bearer ' . $serverKey,
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/kassol-app/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        // return $result;
    }
     else {
        // Retrieve OAuth2 access token from Google
        $tokenUrl   = "https://oauth2.googleapis.com/token";
        $privateKey = $this->private_key;
        $jwt        = $this->generateJwt($privateKey, $this->client_email);

        $tokenRequestData = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenRequestData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if ($response === false) {
            // Optionally log or handle the curl error here
        }
        $responseData = json_decode($response, true);

        if (isset($responseData['access_token'])) {
            $accessToken = $responseData['access_token'];
        } else {
            return response()->json(['error' => 'Failed to retrieve access token.']);
        }
        curl_close($ch);

        // Build FCM Notification payload
        $notification = [
            'title' => $request->title,
            'body'  => $request->description,
        ];
$service =Service::where('id',$request->service)->first();
// return $user->fcm_token;
        $messagePayload = [
            'message' => [
                'token'        => $service->fcm_token,
                'notification' => $notification,
            ]
        ];

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        // Send the FCM notification request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/kassol-app/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messagePayload));

        $result = curl_exec($ch);
        curl_close($ch);
        return redirect()->route('get_notifications');
// return $result;
        // Optionally, you can handle/log $result here

        $new_not['user_id'] = $request->user_id;
    }
        
        
        
        
        if($new){
            return redirect()->route('get_notifications')->with('success','تم الارسال بنجاح');
        }
        return redirect()->route('get_notifications')->with('error','حدث خطأ ما');
    }
    public function delete(){
        $id=request('id');
        $del=Notification::find($id)->delete();
        if($del){
            return redirect()->route('get_notifications')->with('success','تم المسح بنجاح');
        }
        return redirect()->route('get_notifications')->with('error','حدث خطأ ما');
    }
          public function generateJwt($privateKey, $clientId)
    {
        $now = time();
        $exp = $now + 3600; // Token valid for 1 hour

        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];
        $base64UrlHeader = $this->base64url_encode(json_encode($header));

        $payload = [
            'iss' => $clientId,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $exp,
            'iat' => $now
        ];
        $base64UrlPayload = $this->base64url_encode(json_encode($payload));

        $signature = '';
        openssl_sign("$base64UrlHeader.$base64UrlPayload", $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $base64UrlSignature = $this->base64url_encode($signature);

        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }

    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}