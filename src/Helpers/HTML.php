<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 8/14/18
 * Time: 11:50 AM
 */

namespace LCI\Salsify\Helpers;


class HTML
{

    protected $html_element = 'p';

    protected $property_value;

    protected $class = '';

    protected $attributes = [];

    protected $pretty_html = false;

    protected $wrap_html_element = '';

    protected $limit = false;

    /**
     * HtmlHelper constructor.
     *
     * @param $property_value
     */
    public function __construct($property_value)
    {
        $this->property_value = $property_value;
    }

    /**
     * @return mixed
     */
    public function getPropertyValue()
    {
        return $this->property_value;
    }

    /**
     * @param mixed $property_value
     *
     * @return $this
     */
    public function setPropertyValue($property_value)
    {
        $this->property_value = $property_value;
        return $this;
    }

    /**
     * @param int $limit - limit the number of items to return
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }
    /**
     * @param bool $pretty_html
     *
     * @return $this
     */
    public function makePrettyHtml($pretty_html=true)
    {
        $this->pretty_html = $pretty_html;
        return $this;
    }

    /**
     * @param string $class - will add HTML class name(s) to created HTML Elements,
     *      can also add in the current count to the class name like: className-[[+count]]
     *
     * @return $this
     */
    public function addClass($class)
    {
        $this->class = $class;

        if (!empty($this->class) ) {
            $this->class .= ' '.$class;
        } else {
            $this->class = $class;
        }
        return $this;
    }

    /**
     * Will add the attributes to all created elements
     * @param string $name - a valid HTML attribute name, can also add in the count like: name_[[+count]]
     * @param string $value - a valid HTML attribute value, can also add in the count like: value_[[+count]]
     *
     * @return $this
     */
    public function addAttribute($name, $value)
    {
        if (isset($this->attributes[$name]) ) {
            $this->attributes[$name] .= ' '.$value;
        } else {
            $this->attributes[$name] = $value;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function makeParagraphs()
    {
        $this->html_element = 'p';
        return $this;
    }

    /**
     * @return $this
     */
    public function makeListItems()
    {
        $this->html_element = 'li';
        return $this;
    }

    /**
     * @return $this
     */
    public function wrapListItems()
    {
        $this->wrap_html_element = 'ul';
        return $this;
    }

    /**
     * @param string $element ~ a single element like: strong, span, div, ect.
     * @return $this
     */
    public function setItemInHTMLElement($element)
    {
        $this->html_element = $element;
        return $this;
    }

    /**
     * @param string $element ~ a single element like: div, section, ect.
     * @return $this
     */
    public function wrapItemInHTMLElement($element)
    {
        $this->wrap_html_element = $element;
        return $this;
    }

    /**
     *  no html added, but merge the fields together
     * @param string $separator
     * @return string
     */
    public function getAsPlainString($separator='|')
    {
        return $this->render(false, $separator);
    }

    /**
     * @return string
     */
    public function renderAsHTML()
    {
        return $this->render();
    }

    public function __toString()
    {
        return $this->render(false);
    }

    /**
     * @param bool $html
     * @param string $separator
     * @return string
     */
    protected function render($html=true, $separator='')
    {
        $string = '';
        $break = '';

        if ($this->pretty_html) {
            $break = PHP_EOL;
        }

        if(is_array($this->property_value)) {
            $count = 0;
            foreach ($this->property_value as $c => $value) {
                $count++;
                if ($this->limit && $count > $this->limit ) {
                    continue;
                }

                if (!empty($string)) {
                    $string .= $break;

                    if ($separator) {
                        $string .= $separator;
                    }
                }

                if ($html) {
                    $string .= $this->makeHTMLLine($value, $count, $break);
                } else {
                    $string .= $value;
                }
            }

        } elseif (is_string($this->property_value)) {
            if ($html) {
                $string = $this->makeHTMLLine($this->property_value, 1, $break);
            } else {
                $string = $this->property_value;
            }

        }

        if ($html && !empty($string) && !empty($this->wrap_html_element)) {
            $string = '<' . $this->wrap_html_element . '>' .
                $break .
                $string .
                $break .
                '</' . $this->wrap_html_element . '>';
        }

        return $string;
    }

    public function renderHtmlAttributes($count)
    {
        $attributes = '';
        if (!empty($this->class)) {
            $attributes .= ' class="'.$this->processPlaceholders('count', $count, $this->class).'"';
        }

        foreach ($this->attributes as $name => $value) {
            $attributes .= ' '.$this->processPlaceholders('count', $count, $name).'="'.$this->processPlaceholders('count', $count, $value).'"';
        }

        return $attributes;
    }

    /**
     * @param string $name - placeholder name ex: count
     * @param string $value - the value to replace the placeholder
     * @param string $string
     *
     * @return string
     */
    protected function processPlaceholders($name, $value, $string)
    {
        return str_replace('[[+'.$name.']]', $value, $string);
    }

    /**
     * @param string $line
     * @param int $count
     * @param string $break
     * @return string
     */
    protected function makeHTMLLine($line, $count, $break=PHP_EOL)
    {
        return '<'.$this->html_element.$this->renderHtmlAttributes($count).'>' .
            $break .
                $line .
            $break .
            '</'.$this->html_element.'>';
    }
}