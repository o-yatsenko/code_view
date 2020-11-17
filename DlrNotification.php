<?php

namespace Classes\Mob\Components;

use Classes\Mob\Components\Constant\DlrNotificationConst;
use Classes\Models\DlrNotificationsModel;
use Classes\Models\DlrNotificationsOldModel;

class DlrNotification 
{   
    /**
     * Add notification  in DB
     *         
     * @param integer $type Notification type
     * @param mixed $data Notification data
     * @param integer $memberId User id
     * @param integer $status Notification status
    */
    public function add($type, $data, $memberId = 0, $status = DlrNotificationConst::DLR_NOTIFICATION_STATUS_READY)
    {   
        $dlrNotificationsModel = new DlrNotificationsModel();

        $fields = [
            'member_id' => $memberId,
            'type'      => $type,
            'data'      => json_encode($data),
            'status'    => $status,
            'job_no'    => 0,
            'updated'   => '',
            'created'   => CURRENT_DATE
        ];

        $dlrNotificationsModel->setAttributes($fields);
        $dlrNotificationsModel->save();
    }

    /**
     * Get notifications by parameters
     * 
     * @param integer $type Notification type
     * @param integer $status Notification status
     * @param integer $limit Limit
     * @return array Array with notifications
    */
    public function get($type, $status, $limit = 1000)
    {   
        $dlrNotificationsModel = new DlrNotificationsModel();

        $fields = [
            'type'      => (int)$type,
            'status'    => (int)$status
        ];

        return $dlrNotificationsModel->select('*')->limit($limit)->findAllBy($fields);
    }

    /**
     * Delete notification by id
     * 
     * @param integer $id Notification id
     * @return bool Status of deletion
     */
    public function delete($id)
    {
        $dlrNotificationsModel = new DlrNotificationsModel();
        
        return $dlrNotificationsModel->deleteById($id);
    }

    /**
     * Mark notification after processing by id
     * 
     * @param integer $id Notification id
     * @return bool Status of marking
     */
    public function mark($id)
    {
        $dlrNotificationsModel = new DlrNotificationsModel();
        $dlrNotification = $dlrNotificationsModel->findById($id);

        if ($dlrNotification->id) { //notification was found
            $dlrNotification->status = DlrNotificationConst::DLR_NOTIFICATION_STATUS_SUCCESSFUL;
            $dlrNotification->updated = CURRENT_DATE;
            
            return $dlrNotification->save();
        }
        
        return false;
    }

    /**
     * Move notification in history table
     * 
     * @param integer $id Notification id
     * @return bool Status of moving
     */
    public function move($id)
    {
        $dlrNotificationsModel = new DlrNotificationsModel();
        $dlrNotification = $dlrNotificationsModel->select('*')->findBy(['id' => $id]);

        if ($dlrNotification) { //notification was found
            unset($dlrNotification['id']);
            
            $dlrNotificationsOldModel = new DlrNotificationsOldModel();
            $dlrNotificationsOldModel->setAttributes($dlrNotification);
            
            return  $dlrNotificationsOldModel->save();
        }
        
        return false;
    }
}
