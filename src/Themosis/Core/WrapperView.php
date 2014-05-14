<?php
namespace Themosis\Core;

class WrapperView {

    /**
     * The absolute view path.
     *
     * @var string
     */
    private $path;

    /**
     * The container view content.
     *
     * @var string
     */
    private $content;

    /**
     * View sections found: %section_$name%
     *
     * @var array
     */
    private $sections = array();

    /**
     * Build a wrapper view object.
     *
     * @param string $path The absolute view path to use for the metabox.
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->content = $this->parse($this->path);
        $this->sections = $this->setSections($this->content);
    }

    /**
     * Save the content of the view.
     *
     * @param string $path The view path.
     * @return string The default view content with sections, empty if failed.
     */
    private function parse($path)
    {
        if(file_exists($path)){
            return file_get_contents($path);
        }

        return '';

    }

    /**
     * Set the list of sections.
     *
     * @param string $content The view content.
     * @return array
     */
    private function setSections($content)
    {
        $matches = array();

        preg_match_all('/\%section_(.*?)\%/', $content, $matches);

        return end($matches);
    }

    /**
     * Retrieve the list of sections.
     *
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param string $section The section name.
     * @param string $field The rendered field html.
     */
    public function fillSection($section, $field)
    {
        $content = str_replace('%section_'.$section.'%', $field.'%section_'.$section.'%', $this->content);

        $this->content = $content;
    }

    /**
     * Render the wrapper view and its inner fields.
     */
    public function render()
    {
        // Clean sections.
        foreach($this->sections as $section){

            $this->content = str_replace('%section_'.$section.'%', '', $this->content);

        }

        // Output the defined content.
        echo($this->content);
    }

} 