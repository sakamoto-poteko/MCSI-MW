<?php
/**
 * Hooks for Moegirl Client Service Infrastructure extension
 *
 * @file
 * @ingroup Extensions
 */

class MCSIHooks {
    public static function onSendWatchlistEmailNotification($watchingUser, $title, $this)
    {
        return true;
    }
    
    public static function onPageContentSaveComplete($article, $user, $content, $summary, $isMinor, $isWatch, $section, $flags, $revision, $status, $baseRevId)
    {
        $pageTitle = $article->getTitle();
        
        if (!self::shouldInvalidate($pageTitle)) {
            // Cache not applicable
            return true;
        }

        if (self::isTemplate($pageTitle)) {
            MCSIBackendReq::invalidateTemplateCache(substr($pageTitle, 9));
        } else {
            MCSIBackendReq::invalidatePageCache($pageTitle);
        }
        
        return true;
    }
    
    public static function onArticleDeleteComplete(&$article, &$user, $reason, $id, $content, $logEntry) 
    {
        $pageTitle = $article->getTitle();
        
        if (!self::shouldInvalidate($pageTitle)) {
            // Cache not applicable
            return true;
        }
    
        MCSIBackendReq::deletePageCache($pageTitle);
        return true;
    }

    public static function onTitleMoveComplete(&$title, &$newtitle, &$user, $oldid, $newid, $reason = null) 
    {
        if (!self::shouldInvalidate($title)) {
            return true;
        }
        
        
        MCSIBackendReq::deletePageCache($title);
        if (self::isTemplate($newtitle)) {
            MCSIBackendReq::invalidatePageCache($newtitle);    
        } else {
            MCSIBackendReq::invalidateTemplateCache(substr($newtitle, 9));
        }
        
        return true;
    }

    
/**
 * @brief Determine if title is a template
 *
 * @return bool True if is template
 */
    private static function isTemplate($title)
    {
        if (substr_compare("Template:", $title, 0, 9) === 0) {
            return true;
        } else {
            return false;
        }   
    }
    
    private static function shouldInvalidate($title)
    {
        if (substr_compare("Special:", $title, 0, 8) === 0 
        || substr_compare("Category:", $title, 0, 9) === 0) {
            return false;
        } else {
            return true;
        }
    }
}
