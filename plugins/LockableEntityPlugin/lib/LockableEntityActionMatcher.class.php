<?php

/**
 * This is default implementation of action matcher interface.
 *  It matches module and action based on regex or plain text comparision.
 *  For syntactic sugar single asterisk may be used as a synonym for 'anything'
 *    instead of writing regex pattern.
 *
 * @author sakfa
 */
class LockableEntityActionMatcher implements ILockableEntityActionMatcher
{

    protected $modulePattern;
    protected $actionPattern;

    /**
     *  
     * @param string $modulePattern
     * @param string $actionPattern 
     */
    public function __construct($modulePattern, $actionPattern)
    {
        $this->modulePattern = $modulePattern;
        $this->actionPattern = $actionPattern;
    }

    /**
     *  Returns true if given module and action matches connected pattern.
     */
    public function actionMatches($module, $action)
    {
        return $this->patternMatches($this->modulePattern, $module) &&
                $this->patternMatches($this->actionPattern, $action);
    }

    /**
     *  Returns true if $test matches $pattern.
     *  Possible patterns are:
     *      regexps enclosed in slashes i.e. '/^saleList_/' (preg_match)
     *      plain strings i.e. 'saleView' (exact match)
     *      single asterisk '*' (matches everything)
     */
    protected function patternMatches($pattern, $subject)
    {
        if ($pattern === '*') {
            return true;
        } else if ($pattern[0] === '/'
                && $pattern[strlen($pattern) - 1] === '/') {
            return preg_match($pattern, $subject);
        } else {
            return $pattern === $subject;
        }
    }

}
