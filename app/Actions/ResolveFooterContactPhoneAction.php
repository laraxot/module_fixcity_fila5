<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Cms\Datas\BlockData;
use Modules\Cms\Models\Section;
use Spatie\QueueableAction\QueueableAction;

/**
 * Telefono del tenant: `contact.phone` dei blocchi del footer (sections/footer.json), la stessa fonte del footer.
 * Stringa vuota se il tenant non ha un telefono: chi lo mostra non deve inventarne uno.
 */
final class ResolveFooterContactPhoneAction
{
    use QueueableAction;

    public function execute(): string
    {
        return $this->fromBlocks(Section::getBlocksBySlug('footer'));
    }

    /**
     * @param  iterable<array-key, BlockData|array<array-key, mixed>>  $blocks
     */
    public function fromBlocks(iterable $blocks): string
    {
        foreach ($blocks as $block) {
            $data = $block instanceof BlockData ? $block->data : ($block['data'] ?? null);
            $phone = data_get($data, 'contact.phone');
            if (is_string($phone) && trim($phone) !== '') {
                return trim($phone);
            }
        }

        return '';
    }
}
