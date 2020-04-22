<?PHP
class Sync {
	public $appId = "2882303761517489490";
	public $ApiKey = 'Mf5KCPQDptxr81amSe2eliwz';
	public $SecretKey = 'h4RnbaVulUixyLjP5LxhNwai95QGqPrp';
	public $redirect_uri = 'http://mtjo.net/router/index.html';
	
	// HTTP POST请求函数
	function curl($url, $postFields = null) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_FAILONERROR, false );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
				'Content-type: application/x-www-form-urlencoded;charset=UTF-8' 
		) );
		
		if (is_array ( $postFields ) && 0 < count ( $postFields )) {
			/*
			 * foreach ($postFields as $k => $v) {
			 * // $postBodyString .= "$k=" . urlencode($v) . "&";
			 * $postBodyString .= "$k=" . $v . "&";
			 * }
			 */
			$postBodyString = http_build_query ( $postFields );
			
			// die(print_r($postBodyString));
			
			unset ( $k, $v );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt ( $ch, CURLOPT_POST, true );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postBodyString );
		}
		$response = curl_exec ( $ch );
		if (curl_errno ( $ch )) {
			throw new Exception ( curl_error ( $ch ), 0 );
		} else {
			$httpStatusCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
			if (200 !== $httpStatusCode) {
				throw new Exception ( $response, $httpStatusCode );
			}
		}
		curl_close ( $ch );
		return $response;
	}
	public static function refresh_token() {
		$url = 'https://openapi.baidu.com/oauth/2.0/token';
		$postFields = array (
				'grant_type' => 'refresh_token',
				'refresh_token' => $refresh_token,
				'client_id' => $this->ApiKey,
				'client_secret' => $$this->SecretKey 
		);
		
		$response_r = curl ( 'https://openapi.baidu.com/oauth/2.0/token', $postFields );
		return $response_r;
	}
	public function bindaccount() {
		$location = 'https://openapi.baidu.com/oauth/2.0/authorize?response_type=code&' . 'client_id=' . $this->ApiKey . '&redirect_uri=' . $this->redirect_uri . '&scope=basic,netdisk' . '&display=dialog';
		header ( 'Location:' . $location, true, 302 );
	}
	public function getPcsToken($code) {
		$url = 'https://openapi.baidu.com/oauth/2.0/token';
		$postFields = array (
				'grant_type' => 'authorization_code',
				'code' => $code,
				'client_id' => $this->ApiKey,
				'client_secret' => $this->SecretKey,
				'redirect_uri' => $this->redirect_uri,
				'display' => 'dialog' 
		);
		$response_r = $this->curl ( $url, $postFields );
		return $response_r;
	}
	public function getQuota($access_token) {
		$quota_url = "https://pcs.baidu.com/rest/2.0/pcs/quota?method=info&access_token=" . $access_token;
		$response_r = $this->curl ( $quota_url );
		$tmp_data = json_decode ( $response_r, 1 );
		foreach ( $tmp_data as &$v ) {
			$v = sprintf ( "%.2f", $v / (1024 * 1024 * 1024) );
		}
		return json_encode($tmp_data);
	}
}

if ($_REQUEST ['method'] && $_REQUEST ['method'] != "") {
	$method = trim ( $_REQUEST ['method'] );
	$sync = new Sync ();
	// var_dump($sync);exit;
	switch ($method) {
		case "refresh_token" :
			if ($_REQUEST ['$refresh_token'] != '') {
				die ( $sync->refresh_token ( $_REQUEST ['$refresh_token'] ) );
			} else {
				echo "参数错误!";
			}
			break;
		case "bindAccount" :
			if ($_REQUEST ['bindAccount'] == 'mtjo') {
				$sync->bindaccount ();
				exit ();
			} else {
				echo "参数错误!";
			}
			break;
		case "getPcsToken" :
			if ($_REQUEST ['code'] != '') {
				die ( $sync->getPcsToken ( $_REQUEST ['code'] ) );
			} else {
				echo "参数错误!";
			}
			break;
		case "getAppid" :
			$data ["appId"] = $sync->appId;
			die ( json_encode ( $data ) );
			break;
		
		case "getQuota" :
			if ($_REQUEST ['access_token'] != '') {
				die ( $sync->getQuota( $_REQUEST ['access_token'] ) );
			} else {
				echo "参数错误!";
			}
			break;
		default :
			echo "参数错误!";
			break;
	}
} else {
	echo "参数错误!";
}
?>



