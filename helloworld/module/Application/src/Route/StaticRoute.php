<?php
namespace Application\Route;

use Traversable;
use \Zend\Router\Exception;
use \Zend\Stdlib\ArrayUtils;
use \Zend\Stdlib\RequestInterface as Request;
use \Zend\Router\Http\RouteInterface;
use \Zend\Router\Http\RouteMatch;

/**
 * Static route.
 */
class StaticRoute implements RouteInterface
{
    protected $dirName;
    
    protected $templatePrefix;

    protected $fileNamePattern = '/[a-zA-Z0-9_\-]+/';
    
    // Defaults.
    protected $defaults;

    // List of assembled parameters.
    protected $assembledParams = [];

    // Constructor.
    public function __construct($dirName, $templatePrefix, $fileNamePattern, array $defaults = [])
    {
        $this->dirName = $dirName;
        $this->templatePrefix = $templatePrefix;
        $this->fileNamePattern = $fileNamePattern;
        $this->defaults = $defaults;
    }
    
    // Create a new route with given options.
    public static function factory($options = [])
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . 
                    ' expects an array or Traversable set of options');
        }

        if (!isset($options['dir_name'])) {
            throw new Exception\InvalidArgumentException(
                    'Missing "dir_name" in options array');
        }
        
        if (!isset($options['template_prefix'])) {
            throw new Exception\InvalidArgumentException(
                    'Missing "template_prefix" in options array');
        }
        
        if (!isset($options['filename_pattern'])) {
            throw new Exception\InvalidArgumentException(
                    'Missing "filename_pattern" in options array');
        }
                
        if (!isset($options['defaults'])) {
            $options['defaults'] = [];
        }

        return new static(
                $options['dir_name'], 
                $options['template_prefix'], 
                $options['filename_pattern'], 
                $options['defaults']);
    }

    // Match a given request.
    public function match(Request $request, $pathOffset = null)
    {
        // Ensure this route type is used in a HTTP request
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        // Get URL and its path part.
        $uri  = $request->getUri();
        $path = $uri->getPath();
        
        if ($pathOffset!=null) 
            $path = substr($path, $pathOffset);
        
        // Get array of path segments.
        $segments = explode('/', $path);
                
        // Check each segment against allowed file name template.
        foreach ($segments as $segment) {            
            if(strlen($segment)==0)
                continue;
            if(!preg_match($this->fileNamePattern, $segment))
                return null;
        }
        
        // Check if such a .phtml file exists on disk        
        $fileName = $this->dirName . '/'. $this->templatePrefix . $path . '.phtml';                
        if(!is_file($fileName) || !is_readable($fileName)) {
            return null;
        }
                
        $matchedLength = strlen($path); 
        
        return new RouteMatch(
                array_merge($this->defaults, ['page'=>$this->templatePrefix.$path]), 
                $matchedLength);
    }

    // Assembles an URL by route params
    public function assemble(array $params = [], array $options = [])
    {
        $mergedParams          = array_merge($this->defaults, $params);
        $this->assembledParams = [];
        
        if (!isset($params['page'])) {
            throw new Exception\InvalidArgumentException(__METHOD__ . 
                    ' expects the "page" parameter');
        }
        
        $segments = explode('/', $params['page']);
        $url = '';
        foreach ($segments as $segment) {
            if(strlen($segment)==0)
                continue;
            $url .= '/' . rawurlencode($segment);
        }
        
        $this->assembledParams[] = 'page';
        
        return $url;
    }

    // Get a list of parameters used while assembling.
    public function getAssembledParams()
    {
        return $this->assembledParams;
    }
}
