<?php

/**
 * @file SocialMetatagsPlugin.inc.php
 *
 * Copyright (c) 2016 Carlos Cesar Caballero Diaz
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_socialMetatags
 * @brief Wrapper for Social Metatags generic plugin.
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');


class SocialMetatagsPlugin extends GenericPlugin{
    
    /**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
    function register($category, $path) {
        $success = parent::register($category, $path);
        $this->addLocaleData();
        
        if ($success && $this->getEnabled()) {
            // Handler for templates callbacks
            HookRegistry::register('TemplateManager::display', array(&$this, 'templateManagerCallback'));
        }

        return $success;
    }
    
    function getDisplayName() {
        return __('plugins.generic.socialMetatags.displayName');
    }

    function getDescription() {
        return __('plugins.generic.socialMetatags.description');
    }

    /**
     * Get the template path for this plugin.
     */
    function getTemplatePath() {
        return parent::getTemplatePath() . 'templates/';
    }

    /**
     * Get the handler path for this plugin.
     */
    function getHandlerPath() {
        return $this->getPluginPath() . '/pages/';
    }
    
    /**
     * Get the name of the settings file to be installed site-wide when
     * OJS is installed.
     * @return string
     */
    function getInstallSitePluginSettingsFile() {
        return $this->getPluginPath() . '/settings.xml';
    }
    
    
    /**
     * Add social tags
     */
    function templateManagerCallback($hookName, $args) {
        
        $templateMgr =& $args[0];
        $template =& $args[1];
        
        $args = Request::getRequestedArgs();        
        
        $this->import("classes.SocialMetatags");
        $socialMetatags = new SocialMetatags();       
        
        $additionalHeadData = $templateMgr->get_template_vars('additionalHeadData');
        
        if (strpos($template, "templates/article/article.tpl")){
            $articleId = $args[0];
            $additionalHeadData .= "\n".$socialMetatags->getArticleSocialMetaTags($articleId);
        } elseif (strpos($template, "templates/announcement/view.tpl")) {
            $announcementId = $args[0];
            $additionalHeadData .= "\n".$socialMetatags->getAnnouncementSocialMetaTags($announcementId);
        } else {
            $additionalHeadData .= "\n".$socialMetatags->getPageSocialMetaTags();
        }        
        
        $templateMgr->assign('additionalHeadData', $additionalHeadData);
        
    }
    
}