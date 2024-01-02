<?php

declare(strict_types=1);

namespace Verdient\DingTalk;

use Verdient\HttpAPI\AbstractClient;

/**
 * 钉钉
 * @author Verdient。
 */
class DingTalk extends AbstractClient
{
    /**
     * @inheritdoc
     * @author Verdient。
     */
    public $protocol = 'https';

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public $host = 'api.dingtalk.com';

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public $routePrefix = 'v1.0';

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public $request = Request::class;

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
     * @inheritdoc
     * @author Verdient。
     */
    public function request($path): Request
    {
        /** @var Request */
        $request = parent::request($path);
        $request->appKey = $this->appKey;
        $request->appSecret = $this->appSecret;
        $request->requestPath = $this->getRequestPath();
        return $request;
    }
}
