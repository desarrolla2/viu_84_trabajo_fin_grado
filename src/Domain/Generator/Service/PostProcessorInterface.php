<?php
/*
 * This file is part of the Data Miner.
 *
 * Daniel González <daniel@devtia.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Domain\Generator\Service;

use App\Domain\Generator\ValueObject\Text;

interface PostProcessorInterface
{
    public static function order(): int;

    public function execute(Text $text): Text;
}
