<?php

namespace Warclaim\Service;

class WarclaimService
{

    /**
     * This method merges two concurrent versions into the old one
     * @param Warclaim $old The old warclaim in the db, when the form was opened
     * @param Warclaim $form The warclaim from the form submit
     * @return Tries to merge the warclaims together. If sucessfull the new warclaim is returned,
     * false otherwise
     */
    public function mergeWarclaims($old, $form)
    {
        if ($old->getSize() !== $form->getSize()) {
            return false;
        }

        $merged      = $form;
        $assignments = $merged->getAssignments();
        $cleanup     = $merged->getCleanup();
        $info        = $merged->getInfo();

        for ($i = 0; $i < $old->getSize(); $i++) {
            //Check assignment
            if (!trim($assignments[$i]) && trim($old->getAssignments()[$i])) {
                $assignments[$i] = trim($old->getAssignments()[$i]);
            }

            //Check cleanup
            if (!trim($cleanup[$i]) && trim($old->getCleanup()[$i])) {
                $cleanup[$i] = trim($old->getCleanup()[$i]);
            }

            //Check info
            if (!trim($info[$i]) && trim($old->getInfo()[$i])) {
                $info[$i] = trim($old->getInfo()[$i]);
            }

        }
        $merged->setAssignments($assignments);
        $merged->setCleanup($cleanup);
        $merged->setInfo($info);

        return $merged;
    }
}
