<?php

declare(strict_types=1);

namespace Verdient\DingTalk;

use Exception;
use Verdient\http\Request as HttpRequest;

/**
 * 请求
 * @author Verdinet。
 */
class Request extends HttpRequest
{
    /**
     * @var string App标识
     * @author Verdient。
     */
    public $appKey;

    /**
     * @var string App秘钥
     * @author Verdient。
     */
    public $appSecret;

    /**
     * @var string 请求路径
     * @author Verdient。
     */
    public $requestPath;

    /**
     * @inheritdoc
     * @return Response
     * @author Verdient。
     */
    public function send()
    {
        return new Response(parent::send());
    }

    /**
     * 携带访问秘钥
     * @return static
     * @author Verdient。
     */
    public function withToken()
    {
        $this->addHeader('x-acs-dingtalk-access-token', $this->getAccessToken());
        return $this;
    }

    /**
     * 获取访问秘钥
     * @return static
     * @author Verdient。
     */
    protected function getAccessToken()
    {
        $cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'verdient' . DIRECTORY_SEPARATOR .  'ding-talk';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        $cacheFileName = md5($this->appSecret . '::' . $this->appSecret . '-access_token');
        $cachePath = $cacheDir . DIRECTORY_SEPARATOR . $cacheFileName;
        if (file_exists($cachePath)) {
            $cache = unserialize(file_get_contents($cachePath));
            if (is_array($cache) && isset($cache['accessToken']) && isset($cache['expiredAt']) && $cache['expiredAt'] > time()) {
                return $cache['accessToken'];
            }
            @unlink($cachePath);
        }
        $request = new Request();
        $request->setMethod('POST');
        $request->setUrl($this->requestPath . '/oauth2/accessToken');
        $request->setBody([
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret
        ]);
        $res = $request->send();
        if ($res->getIsOK()) {
            $data = $res->getData();
            $cache = [
                'accessToken' => $data['accessToken'],
                'expiredAt' => time() + $data['expireIn'] - 60
            ];
            file_put_contents($cachePath, serialize($cache));
            return $data['accessToken'];
        } else {
            throw new Exception('[' . $res->getErrorCode() . '] ' . $res->getErrorMessage());
        }
    }
}
