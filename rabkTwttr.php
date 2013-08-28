<?php

// =============================================================================
//
//  rabkTwttr: A Twitter API library in PHP
//  Copyright (c) 2013, Robert Aboukhalil
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// =============================================================================

if(session_id() == '') {
  session_start();
}

class rabkTwttr
{
    // =========================================================================
    // ==== Member variables ===================================================
    // =========================================================================
    
    // ---- Constants
    const url_default   = 'https://api.twitter.com/1.1/';
    const url_oauth     = 'https://api.twitter.com/';
    const url_streaming = '';
    const MODE_APP      = 0;
    const MODE_USER     = 1;

    // ---- Configuration
    public static $consumer_key;
    public static $consumer_secret;

    // ---- Variables
    private $URL;
    private $mode;
    private $authenticated;
    private $oauth_callback;

    // =========================================================================
    // ==== Constructor ========================================================
    // =========================================================================
    function __construct($authReq = false, $oauth_callback = '')
    {
        // ---- Initialize variables
        if($oauth_callback == '')
          $oauth_callback = $_SERVER['SERVER_ADDR'] . $_SERVER['REQUEST_URI'];
        
        $this->mode           = $authReq ? rabkTwttr::MODE_USER : rabkTwttr::MODE_APP;
        $this->oauth_callback = $oauth_callback;
        $this->authenticated  = false;

        // ---- Authenticate
        $this->authenticate();
    }

    // =========================================================================
    // ==== Query Twitter ======================================================
    // =========================================================================
    public function query($query, $method, $args)
    {
        // 
        $auth = '';
        $body = '';
        $returnJSON = true;

        if(count($tmp=explode('?', $query)) > 1)
            die('Do not include ?var=value in your URL. Put those in an ' .
                'array in the 3rd argument of query()');

        // ==== Process query based on whether GET or POST =====================
        // ----  GET -----------------------------------------------------------
        if($method == 'GET')
        {
            $query .= '?';
            foreach($args as $key => $value)
            {
                $query .= rawurlencode($key) . '=' . rawurlencode($value) . '&';
                $auth[$key] = $value;
            }
            $query = rtrim($query, '&');
        }
        // ----  POST ----------------------------------------------------------
        else if($method == 'POST')
        {
            foreach($args as $key => $value)
            {
                $body .= rawurlencode($key) . '=' . rawurlencode($value) . '&';
                $auth[$key] = $value;
            }
            $body = rtrim($body, '&');
        }
        else
            die("Error: unsupported method. Use either GET or POST.");

        // ==== Authentication method for Application-only or user mode ========
        if($this->mode == rabkTwttr::MODE_APP)
            $auth = 'Bearer ' . $_SESSION['access_token'];
        else if($this->mode == rabkTwttr::MODE_USER)
            $auth['oauth_token'] = $_SESSION['access_token'];

        // ==== Filter and further process URL from query ======================
        $this->URL = $this->prepareURL($query);

        return rabkTwttr::httpquery($query, $method, $auth, $body, $returnJSON);
    }


    // =========================================================================
    // ==== Authenticate application or user ===================================
    // =========================================================================
    private function authenticate()
    {
        // If already authenticated, no need to do it again
        if($this->is_authenticated() && $this->authenticated)
            return true;

        // ==== Application-only authentication; no need for signed oAuth ======
        if($this->mode == rabkTwttr::MODE_APP)
        {
            // Encode consumer key and consumer secret
            $encodedToken = base64_encode(rawurlencode(rabkTwttr::$consumer_key) . ':' . rawurlencode(rabkTwttr::$consumer_secret));

            // Query to get bearer token
            $auth  = 'Basic ' . $encodedToken;
            $body  = 'grant_type=client_credentials';
            $query = rabkTwttr::httpquery('oauth2/token', 'POST', $auth, $body, $JSON = true);

            // Return the token if valid
            if($query->token_type != 'bearer')
                die('Error: couldn\'t authenticate.');

            $_SESSION['access_token'] = $query->access_token;
        }

        // ==== User authentication: need signed oAuth =========================
        else if($this->mode == rabkTwttr::MODE_USER)
        {
            // ---- If need to redirect to Twitter for user login --------------
            if(!isset($_GET['oauth_token']) && !isset($_GET['oauth_verifier']))
            {
                // Query Twitter to get a request token
                $oauth_params['oauth_callback'] = $this->oauth_callback;
                $query = rabkTwttr::httpquery('oauth/request_token', 'POST', $oauth_params);

                // Parse results
                parse_str($query);
                if(!$oauth_callback_confirmed)
                    die('Error: Query to Twitter failed');

                // If everything good, redirect to Twitter to authenticate
                echo '<script>window.location="https://api.twitter.com/oauth/authenticate?oauth_token=' . $oauth_token . '";</script>';
            }

            // ---- Once redirected from Twitter login page --------------------
            else
            {
                // Query Twitter to get the access token
                $oauth_params['oauth_token']    = $_GET['oauth_token'];
                $oauth_params['oauth_verifier'] = $_GET['oauth_verifier'];

                $body = 'oauth_verifier=' . $oauth_params['oauth_verifier'];
                $query = rabkTwttr::httpquery('oauth/access_token', 'POST', $oauth_params, $body);

                // Save tokens to session variable
                parse_str($query);
                $_SESSION['access_token']        = $oauth_token;
                $_SESSION['access_token_secret'] = $oauth_token_secret;

                $this->authenticated  = true;
                return true;
            }
        }
        else
            die('Error: invalid mode.');
    }
    // ==== Check if user is already authenticated =============================
    private function is_authenticated()
    {
        if($this->mode == rabkTwttr::MODE_APP)
            return isset($_SESSION['access_token']);
        
        if($this->mode == rabkTwttr::MODE_USER)
            return isset($_SESSION['access_token']) && isset($_SESSION['access_token_secret']);
    }


