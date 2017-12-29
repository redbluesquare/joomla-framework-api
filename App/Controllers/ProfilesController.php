<?php
namespace App\Controllers;
use App\Controllers\DefaultController;
use App\Models\ProfilesModel;
use Joomla\Session\Session;
use Joomla\Event\Dispatcher;

class ProfilesController extends DefaultController
{
	
	public function index()
	{
		
		$model = new ProfilesModel($this->getInput(), $this->getContainer()->get('db'));
		$items = $model->listItems();
		return array('items'=>$items);
	}
	
	public function register()
	{
		$model = new ProfilesModel($this->getInput(), $this->getContainer()->get('db'));
		$item = $model->register();
		
		return array('item'=>$item);
	}

	public function login()
	{
		$model = new ProfilesModel($this->getInput(), $this->getContainer()->get('db'));
		$item = array("succes"=>false);
		if($model->user_login()){
			$item = $model->createUserToken();
		}
		
		return array('item'=>$item);
	}
}