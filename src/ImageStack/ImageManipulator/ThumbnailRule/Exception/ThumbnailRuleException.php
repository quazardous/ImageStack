<?php
namespace ImageStack\ImageManipulator\ThumbnailRule\Exception;

use ImageStack\ImageManipulator\Exception\ImageManipulatorException;

class ThumbnailRuleException extends ImageManipulatorException
{
    const UNSUPPORTED_RULE_FORMAT = 101;
    const IMAGINE_NOT_SETUP = 102;
}