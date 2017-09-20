<?php

namespace Boolw\PhpSms;

use Toplan\PhpSms\Agent;
use Toplan\PhpSms\TemplateSms;
use Toplan\PhpSms\VoiceCode;
use Toplan\PhpSms\TemplateVoice;

/**
 * Class AlidayuSmsAgent
 *
 * @property string $sendUrl
 * @property string $appKey
 * @property string $secretKey
 * @property string $smsFreeSignName
 * @property string $calledShowNum
 */
class AlidayuSmsAgent extends Agent implements TemplateSms, VoiceCode, TemplateVoice
{
    /**
     * Template SMS send process.
     *
     * @param string|array $to
     * @param int|string   $tempId
     * @param array        $tempData
     */
    public function sendTemplateSms($to, $tempId, array $tempData)
    {
        $params = [
            'Action'            => "SendSms",
            'PhoneNumbers'            => $to,
            'SignName' => $this->smsFreeSignName,
            'TemplateCode'  => $tempId,
            'TemplateParam'          => $this->getTempDataString($tempData),
//          'OutId'           => rand(1000000,9999999),
        ];
        $this->request($params);
    }

    /**
     * Template voice send process.
     *
     * @param string|array $to
     * @param int|string   $tempId
     * @param array        $tempData
     */
    public function sendTemplateVoice($to, $tempId, array $tempData)
    {
        $params = [
            'called_num'        => $to,
            'called_show_num'   => $this->calledShowNum,
            'method'            => 'alibaba.aliqin.fc.tts.num.singlecall',
            'tts_code'          => $tempId,
            'tts_param'         => $this->getTempDataString($tempData),
        ];
        $this->request($params);
    }

    /**
     * Voice code send process.
     *
     * @param string|array $to
     * @param int|string   $code
     */
    public function sendVoiceCode($to, $code)
    {
        $params = [
            'called_num'        => $to,
            'called_show_num'   => $this->calledShowNum,
            'method'            => 'alibaba.aliqin.fc.voice.num.singlecall',
            'voice_code'        => $code,
        ];
        $this->request($params);
    }

    protected function request(array $params)
    {
        $params = $this->createParams($params);
        $result = $this->curlPost($this->sendUrl, [], [
            CURLOPT_POSTFIELDS => http_build_query($params),
        ]);
        $this->setResult($result, $this->genResponseName(isset($params['Action']) ? $params['Action'] : $params['method']));
    }

    protected function createParams(array $params)
    {
    	$timezone = date_default_timezone_get();
    	date_default_timezone_set("GMT");
        $params = array_merge([
        	'RegionId'=>"cn-hangzhou",
            'AccessKeyId'            => $this->appKey,
        	'Format'=>"JSON",
        	'SignatureMethod'=>"HMAC-SHA1",
        	'SignatureVersion'=>"1.0",
            'Timestamp'          => date('Y-m-d\TH:i:s\Z'),
        	'Version'=>"2017-05-25",
        	'SignatureNonce'=>uniqid(),
        ], $params);
        $params['Signature'] = $this->genSign($params);
    	date_default_timezone_set($timezone);
        return $this->params($params);
    }

	protected function percentEncode($str)
	{
	    $res = urlencode($str);
	    $res = preg_replace('/\+/', '%20', $res);
	    $res = preg_replace('/\*/', '%2A', $res);
	    $res = preg_replace('/%7E/', '~', $res);
	    return $res;
	}
	
	protected function signString($source, $accessSecret){
		return	base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
	}
	
    protected function genSign($parameters)
    {
	    ksort($parameters);
	    $canonicalizedQueryString = '';
	    foreach($parameters as $key => $value)
	    {
			$canonicalizedQueryString .= '&' . $this->percentEncode($key). '=' . $this->percentEncode($value);
	    }	
	    $stringToSign = "POST".'&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
	    $signature = $this->signString($stringToSign, $this->secretKey."&");

	    return $signature;
    }

    protected function setResult($result, $callbackName)
    {
        if ($result['request']) {
            $result = json_decode($result['response'], true);
            if ($result['Code'] == "OK") {
                $this->result(Agent::SUCCESS,true);
                $this->result(Agent::INFO, json_encode($result));
                $this->result(Agent::CODE, $result['Code']);
            }else{
                $this->result(Agent::INFO, json_encode($result));
                $this->result(Agent::CODE, $result['Code']);
            }
        } else {
            $this->result(Agent::INFO, 'request failed');
        }
    }

    protected function genResponseName($method)
    {
        return str_replace('.', '_', $method) . '_response';
    }

    protected function getTempDataString(array $data)
    {
        $data = array_map(function ($value) {
            return (string) $value;
        }, $data);

        return json_encode($data);
    }
}
