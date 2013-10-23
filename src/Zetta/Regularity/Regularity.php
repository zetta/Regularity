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

    public function oneOf(array $elements)
    {
        foreach ($elements as $key => $value)
        {
            $elements[$key] = $this->interpret($value, null);
        }
        return $this->writeRegexp(sprintf('(%s)', implode('|', $elements)));
    }

    public function between($start, $end, $string)
    {
        $format = (strlen($string) > 1 && !$this->isRegularPattern($string)) ? '(%s){%s,%s}' : '%s{%s,%s}';
        return $this->writeRegexp(sprintf($format, $this->interpret($string, null), $start, $end));
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
     * A substring that maybe appear in a string
     */
    public function maybe($string, $pattern = null)
    {
        $constraint = $this->interpret($string, $pattern);
        $format = (strlen($constraint) > 1 && !$this->isRegularPattern($string)) ? '(%s)?' : '%s?';
        return $this->writeRegexp(sprintf($format, $constraint));
    }

    /**
     * @todo
     */
    public function zeroOrMore($string, $pattern = null)
    {
        $constraint = $this->interpret($string, $pattern);
        $format = (strlen($constraint) > 1 && !$this->isRegularPattern($string)) ? '(%s)*' : '%s*';
        return $this->writeRegexp(sprintf($format, $constraint));
    }

    /**
     * @todo
     */
    public function oneOrMore($string, $pattern = null)
    {
        $constraint = $this->interpret($string, $pattern);
        $format = (strlen($constraint) > 1 && !$this->isRegularPattern($string)) ? '(%s)+' : '%s+';
        return $this->writeRegexp(sprintf($format, $constraint));
    }

    /**
     * Try to convert the string into the format and interpret the pattern
     * @param string $format
     * @param string $string
     * @param string $pattern
     */
    protected function write($format, $string, $pattern = null)
    {
        return $this->writeRegexp(sprintf($format, $this->interpret($string, $pattern)));
    }

    /**
     * Check if a given string matches with the current pattern
     * @param string $string String to check
     * @return boolean
     */
    public function test($string)
    {
        return (1 === preg_match($this->getPattern(), $string));
    }

    /**
     * Sets the option to set a case-insensitive search
     * @return Regularity
     */
    public function ignoreCase()
    {
        $this->addOption(Option::IGNORE_CASE);
        return $this;
    }

    /**
     * Gets the pattern
     * @return string
     */
    public function getPattern()
    {
        return '/' . $this->regexp . '/' . $this->options;
    }

    /**
     * Core!! =)
     * Write te string to the expression
     * @param string
     * @return Regularity
     */
    final protected function writeRegexp($string)
    {
        $this->regexp .= $string;
        return $this;
    }

    /**
     * Interpret the string and try to determine what type of contraint is
     * @param string|int $string
     * @param string [OPTIONAL] $pattern
     * @return string
     */
    protected function interpret($string, $pattern = null)
    {
        if (null == $pattern)
            return $this->patternedConstraint($string);
        else
            return $this->numberedConstraint($string, $pattern);
    }

    /**
     * Works with numbered constraints for example (4, ':digits')
     * @param int
     * @param string $pattern
     */
    protected function numberedConstraint($integer, $pattern)
    {
        $constraint = $this->patternedConstraint($pattern);
        $constraint = (1 == strlen($pattern) || $this->isRegularPattern($pattern)) ? $constraint : $this->enclose($constraint);
        return sprintf('%s{%d}', $constraint, (int) $integer);
    }

    /**
     * Works with patterns example ('hello') or (':digit')
     * @param string $pattern
     * @return string
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
        return preg_quote($pattern, '/');
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

    /**
     * Encloses a string
     * @param string
     * @return string
     */
    protected function enclose($string)
    {
        return '(' . $string . ')';
    }

    /**
     * Add option
     */
    protected function addOption($option)
    {
        $this->options .= $option;
    }

}