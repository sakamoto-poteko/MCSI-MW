<?php
/**
 * Hooks for Moegirl Client Service Infrastructure extension
 *
 * @file
 * @ingroup Extensions
 */

class MCSIHooks { 
    public static function onPageContentSaveComplete($article, $user, $content, $summary, $isMinor, $isWatch, $section, $flags, $revision, $status = null, $baseRevId = null)
    {
        $title = $article->getTitle();
        
        if (!self::shouldInvalidate($title)) {
            // Cache not applicable
            return true;
        }

        if (self::isTemplate($title)) {
            MCSIBackendReq::invalidateTemplateCache($title->getText());
        } else {
            MCSIBackendReq::invalidatePageCache($title->getFullText());
        }
        self::notifyWatchlist($title, "Edit", $summary, $isMinor);
        return true;
    }
    
    public static function onArticleDeleteComplete(&$article, &$user, $reason, $id, $content, $logEntry) 
    {
        $title = $article->getTitle();
        
        if (!self::shouldInvalidate($title)) {
            // Cache not applicable
            return true;
        }
    
        MCSIBackendReq::deletePageCache($title->getFullText());
        self::notifyWatchlist($title, "Delete", $reason);
        return true;
    }

    public static function onTitleMoveComplete(&$title, &$newtitle, &$user, $oldid, $newid, $reason = null) 
    {
        if (!self::shouldInvalidate($title)) {
            return true;
        }
        
        if (self::isTemplate($title)) {
            MCSIBackendReq::invalidateTemplateCache($title->getText());
        } else {
            MCSIBackendReq::invalidatePageCache($title->getFullText());
        }
        
        if (self::isTemplate($newtitle)) {
            MCSIBackendReq::invalidateTemplateCache($newtitle->getText());
        } else {
            MCSIBackendReq::invalidatePageCache($newtitle->getFullText());
        }
        
        self::notifyWatchlist($title, "Move", $reason);
        return true;
    }

    
/**
 * @brief Determine if title is a template
 *
 * @return bool True if is template
 */
    private static function isTemplate($title)
    {
        if ("Template" === $title->getSubjectNsText()) {
            return true;
        } else {
            return false;
        }
    }
    
    private static function shouldInvalidate($title)
    {
        if ("Special" === $title->getSubjectNsText() || "Category" === $title->getSubjectNsText()) {
            return false;
        } else {
            return true;
        }
    }
    
    private static function notifyWatchlist($title, $action, $summary = null, $isMinor = false)
    {
        $watchingUsers = self::getUserWatchingThePage($title);
        $info = array(
            "Title" => $title->getFullText(),
            "ArticleId" => $title->getArticleID(),
            "Users" => $watchingUsers,
            "Action" => $action,
            "Summary" => $summary,
            "IsMinor" => $isMinor
        );
        
        $json = json_encode($info);
        MCSIBackendReq::notifyWatchlist($json);
    }
    
    private static function getUserWatchingThePage($title)
    {
        $dbr = wfGetDB(DB_SLAVE);
        
        $users = array();
        
        $res = $dbr->select(array('user', 'watchlist'),
                            array('user_name'),
                            array('wl_namespace' => $title->getNamespace(),
                                  'wl_title' => $title->getDBKey()),
                            __METHOD__,
                            array(),
                            array('watchlist' => array('INNER JOIN', array('wl_user=user_id'))));
        
        foreach($res as $row) {
            $users[] = $row->user_name;
        }
        
        return $users;
    }
    
}



