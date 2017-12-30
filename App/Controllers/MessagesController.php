<?php
namespace App\Controllers;
use App\Controllers\DefaultController;
use App\Models\ProfilesModel;
use App\Models\MessagesModel;
use Joomla\Session\Session;
use Joomla\Event\Dispatcher;

class MessagesController extends DefaultController
{
	
	public function index()
	{
		$items = array("success"=>false);
		$profilesModel = new ProfilesModel($this->getInput(), $this->getContainer()->get('db'));
		$model = new MessagesModel($this->getInput(), $this->getContainer()->get('db'));
		$arrayUserId = $profilesModel->authenticateToken();
		if($arrayUserId["success"]){
			$items = $model->listItems(null,$arrayUserId["user_id"]);
		}
		return array('items'=>$items);
	}
	
	public function add()
	{
		$item = array("success"=>false);
		$profilesModel = new ProfilesModel($this->getInput(), $this->getContainer()->get('db'));
		$model = new MessagesModel($this->getInput(), $this->getContainer()->get('db'));
		$arrayUserId = $profilesModel->authenticateToken();
		if($arrayUserId["success"]){
			if($msg = $model->saveMessage($arrayUserId["user_id"])){
				$item["success"] = true;
				$item["msg_id"] = $msg["ddc_post_id"];
			}

		}
		return array('item'=>$item);
	}

}