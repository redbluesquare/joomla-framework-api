<?php
namespace App\Controllers;
use App\Controllers\DefaultController;
use App\Models\DashboardModel;
use Joomla\Session\Session;
use Joomla\Event\Dispatcher;

class DashboardController extends DefaultController
{
	
	public function index()
	{
		
		$model = new DashboardModel($this->getInput(), $this->getContainer()->get('db'));
		$items = $model->listItems();
		return array('items'=>$items);
	}
	
	public function edit()
	{
		$id = $this->getInput()->getString('id');
		$item = null;
		if($id!=null)
		{
			$model = new DashboardModel($this->getInput(), $this->getContainer()->get('db'));
			$item = $model->getItem($id);
		}
		
		return array('item'=>$item);
	}
}