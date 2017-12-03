<?php
namespace App\Controllers;

use Joomla\Controller\AbstractController;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\Container;

Class DefaultController extends AbstractController implements ContainerAwareInterface
{
	function execute()
	{
		$input = $this->getInput();
		$task = $input->get('task', 'index');
		
		if(method_exists($this, $task))
		{
			return $this->$task();
		}
		else 
		{
			echo "Method not found ";
		}
	}
	
	public function setContainer(Container $container) {
		$this->container = $container;
		return $this;
	}
	public function getContainer() {
		return $this->container;
	}
}

