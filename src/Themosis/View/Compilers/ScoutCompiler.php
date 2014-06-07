<?php
namespace Themosis\View\Compilers;

use \Exception;

class ScoutCompiler extends Compiler implements ICompiler {

    /**
     * The original view path.
     *
     * @var string
     */
    protected $path;

    /**
     * Opening and closing echo tags.
     *
     * @var array
     */
    protected $echoTags = array('{{', '}}');

    /**
     * Opening and closing escaped tags.
     *
     * @var array
     */
    protected $escapedTags = array('{{{', '}}}');

    /**
     * Compile the view at the given path.
     *
     * @param string $path
     * @return void
     */
    public function compile($path)
    {
        if($path){
            $this->setPath($path);
        }

        // Compile the view content.
        $content = $this->compileString($this->getViewContent($path));

        if(!is_null($this->storage)){

            // Store the compiled view.
            file_put_contents($this->getCompiledPath($this->getPath()), $content);

        }
    }

    /**
     * Compile the scout view file.
     *
     * @param string $content
     * @return string
     */
    public function compileString($content)
    {
        $result = '';

        foreach(token_get_all($content) as $token){

            $result .= is_array($token) ? $this->parseToken($token) : $token;

        }

        return $result;
    }

    /**
     * Set the original view path.
     *
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Return the original view path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Parse token from template.
     *
     * @param array $token
     * @return string
     */
    protected function parseToken(array $token)
    {
        list($id, $content) = $token;

        if($id == T_INLINE_HTML){

            // Keep 'Echos' as last compiler.
            foreach(array('Statements', 'Comments', 'Echos') as $type){

                $content = $this->{"compile{$type}"}($content);

            }

        }

        return $content;
    }

    /**
     * Compile to native PHP statements. Compile all methods
     * that start with '@'.
     *
     * @param string $content
     * @return string
     */
    protected function compileStatements($content)
    {
        $compiler = $this;

        $callback = function($match) use($compiler){

            if(method_exists($compiler, $method = 'compile'.ucfirst($match[1]))){

                $match[0] = $compiler->$method(array_get($match, 3));

            }

            return isset($match[3]) ? $match[0] : $match[0].$match[2];
        };

        return preg_replace_callback('/\B@(\w+)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $content);
    }

    /**
     * Compile to native PHP comments.
     *
     * @param string $content
     * @return string
     */
    protected function compileComments($content)
    {
        $pattern = sprintf('/%s--((.|\s)*?)--%s/', $this->echoTags[0], $this->echoTags[1]);

        return preg_replace($pattern, '<?php /*$1*/ ?>', $content);
    }

    /**
     * Compile to native PHP echos.
     *
     * @param string $content
     * @return string
     */
    protected function compileEchos($content)
    {
        $difference = strlen($this->echoTags[0]) - strlen($this->escapedTags[0]);

        if($difference > 0){
            return $this->compileEscapedEchos($this->compileRegularEchos($content));
        }

        return $this->compileRegularEchos($this->compileEscapedEchos($content));
    }

    /**
     * Compile regular echos.
     *
     * @param string $content
     * @return string
     */
    protected function compileRegularEchos($content)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s/s', $this->echoTags[0], $this->echoTags[1]);

        $compiler = $this;

        $callback = function($matches) use($compiler){
            return $matches[1] ? substr($matches[0], 1) : '<?php echo '.$compiler->compileEchoDefaults($matches[2]).'; ?>';
        };

        return preg_replace_callback($pattern, $callback, $content);
    }

    /**
     * Compile escape echos.
     *
     * @param string $content
     * @return string
     */
    protected function compileEscapedEchos($content)
    {
        $pattern = sprintf('/%s\s*(.+?)\s*%s/s', $this->escapedTags[0], $this->escapedTags[1]);

        $compiler = $this;

        $callback = function($matches) use($compiler){
            return '<?php echo e('.$compiler->compileEchoDefaults($matches[1]).'); ?>';
        };

        return preg_replace_callback($pattern, $callback, $content);
    }

    /**
     * Compile default echo content.
     * Allow user to check for value existence by using the 'or' shortcut
     * to define a default value.
     *
     * @param string $content
     * @return string
     */
    protected function compileEchoDefaults($content)
    {
        return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', 'isset($1) ? $1 : $2', $content);
    }

    /**
     * Compile include statements.
     *
     * @param string $expression
     * @return string
     */
    protected function compileInclude($expression)
    {
        if(starts_with($expression, '(')){
            $expression = substr($expression, 1, -1);
        }

        return "<?php echo \$__env->make($expression, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";
    }

    /**
     * Return the defined view content.
     *
     * @param string $path
     * @throws \Exception
     * @return string
     */
    private function getViewContent($path)
    {
        if(is_file($path)){
            return file_get_contents($path);
        }

        throw new Exception('View file does not exists at this location: '.$path);
    }

}