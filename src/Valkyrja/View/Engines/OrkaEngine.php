<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja Framework package.
 *
 * (c) Melech Mizrachi <melechmizrachi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\View\Engines;

use Valkyrja\Auth\Facades\Auth;
use Valkyrja\Support\Directory;
use Valkyrja\View\Config\Config;

use function array_keys;
use function file_get_contents;
use function file_put_contents;
use function is_file;
use function md5;
use function preg_replace;

/**
 * Class OrkaEngine.
 *
 * @author Melech Mizrachi
 */
class OrkaEngine extends PhpEngine
{
    /**
     * @var array<string, string>
     */
    protected static array $replace = [
        // @layout
        '/@layout\s*\(\s*(.*)\s*\)/x'              => '<?php $template->setLayout(${1}); ?>',
        '/@block\s*\(\s*(.*)\s*\)/x'               => '<?= $template->getBlock(${1}); ?>',
        '/@trimblock\s*\(\s*(.*)\s*\)/x'           => '<?= trim($template->getBlock(${1})); ?>',
        '/@startblock\s*\(\s*(.*)\s*\)/x'          => '<?php $template->startBlock(${1}); ?>',
        '/@endblock/x'                             => '<?php $template->endBlock(); ?>',
        '/@hasblock\s*\(\s*(.*)\s*\)/x'            => '<?php if ($template->hasBlock(${1})) : ?>',
        '/@elsehasblock/x'                         => '<?php else : ?>',
        '/@elseifhasblock\s*\(\s*(.*)\s*\)/x'      => '<?php elseif ($template->hasBlock(${1})) : ?>',
        '/@endhasblock/x'                          => '<?php endif ?>',
        '/@unlessblock\s*\(\s*(.*)\s*\)/x'         => '<?php if (! $template->hasBlock(${1})) : ?>',
        '/@endunlessblock/x'                       => '<?php endif ?>',
        '/@partial\s*\(\s*(.*)\s*\s*\)/x'          => '<?= $template->getPartial(${1}); ?>',
        '/@partial\s*\(\s*(.*)\s*,(.*)\s*\)/x'     => '<?= $template->getPartial(${1}, ${2}); ?>',
        '/@trimpartial\s*\(\s*(.*)\s*\s*\)/x'      => '<?= trim($template->getPartial(${1})); ?>',
        '/@trimpartial\s*\(\s*(.*)\s*,(.*)\s*\)/x' => '<?= trim($template->getPartial(${1}, ${2})); ?>',
        '/@setvariables\s*\(\s*(.*)\s*\s*\)/x'     => '<?php $template->setVariables(${1}); ?>',
        '/@setvariable\s*\(\s*(.*)\s*,(.*)\s*\)/x' => '<?php $template->setVariable(${1}, ${2}); ?>',
        '/@empty\s*\(\s*(.*)\s*\)/x'               => '<?php if (empty(${1})) : ?>',
        '/@notempty\s*\(\s*(.*)\s*\)/x'            => '<?php if (! empty(${1})) : ?>',
        '/@if\s*\(\s*(.*)\s*\)/x'                  => '<?php if (${1}) : ?>',
        '/@elseif\s*\(\s*(.*)\s*\)/x'              => '<?php elseif (${1}) : ?>',
        '/@else/x'                                 => '<?php else : ?>',
        '/@endif/x'                                => '<?php endif ?>',
        '/@isset\s*\(\s*(.*)\s*\)/x'               => '<?php if (isset(${1})) : ?>',
        '/@endisset/x'                             => '<?php endif ?>',
        '/@unless\s*\(\s*(.*)\s*\)/x'              => '<?php if (! (${1})) : ?>',
        '/@elseunless\s*\(\s*(.*)\s*\)/x'          => '<?php elseif (! (${1})) : ?>',
        '/@endunless/x'                            => '<?php endif ?>',
        '/@foreach\s*\(\s*(.*)\s*\)/x'             => '<?php foreach (${1}) : ?>',
        '/@endforeach/x'                           => '<?php endforeach ?>',
        '/@for\s*\(\s*(.*)\s*\)/x'                 => '<?php for (${1}) : ?>',
        '/@endfor/x'                               => '<?php endfor ?>',
        '/@switch\s*\(\s*(.*)\s*\)/x'              => '<?php switch (${1}) :',
        '/@firstcase\s*\(\s*(.*)\s*\)/x'           => 'case ${1} : ?>',
        '/@case\s*\(\s*(.*)\s*\)/x'                => '<?php case ${1} : ?>',
        '/@break/x'                                => '<?php break; ?>',
        '/@default/x'                              => '<?php default : ?>',
        '/@endswitch/x'                            => '<?php endswitch ?>',
        '/@dd\s*\(\s*(.*)\s*\s*\)/x'               => '<?php \Valkyrja\dd(${1}); ?>',
        // {{--
        '/\{\{\-\-/x'                              => '<?php /** ?>',
        // --}}
        '/\-\-\}\}/x'                              => '<?php */ ?>',
        // {{{ unescaped Auth::
        '/\{\{\{\s*Auth::/x'                       => '{{{ ' . Auth::class . '::',
        // {{ escaped Auth::
        '/\{\{\s*Auth::/x'                         => '{{ ' . Auth::class . '::',
        // {{{ unescaped }}}
        '/\{\{\{\s*(.*?)\s*\}\}\}/x'               => '<?= ${1}; ?>',
        // {{ escaped }}
        '/\{\{\s*(.*?)\s*\}\}/x'                   => '<?= $template->escape(${1}); ?>',
    ];

    /**
     * OrkaEngine constructor.
     *
     * @param Config|array{
     *     dir: string,
     *     engine: string,
     *     engines: array<string, class-string>,
     *     paths: array<string, string>,
     *     disks: array{orka?: array{fileExtension: string}, php?: array{fileExtension: string}}
     * }           $config  The config
     * @param bool $isDebug Whether to run in debug mode
     */
    public function __construct(
        Config|array $config,
        protected bool $isDebug
    ) {
        parent::__construct($config);

        $this->fileExtension = $config['disks']['orka']['fileExtension'] ?? '.orka.phtml';
    }

    /**
     * @inheritDoc
     */
    public function renderFile(string $name, array $variables = []): string
    {
        $cachedPath = $this->getCachedFilePath($name);

        if ($this->isDebug || ! is_file($cachedPath)) {
            $contents = $this->parseContent(file_get_contents($this->getFullPath($name)));

            file_put_contents($cachedPath, $contents);
        }

        return $this->renderFullPath($cachedPath, $variables);
    }

    /**
     * Parse okra written content to PHP parseable.
     *
     * @param string $contents The contents to parse
     */
    protected function parseContent(string $contents): string
    {
        return preg_replace(array_keys(self::$replace), self::$replace, $contents);
    }

    /**
     * Get the cached file path.
     *
     * @param string $name The name
     */
    protected function getCachedFilePath(string $name): string
    {
        return Directory::storagePath('views/' . md5($name));
    }
}
