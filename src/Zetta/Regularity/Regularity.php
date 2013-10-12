<?php

namespace Zetta\Regularity;

/**
 * @author zetta
 */
class Regularity
{
    /**
     * Patters to use
     * Makes easier writting rules
     */
    protected static $patterns = array(
        ':digit'        => '[0-9]',
        ':lowercase'    => '[a-z]',
        ':uppercase'    => '[A-Z]',
        ':letter'       => '[A-Za-z]',
        ':alphanumeric' => '[A-Za-z0-9]',
        ':whitespace'   => '\s',
        ':space'        => ' ',
        ':tab'          => '\t',
    );

    /**
     * regexp options
     * @var @todo
     * @todo
     */
    protected $options = null;

    /**
     * This special chars need to be scaped
     * @var string
     */
    protected $escapedChars = '*.?^+$|()[]{}';

    /**
     * 'compiled' regexp
     * @var string
     */
    protected $regexp;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->regexp = '';
    }

    /**
     * We can use Regularity Object as string one
     * @return string
     */
    public function __toString()
    {
        return (string) $this->regexp;
    }


    /**
     * Outputs the expression
     * @return string
     */
    public function getExpression()
    {
        return (string) $this->regexp;
    }

    /**
     * Sets how the string MUST start
     * @param string|int
     * @param [OPTIONAL] string
     * @return Regularity
     */
    public function startWith($string, $pattern = null)
    {
        if (strlen($this->regexp))
            throw new Exception('Cant define a \'startWith\' when you have been created rules before');
        return $this->write('^%s', $string, $pattern);
    }

    /**
     * Appends text to the expression
     * @param string|int
     * @param [OPTIONAL] string
     * @return Regularity
     */
    public function append($string, $pattern = null)
    {
        return $this->write('%s',$string, $pattern);
    }

    /**
     * Alias of append
     * @see Regularity::append
     * @return Regularity
     */
    public function then($string, $pattern = null)
    {
        return $this->append($string, $pattern);
    }

    /**
     * Sets how the string MUST end
     * @param string|int
     * @param [OPTIONAL] string
     * @return Regularity
     */
    public function endWith($string, $pattern = null)
    {
        return $this->write('%s$', $string, $pattern);
    }

    /**
     * A substring that maybe appear in a string
     */
    public function maybe($string, $pattern = null)
    {
        return $this->write('%s?',$string, $pattern);
    }

    public function oneOf($elements)
    {
        return $this->write(sprintf('[%s]', implode('|', $elements)), '');
    }

    public function between($start, $end, $pattern = null)
    {
        return $this->write(sprintf('%%s{%d,%d}', (int) $start, (int) $end) , $pattern);
    }

    /**
     * @todo
     */
    public function atLeast($times, $pattern)
    {
        return $this->between($times, null, $pattern);
    }

    /**
     * @todo
     */
    public function atMost($times, $pattern)
    {
        return $this->between(null, $times, $pattern);
    }

    /**
     * @todo
     */
    public function zeroOrMore($string, $pattern)
    {
        return $this->write('%s*',$string, $pattern);
    }

    /**
     * @todo
     */
    public function oneOrMore($string, $pattern)
    {
        return $this->write('%s+',$string, $pattern);
    }




    protected function write($format, $string, $pattern = null)
    {
        $this->regexp .= sprintf($format, $this->interpret($string, $pattern));
        return $this;
    }


    public function interpret($string, $pattern)
    {
        if (null == $pattern)
            return $this->patternedConstraint($string);
        else
            return $this->numberedConstraint($string, $pattern);
    }

    protected function numberedConstraint($integer, $pattern)
    {
        $constraint = $this->patternedConstraint($pattern);
        $constraint = (1 == strlen($pattern) || $this->isRegularPattern($pattern)) ? $constraint : '(' . $constraint . ')';
        return sprintf('%s{%d}', $constraint, (int) $integer);
    }


    /**
     *
     */
    protected function patternedConstraint($pattern)
    {
        return $this->translate((string) $pattern);
    }

    /**
     * Singularization
     * remove trailing s in words
     * @param string word
     * @return string
     */
    protected function singularize($word)
    {
        return 's' == substr($word, -1) ? substr($word, 0, -1) : $word; // basic singularization
    }

    /**
     * Escape special chars
     * @param string $pattern
     * @return string
     */
    protected function escape($pattern)
    {
        $escaped = '';
        for($i=0; $i<strlen($pattern); $i++)
        {
            $escaped .= (false === strpos($this->escapedChars, $pattern[$i])) ? $pattern[$i] : '\\'.$pattern[$i];
        }
        return $escaped; // @todo
    }

    /**
     * Translate an identifier such as :digits to [0-9], etc
     * Returns the original identifier if no character class found
     */
    protected function translate($pattern)
    {
        return $this->isRegularPattern($pattern) ? static::$patterns[$this->singularize($pattern)] : $this->escape($pattern);
    }

    /**
     * Is this pattern known in Regularity patterns?
     * @param string $pattern
     * @return boolean
     */
    protected function isRegularPattern($pattern)
    {
        return isset(static::$patterns[$this->singularize($pattern)]);
    }
}