<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function login()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => env('OAUTH_APP_ID'),
            'clientSecret' => env('OAUTH_APP_PASSWORD'),
            'redirectUri' => env('OAUTH_REDIRECT_URI'),
            'urlAuthorize' => env('OAUTH_AUTHORITY') . env('OAUTH_AUTHORIZE_ENDPOINT'),
            'urlAccessToken' => env('OAUTH_AUTHORITY') . env('OAUTH_TOKEN_ENDPOINT'),
            'urlResourceOwnerDetails' => '',
            'scopes' => env('OAUTH_SCOPES')
        ]);
        if (!isset($_GET['code'])) {
            // Generate the auth URL
            $authorizationUrl = $oauthClient->getAuthorizationUrl([env('REG_EMAIL_ADDR')]);
            // Save client state so we can validate in response
            $_SESSION['oauth2state'] = $oauthClient->getState();
            // Redirect to authorization endpoint
            header('Location: ' . $authorizationUrl);
            exit();
            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
            exit('Invalid state');
        } else {
            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);
                $email = $this->getUserName($accessToken->getValues()['id_token']);
//                $user = DB::select("select rtrim(usr_id) usr_id,usr_name,email_addr,rtrim(mobile_phone) mobile_phone,rtrim(revoked_by) revoked_by,revoked_date,pwd_changed from sys_usr_list where email_addr='$email' ");
//                dd($user);

                // Redirect back to redirect page
                return redirect(route('home'))->withCookie(cookie('email',$email,1000));

            } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                exit($e->getMessage());
            }
        }
    }

    public static function getUserName($id_token)
    {
        $token_parts = explode(".", $id_token);

        // First part is header, which we ignore
        // Second part is JWT, which we want to parse
        error_log("getUserName found id token: " . $token_parts[1]);

        // First, in case it is url-encoded, fix the characters to be
        // valid base64
        $encoded_token = str_replace('-', '+', $token_parts[1]);
        $encoded_token = str_replace('_', '/', $encoded_token);
        error_log("After char replace: " . $encoded_token);

        // Next, add padding if it is needed.
        switch (strlen($encoded_token) % 4) {
            case 0:
                // No pad characters needed.
                error_log("No padding needed.");
                break;
            case 2:
                $encoded_token = $encoded_token . "==";
                error_log("Added 2: " . $encoded_token);
                break;
            case 3:
                $encoded_token = $encoded_token . "=";
                error_log("Added 1: " . $encoded_token);
                break;
            default:
                // Invalid base64 string!
                error_log("Invalid base64 string");
                return null;
        }

        $json_string = base64_decode($encoded_token);
        error_log("Decoded token: " . $json_string);
        $jwt = json_decode($json_string, true);
        error_log("Found user name: " . $jwt['preferred_username']);
        return $jwt['preferred_username'];
    }
}
