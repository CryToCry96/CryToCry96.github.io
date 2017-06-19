<?php

/**
 * Code based by Nguyen Huu Dat - https://www.facebook.com/dl2811
 * Code được chia sẻ miễn phí tại J2TEAM Community - https://www.facebook.com/groups/j2team.community
 * Website: https://trolyfacebook.com
 * Converted to Chatfuel API by Le Hoang - https://www.facebook.com/lehoangnb
 * 
 */

function gettoken()
{
	$headers = array();
	$headers[] = 'Content-Type: application/x-www-form-urlencoded';
	$headers[] = 'Host: graph.nhaccuatui.com';
	$headers[] = 'Connection: Keep-Alive';
	
	
	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, "https://graph.nhaccuatui.com/v1/commons/token");
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($c, CURLOPT_POST, 1);
	curl_setopt($c, CURLOPT_POSTFIELDS, "deviceinfo=%7B%22DeviceID%22%3A%22dd03852ada21ec149103d02f76eb0a04%22%2C%22DeviceName%22%3A%22TrolyFaceBook.Com%22%2C%22OsName%22%3A%22SmartTV%22%2C%22OsVersion%22%3A%228.0%22%2C%22AppName%22%3A%22NCTTablet%22%2C%22AppVersion%22%3A%221.3.0%22%2C%22UserName%22%3A%220%22%2C%22QualityPlay%22%3A%22128%22%2C%22QualityDownload%22%3A%22128%22%2C%22QualityCloud%22%3A%22128%22%2C%22Network%22%3A%22WIFI%22%2C%22Provider%22%3A%22BeDieuApp%22%7D%0A&md5=488c994e95caa50344d217e9926caf76&timestamp=1497863709521");


	$page = curl_exec($c);
	curl_close($c);
	
	$infotoken = json_decode($page);
	$token = $infotoken->data->accessToken;
	return $token;
}


function getlink($idbaihat,$token)
{
	$linklist = 'https://graph.nhaccuatui.com/v1/songs/'.$idbaihat.'?access_token='.$token;
	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, $linklist);
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

	$page = curl_exec($c);
	curl_close($c);
	
	$data = json_decode($page);
	return $data;
}

if (isset($_GET['url'])) {
    $url      = $_GET['url'];
    $temp     = explode(".", $url);
    $idbaihat = trim($temp[3]);
    if ($idbaihat != "") {
        $token = gettoken();
        if ($token != "") {
            $data = getlink($idbaihat, $token);
            
            //  $linkplay     = $data->data->{7};
            $link128      = $data->data->{11};
            $link320      = $data->data->{12};
            $linklossless = $data->data->{19};
            //  $thumbnail    = $data->data->{8};
            $tenbaihat    = $data->data->{2};
            $casy         = $data->data->{3};
            $luotnghe     = $data->data->{5};
            $album        = $data->data->{14};
        }
        if ($tenbaihat != "") {
            $tenfile = "$tenbaihat - $casy";
        }
        
        
        $result = array(
            "messages" => array(
                array(
                    "text" => "Tên bài hát:  $tenfile \nLượt nghe: $luotnghe"
                ),
                
                array(
                    "attachment" => array(
                        "type" => "template",
                        "payload" => array(
                            "template_type" => "button",
                            "text" => "Bấm nút bên dưới để tải về!",
                            "buttons" => array(
                                array(
                                    "type" => "web_url",
                                    "url" => $link128,
                                    "title" => "128Kbs"
                                ),
                                array(
                                    "type" => "web_url",
                                    "url" => $link320,
                                    "title" => "320Kbs"
                                ),
                                array(
                                    "type" => "web_url",
                                    "url" => $linklossless,
                                    "title" => "LossLess"
                                )
                            )
                        )
                    )
                )
            )
        );
        echo json_encode($result);
        header("Status: 200");
    }
}
?>