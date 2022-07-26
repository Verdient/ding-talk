<?php

declare(strict_types=1);

namespace Verdient\DingTalk;

use Verdient\http\Response as HttpResponse;
use Verdient\HttpAPI\AbstractResponse;
use Verdient\HttpAPI\Result;

/**
 * 响应
 * @author Verdinet。
 */
class Response extends AbstractResponse
{
    /**
     * @inheritdoc
     * @author Verdient。
     */
    protected function normailze(HttpResponse $response): Result
    {
        $result = new Result;
        $statusCode = $response->getStatusCode();
        $body = $response->getBody();
        if ($statusCode >= 200 && $statusCode <= 300) {
            $result->isOK = true;
            $result->data = $body;
        }
        if (!$result->isOK) {
            $result->errorCode = $body['code'] ?? $statusCode;
            $result->errorMessage = $body['message'] ?? $response->getStatusMessage();
        }
        return $result;
    }
}
