<?php
    
namespace Classes\Mob\Components;

use Classes\Mob\Mob;
use Classes\Mob\Helpers\GeneratorHelper;
use Classes\Models\GuidSessionModel;

class GuidSession 
{   
    /**
     * Added session values in DB
     * 
     * @param array $data Data which need to be added in DB
     * @return string|null Guid or null
     */
    public static function add(Array $data)
    {
        $guidSessionModel = new GuidSessionModel();
        $guidSessionModel->id = GeneratorHelper::generateGUID();
        $guidSessionModel->data = json_encode($data);
        $guidSessionModel->created = CURRENT_DATE;
        
        if ($guidSessionModel->save()) {
            return $guidSessionModel->id;
        }
        return null;
    }
    
    /**
     * Initializing session with information from DB 
     */
    public static function init()
    {
        $guid = Mob::$app->request->get('gs', '', true);
        
        if ($guid) {
            $guidSessionModel = new GuidSessionModel();
            $guidSession = $guidSessionModel->findBy(['id' => $guid]);
            
            if (empty($guidSession->data)) {//DB info was not found
                return;
            }
            
            $items = json_decode($guidSession->data, true);
            
            if (!is_array($items)) { //DB info was not decoded correctly
                return;
            }
            
            foreach ($items as $key => $value) {
                Mob::$app->session->set($key, $value);
            }
            
            //info was found and added in session, so now we can delete info from DB
            $guidSessionModel->deleteById($guid);
        }
    }
}
