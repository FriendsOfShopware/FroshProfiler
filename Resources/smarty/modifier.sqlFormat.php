<?php

function smarty_modifier_sqlFormat($var)
{
    return SqlFormatter::format($var);
}
