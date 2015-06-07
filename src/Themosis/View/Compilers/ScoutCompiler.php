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
     * List of lines to add to templates.
     *
     * @var array
     */
    protected $footer = array();

    /**
     * Compile the view at the given path.
     *
     * @param string $path
     * @return void
     */
    public function compile($path)
    {
        // Reset footer.
        $this->footer = array();

        if ($path)
        {
            $this->setPath($path);
        }

        // Compile the view content.
        $content = $this->compileString($this->getViewContent($path));

        if (!is_null($this->storage))
        {
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

        foreach (token_get_all($content) as $token)
        {
            $result .= is_array($token) ? $this->parseToken($token) : $token;
        }

        // Add extends lines at the end of compiled results
        // so we can inherit templates.
        if (count($this->footer) > 0)
        {
            $result = ltrim($result, PHP_EOL).PHP_EOL.implode(PHP_EOL, array_reverse($this->footer));
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

        if ($id == T_INLINE_HTML)
        {
            // Keep 'Echos' as last compiler.
            foreach (array('Statements', 'Comments', 'Echos') as $type)
            {
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

            if (method_exists($compiler, $method = 'compile'.ucfirst($match[1])))
            {
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

        if ($difference > 0)
        {
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
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->echoTags[0], $this->echoTags[1]);

        $compiler = $this;

        $callback = function($matches) use($compiler){
            return $matches[1] ? substr($matches[0], 1) : '<?php echo('.$compiler->compileEchoDefaults($matches[2]).'); ?>';
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
        $pattern = sprintf('/%s\s*(.+?)\s*%s(\r?\n)?/s', $this->escapedTags[0], $this->escapedTags[1]);

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
     * Compile the yield statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileYield($expression)
    {
        return "<?php echo \$__env->yieldContent{$expression}; ?>";
    }

    /**
     * Compile the section statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileSection($expression)
    {
        return "<?php \$__env->startSection{$expression}; ?>";
    }

    /**
     * Compile the stop statements to valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileStop($expression)
    {
        return "<?php \$__env->stopSection(); ?>";
    }

    /**
     * Compile the show statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileShow($expression)
    {
        return "<?php echo \$__env->yieldSection(); ?>";
    }

    /**
     * Compile the overwrite statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileOverwrite($expression)
    {
        return "<?php \$__env->stopSection(true); ?>";
    }

    /**
     * Compile include statements.
     *
     * @param string $expression
     * @return string
     */
    protected function compileInclude($expression)
    {
        if (starts_with($expression, '('))
        {
            $expression = substr($expression, 1, -1);
        }

        return "<?php echo \$__env->make($expression, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";
    }

    /**
     * Compile the each statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileEach($expression)
    {
        return "<?php echo \$__env->renderEach{$expression}; ?>";
    }

    /**
     * Compile the unless statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileUnless($expression)
    {
        return "<?php if(!$expression): ?>";
    }

    /**
     * Compile the end unless statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndunless($expression)
    {
        return "<?php endif; ?>";
    }

    /**
     * Compile the else statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileElse($expression)
    {
        return "<?php else: ?>";
    }

    /**
     * Compile the for statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileFor($expression)
    {
        return "<?php for{$expression}: ?>";
    }

    /**
     * Compile the foreach statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileForeach($expression)
    {
        return "<?php foreach{$expression}: ?>";
    }

    /**
     * Compile the if statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileIf($expression)
    {
        return "<?php if{$expression}: ?>";
    }

    /**
     * Compile the else-if statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileElseif($expression)
    {
        return "<?php elseif{$expression}: ?>";
    }

    /**
     * Compile the while statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileWhile($expression)
    {
        return "<?php while{$expression}: ?>";
    }

    /**
     * Compile the end-while statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndwhile($expression)
    {
        return "<?php endwhile; ?>";
    }

    /**
     * Compile the end-for statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndfor($expression)
    {
        return "<?php endfor; ?>";
    }

    /**
     * Compile the end-for-each statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndforeach($expression)
    {
        return "<?php endforeach; ?>";
    }

    /**
     * Compile the end-if statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndif($expression)
    {
        return "<?php endif; ?>";
    }

    /**
     * Compile the extends statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileExtends($expression)
    {
        if (starts_with($expression, '('))
        {
            $expression = substr($expression, 1, -1);
        }

        $data = "<?php echo \$__env->make($expression, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";

        $this->footer[] = $data;

        return '';
    }

    /**
     * Compile the loop statement into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileLoop($expression)
    {
        return '<?php if(have_posts()){ while(have_posts()){ the_post(); ?>';
    }

    /**
     * Compile the endloop statement into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndloop($expression)
    {
        return '<?php }} ?>';
    }

    /**
     * Compile the query statement into valid PHP.
     *
     * @param string|WP_Query $expression
     * @return string
     */
    protected function compileQuery($expression)
    {
        return '<?php $themosisQuery = (is_array('.$expression.')) ? new WP_Query('.$expression.') : '.$expression.'; if($themosisQuery->have_posts()){ while($themosisQuery->have_posts()){ $themosisQuery->the_post(); ?>';
    }

    /**
     * Compile the endquery statement into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndquery($expression)
    {
        return '<?php }} wp_reset_postdata(); ?>';
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
        if (is_file($path))
        {
            return file_get_contents($path);
        }

        throw new Exception('View file does not exists at this location: '.$path);
    }

}
