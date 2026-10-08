<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Tenant\Actions\Markdown\GetLocalizedMarkdownPathAction;
use Spatie\QueueableAction\QueueableAction;

class GetPublishedPrivacyPolicyAction
{
    use QueueableAction;

    public function execute(): ?string
    {
        $path = app(GetLocalizedMarkdownPathAction::class)->execute('policy.md');
        if ($path === '#' || ! File::isFile($path)) {
            return null;
        }

        $content = trim(File::get($path));
        if ($content === '' || Str::contains($content, 'Edit this file to define the privacy policy for your application.')) {
            return null;
        }

        return $content;
    }
}
