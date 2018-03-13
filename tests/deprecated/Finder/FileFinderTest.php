<?php

use PHPUnit\Framework\TestCase;

class FileFinderTest extends TestCase
{
    /**
     * Test in order to find view files.
     */
    public function testViewFinder()
    {
        $finder = new \Themosis\View\ViewFinder(new \Illuminate\Filesystem\Filesystem(), [], ['blade.php', 'scout.php', 'php', 'twig']);
        $paths = [
            themosis_path('sys').'Metabox'.DS.'Views',
            themosis_path('sys').'Field'.DS.'Fields'.DS.'Views',
        ];
        foreach ($paths as $path) {
            $finder->addLocation($path);
        }

        // Paths are correctly registered.
        $this->assertEquals($paths, $finder->getPaths());

        // Test if core Metabox file is found.
        $metaboxCoreFullPath = $finder->find('_themosisCoreMetabox');

        $this->assertEquals(themosis_path('sys').'Metabox'.DS.'Views'.DS.'_themosisCoreMetabox.scout.php', $metaboxCoreFullPath);
    }

    /**
     * Test in order to find asset files.
     */
    public function testAssetFinder()
    {
        $finder = new \Themosis\Asset\AssetFinder();
        $paths = [plugins_url('themosis-framework/src/Themosis/_assets') => themosis_path('sys').'_assets'];
        $finder->addPaths($paths);

        // Check paths are correctly registered.
        $this->assertEquals($paths, $finder->getPaths());

        // Test if core assets files are found.
        // The difference here with the AssetFinder is
        // that the find method returns the URL of the asset and not the path.
        $coreCss = $finder->find('css/_themosisCore.css');
        $coreJs = $finder->find('js/_themosisCore.js');

        $this->assertEquals(plugins_url('themosis-framework/src/Themosis/_assets').'/css/_themosisCore.css', $coreCss);
        $this->assertEquals(plugins_url('themosis-framework/src/Themosis/_assets').'/js/_themosisCore.js', $coreJs);
    }
}
