<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 8/15/18
 * Time: 11:05 AM
 */

namespace LCI\Salsify\Helpers;


trait Syntax
{

    /**
     * @param string $name
     * @return string
     */
    protected function makeStudyCase($name)
    {
        $StudyName = '';
        $parts = explode('_', $name);
        foreach ($parts as $part) {
            $StudyName .= ucfirst($part);
        }
        return $StudyName;
    }
}