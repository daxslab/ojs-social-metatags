<?php

/**
 * @file SocialMetatags.inc.php
 *
 * Copyright (c) 2016 Carlos Cesar Caballero Diaz
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_socialMetatags
 * @brief Wrapper for Social Metatags generic plugin.
 *
 */

class SocialMetatags {
    
    
    /**
     * Get article social meta tags
     * @param $articleId Id of current article
     * @return social tags string
     */
    function getArticleSocialMetaTags($articleId){        
        $journal = Request::getJournal();
        
        $articleDao = &DAORegistry::getDAO('ArticleDAO');
        $article = $articleDao->getArticle($articleId);
        
        $title = $article->getArticleTitle();
        $type = 'article';
        $url = Request::url('article', 'view', $articleId);             
        
               
        if ($article->getLocalizedFilename()){
            import('classes.file.PublicFileManager');
            $publicFileManager = new PublicFileManager();
            $coverPagePath = Request::getBaseUrl() . '/';
            $coverPagePath .= $publicFileManager->getJournalFilesPath($journal->getId()) . '/';
            
            $articleImage = $coverPagePath . $article->getLocalizedFilename();            
        } else{
            $articleImage = NULL;
        }
        
        $description = str_replace('"', '', $article->getLocalizedAbstract());        
        $description = strip_tags($description);
        $description = $this->truncate($description, 140);
        
        $tagsStr = "\n\n<!-- Open Graph -->\n";
        
        $tagsStr .= '<meta property="og:title" content="'.str_replace('"', '', $title).'" />'."\n";
        $tagsStr .= '<meta property="og:type" content="'.$type.'" />'."\n";
        $tagsStr .= '<meta property="og:url" content="'.$url.'" />'."\n";
        $tagsStr .= '<meta property="og:description" content="'. $description .'" />'."\n";
        
        if ($articleImage) {
            $tagsStr .= '<meta property="og:image" content="' . $articleImage . '" />'."\n";
        }
        
        $tagsStr .= "\n\n<!-- Twitter Cards -->\n";
        
        $tagsStr .= '<meta name="twitter:card" content="summary" />'."\n";
        $tagsStr .= '<meta name="twitter:title" content="'.str_replace('"', '', $title).'" />'."\n";
        $tagsStr .= '<meta name="twitter:description" content="'. $description .'" />'."\n";
        
        if ($articleImage) {
            $tagsStr .= '<meta name="twitter:image" content="' . $articleImage . '" />'."\n";
        }
        

        return $tagsStr;
    }
    
    /**
     * Get pages social meta tags
     * @return social tags string
     */
    function getPageSocialMetaTags(){
        $journal = Request::getJournal();
        
        $title = $journal->getLocalizedTitle();
        $type = 'webpage';
        $url = Request::url();
        
        $description = str_replace('"', '', $journal->getLocalizedDescription());        
        $description = strip_tags($description);
        $description = $this->truncate($description, 140);
        
        if ($journal->getLocalizedPageHeaderLogo()){
            import('classes.file.PublicFileManager');
            $publicFileManager = new PublicFileManager();
            $coverPagePath = Request::getBaseUrl() . '/';
            $coverPagePath .= $publicFileManager->getJournalFilesPath($journal->getId()) . '/';
            $pageHeaderLogo = $journal->getLocalizedPageHeaderLogo();
            $journalImage = $coverPagePath . $pageHeaderLogo['uploadName'];
        } elseif ($journal->getLocalizedPageHeaderTitle()) {            
            import('classes.file.PublicFileManager');
            $publicFileManager = new PublicFileManager();
            $coverPagePath = Request::getBaseUrl() . '/';
            $coverPagePath .= $publicFileManager->getJournalFilesPath($journal->getId()) . '/';
            $pageHeaderTitle = $journal->getLocalizedPageHeaderTitle();
            $journalImage = $coverPagePath . $pageHeaderTitle['uploadName'];
        } else {
            $journalImage = NULL;
        }
        
        $tagsStr = "\n\n<!-- Open Graph -->\n";
        $tagsStr .= '<meta property="og:title" content="'.$title.'" />'."\n";
        $tagsStr .= '<meta property="og:type" content="'.$type.'" />'."\n";
        $tagsStr .= '<meta property="og:url" content="'.$url.'" />'."\n";
        $tagsStr .= '<meta property="og:description" content="'. $description .'" />'."\n";
        if ($journalImage) {
            $tagsStr .= '<meta property="og:image" content="' . $journalImage . '" />'."\n";
        }
        
        $tagsStr .= "\n\n<!-- Twitter Cards -->\n";
        
        $tagsStr .= '<meta name="twitter:card" content="summary" />'."\n";
        $tagsStr .= '<meta name="twitter:title" content="'.str_replace('"', '', $title).'" />'."\n";
        $tagsStr .= '<meta name="twitter:description" content="'. $description .'" />'."\n";
        
        if ($journalImage) {
            $tagsStr .= '<meta name="twitter:image" content="' . $journalImage . '" />'."\n";
        }
        
        return $tagsStr;
    }
    
    /**
     * Get announcement social meta tags
     * @param $announcementId Id of current announcement
     * @return social tags string
     */
    function getAnnouncementSocialMetaTags($announcementId){
        $journal = Request::getJournal();
        $announcementDao = &DAORegistry::getDAO('AnnouncementDAO');
        $announcement = $announcementDao->getAnnouncement($announcementId);
        
        $title = $announcement->getLocalizedTitle();
        $type = 'article';
        $url = Request::url('announcement', 'view', $announcementId);
        
        $description = str_replace('"', '', $announcement->getLocalizedDescription());      
        $description = strip_tags($description);
        $description = $this->truncate($description, 140);
        
        $tagsStr = "\n\n<!-- Open Graph -->\n";
        $tagsStr .= '<meta property="og:title" content="'.$title.'" />'."\n";
        $tagsStr .= '<meta property="og:type" content="'.$type.'" />'."\n";
        $tagsStr .= '<meta property="og:url" content="'.$url.'" />'."\n";
        $tagsStr .= '<meta property="og:description" content="'. $description .'" />'."\n";
        
        $tagsStr .= "\n\n<!-- Twitter Cards -->\n";
        
        $tagsStr .= '<meta name="twitter:card" content="summary" />'."\n";
        $tagsStr .= '<meta name="twitter:title" content="'.str_replace('"', '', $title).'" />'."\n";
        $tagsStr .= '<meta name="twitter:description" content="'. $description .'" />'."\n";        
        
        return $tagsStr;
    }
    
    
    /**
     * Truncate a string
     * @param $string String to truncate
     * @param $integer (optional) result characters count
     * @param $string (optional) string to append
     * @return string
     */
    function truncate($string, $length=100,$append="&hellip;") {
        $string = trim($string);

        if(strlen($string) > $length) {
          $string = wordwrap($string, $length);
          $string = explode("\n", $string, 2);
          $string = $string[0] . $append;
        }

        return $string;
    }
    
    
    
}