    // =========================================================================
    // ==== Send an HTTP request (does not use cURL) ===========================
    // =========================================================================
    private static function httpquery($URL, $method = 'GET', $authorization = '', $body = '', $returnJSON = false)
    {
        //
        $arrHeader  = array();
        $arrOptions = array();

        // Make sure URL is in correct format
        $URL = rabkTwttr::prepareURL($URL);

        // Create header field
        $arrHeader['Content-type'] = 'application/x-www-form-urlencoded;charset=UTF-8';
        if(strlen($body) > 0)
            $arrHeader['Content-Length'] = strlen($body);
        
        // ==== Create authorization field =====================================
        if(!is_array($authorization))
            $arrHeader['Authorization'] = $authorization;
        else
            $arrHeader['Authorization'] = rabkTwttr::oauth_header($URL, $method, $authorization);

        // ==== Setup header ===================================================
        $header = '';
        foreach($arrHeader as $key => $value)
            $header .= "$key: $value\r\n";

        // Setup HTTP request
        $arrOptions['http']['method']  = $method;
        $arrOptions['http']['content'] = $body;
        $arrOptions['http']['header']  = $header;

        // Send the request and wait for reply
        $context = stream_context_create($arrOptions);
        $result  = file_get_contents($URL, false, $context);

        // Return JSON object if needed
        if($returnJSON)
            return json_decode($result);

        return $result;
    }
    // ==== Create oAuth header ================================================
    private static function oauth_header($URL, $method, $authorization)
    {
        $param = array();
        $DST   = "OAuth ";

        // For the purposes of the signature, no need for anything beyond '?'
        $tmp = explode("?", $URL);
        $URL = $tmp[0];

        // Setup default values
        $param['oauth_timestamp']        = time();
        $param['oauth_consumer_key']     = rabkTwttr::$consumer_key;
        $param['oauth_nonce']            = base64_encode(str_shuffle(MD5(microtime())));
        $param['oauth_version']          = '1.0';
        $param['oauth_signature_method'] = 'HMAC-SHA1';
        $param['oauth_token']            = '';

        // Add (or overwrite) parameters with given values
        foreach($authorization as $key => $value)
            $param[$key] = $value;
        $param = array_filter($param);

        // ---- Sign the HTTP request ------------------------------------------
        $oauth_signature_base = "";

        $param2 = $param;
        // Step 1: Percent-encode keys and values and sort alphabetically by key
        foreach($param2 as $key => $value)
            $param2[ rawurlencode($key) ] = rawurlencode($value);
        ksort($param2);

        // Step 2: Create signature base
        foreach($param2 as $key => $value)
            $oauth_signature_base .= $key . '=' . $value . '&';
        $oauth_signature_base = strtoupper($method) . '&' . rawurlencode($URL) . '&' . rawurlencode(rtrim($oauth_signature_base, '&'));

        // Step 3: Create signing key and sign
        $oauth_signing_key = rawurlencode(rabkTwttr::$consumer_secret) . '&' . @rawurlencode($_SESSION['access_token_secret']);
        $param['oauth_signature'] = base64_encode(hash_hmac('sha1', $oauth_signature_base, $oauth_signing_key, true));

        // ---- Create header --------------------------------------------------
        foreach($param as $key => $value)
            if(preg_match('/oauth_/', $key))
                $DST .= rawurlencode($key) . '="' . rawurlencode($value) . '", ';
        $DST = rtrim($DST, ", ");

        return $DST;
    }

    // =========================================================================
    // ==== Create URL from query (e.g. search/tweets.json) or from URL ========
    // =========================================================================
    private static function prepareURL($query)
    {
        // Is query isn't a URL, prepend default URL
        if(!filter_var($query, FILTER_VALIDATE_URL))
            if(preg_match('/oauth/', $query))
                $query = rabkTwttr::url_oauth . $query;
            else
                $query = rabkTwttr::url_default . $query;

        // Make sure using HTTPS
        if(($scheme = parse_url($query, PHP_URL_SCHEME)) != 'https')
            $query = str_replace($scheme, 'https', $query);

        // Remove excessive '/' in URL
        $query = preg_replace('%([^:])([/]{2,})%', '\\1/', $query);

        return $query;
    }
}

?>
