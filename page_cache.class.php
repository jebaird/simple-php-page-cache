<?php

/**
 * @author Jesse Baird <jebaird@gmail.com>
 * @version .01 
 * @since 3/3/2009
 * @notes code/ideas based off of http://papermashup.com/caching-dynamic-php-pages-easily
 * @useage
 * <?php 
 * require_once "modules/page_cache/page_cache.class.php";
 * $cache=new page_cache(120); //cache page for 2 hours
 * //put 
 * //page
 * //content
 * //here
 * $cache->createCachedPage();
 * ?> 
 *      
 * 
 *   
 *  	
 */

class page_cache {

    var $cacheDir; //cache/dir
    var $cacheTTL; //time to cache file
    var $defaultFileName; //if no file name index.html
    var $useCacheForThisPage;//bool
    //private vars
    var $cacheName;

  /**
   * page_cache::page_cache()
   * @author Jesse Baird <jebaird@gmail.com>
   * @version .01
   * @since 3/3/2009  
   * @param bool $useCacheForThisPage | turn caching on or off
   * @param integer $cacheTTL | number of minutes that the file is to be cached, default 1 hour
   * @param string $defaultFileName | if a file is not specified then use this file ie index.php
   * @param string $cacheDir | directory that the cached file are stored, relative path to the requested file. cache/
   * @return void
   */
    function page_cache($cacheTTL = 60,$useCacheForThisPage = true, $defaultFileName = 'index.php', $cacheDir = 'cache/') {
        $this->cacheDir = $cacheDir;
        $this->cacheTTL = ($cacheTTL*60);
        $this->defaultFileName = $defaultFileName;
        $this->useCacheForThisPage = $useCacheForThisPage;
        //############################
        if($this->useCacheForThisPage == true) {
            if((isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '/')) // Is there any URI to cache
                {
                $this->cacheName = substr(str_replace('/', '-', $_SERVER['REQUEST_URI']),1); // Replacing all slashes from URI with "-" and Removing first unwanted "-" symbol
                //$this->cacheName = $this->cacheName, 1); // 
            } else {
                $this->cacheName = $this->defaultFileName; //If there are no URI, this maybe index.php
            }

            $cacheFile = $this->cacheDir . $this->cacheName; // Cache file name now is clean and we can use it

            //this next line is different than the original version
            //if the file is there and  and the cache has not timed out serve the cached the file 
            if(file_exists($cacheFile) && (time()-filemtime($cacheFile)) < $this->cacheTTL ) {
            //	echo '<br>this is cached<br>';
                include ($cacheFile);
                exit;
            }else{//i added this else statement becuase the server kept sending me the un-cached version of the page
            	//else start out put buffering so we can capture the file 
            //	echo '<br>using source file<br>';
			ob_start();	
			}
            
        }
    }
  /**
   * page_cache::createCachedPage()
   * @author Jesse Baird <jebaird@gmail.com>
   * @version .01
   * @since 3/3/2009   
   * @notes call this function at the end of the file
   * @return void
   */
    function createCachedPage() {
        if($this->useCacheForThisPage == true) {
            $cached = fopen($this->cacheDir . $this->cacheName, 'w');
            fwrite($cached, ob_get_contents());
            fclose($cached);
            ob_end_flush(); // Send the output to the browser
        }
    }
    

}


?>