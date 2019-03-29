<?php

namespace leap\composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;

class PhpcsPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var IOInterface
     */
    private $io;
    
    public static function getSubscribedEvents()
    {
        return array(
            'post-install-cmd' => 'installGitPreHook',
            'post-update-cmd' => 'installGitPreHook'
        );
    }

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->io = $io;
    }

    public function installGitPreHook()
    {
        $hooksDir = '.git/hooks';
        if (!is_dir($hooksDir)) {
            $this->io->isVeryVerbose() && $this->io->writeError("No .git found, not in vcs?");
            return;
        }
        if (file_exists($hookFile = $hooksDir . '/pre-commit')) {
            $this->io->isVeryVerbose() && $this->io->writeError("pre-commit hook file exists, skip");
            return;
        }
        copy(__DIR__.'/../resources/pre-commit', $hookFile);
        chmod($hookFile, 0755);
        $this->io->write("<info>Install git hook script $hookFile</>");
    }
}
