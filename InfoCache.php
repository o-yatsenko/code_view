<?php

namespace Classes\Mob\Components;

use Classes\Models\InfoCacheModel;

class InfoCache
{
    /**
     * Add information in cache
     * 
     * @param string $key Key for identification
     * @param mixed $info Information for adding in cache
     * @param int $expTimestamp Value expiration timeout (default 1 day)
     */
    public static function set($key, $info, $expTimestamp = 86400)
    {        
        $expiration = date(DATE_FORMAT, CURRENT_TIMESTAMP + $expTimestamp); 
              
        $infoCacheModel = new InfoCacheModel();        
        $infoCache = $infoCacheModel->findBy(['key' => $key]);
        
        if ($infoCache->id) { // info was found, so we updated
            $infoCache->info = serialize($info);
            $infoCache->expiration = $expiration;
            $infoCache->crerated = CURRENT_DATE;
            $infoCache->save();
        } else { // info was not found, so we created              
            $infoCacheModel->key = $key;
            $infoCacheModel->info = serialize($info);
            $infoCacheModel->expiration = $expiration;
            $infoCacheModel->crerated = CURRENT_DATE;
            $infoCacheModel->save();
        }
    }
    
    /**
     * Get information from cache
     * 
     * @param string $key Identifier
     * @return mixed Cached value  or null
     */
    public static function get($key)
    {
        $infoCacheModel = new InfoCacheModel();
        $infoCache = $infoCacheModel->select('info')->findBy(['key' => $key, 'expiration >' => CURRENT_DATE]);
        
        return isset($infoCache['info']) ? unserialize($infoCache['info']) : null;
    }
}
