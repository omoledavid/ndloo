<?php

declare(strict_types=1);

namespace App\Support\Helpers\Signatures;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class KorapaySignature implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $signature = $request->header($config->signatureHeaderName);
        if (! $signature) {
            return false;
        }
        $signingSecret = $config->signingSecret;

        if (empty($signingSecret)) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        $computedSignature = hash_hmac('256', $request->getContent(), $signingSecret);

        return hash_equals($signature, $computedSignature);
    }
}
