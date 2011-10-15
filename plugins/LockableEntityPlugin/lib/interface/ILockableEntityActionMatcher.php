<?php

/**
 * Public interface of action matcher.
 *
 * @author sakfa
 */
interface ILockableEntityActionMatcher
{
    /**
     *  Should return true if given $module and $action needs acquiring lock.
     */
    public function actionMatches($module, $action);
}


