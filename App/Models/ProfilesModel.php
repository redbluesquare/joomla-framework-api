<?php
namespace App\Models;
use App\Models\DefaultModel;
use Joomla\Application;


class ProfilesModel extends DefaultModel
{
	protected $_published 	= 1;
	protected $_location	= null;
	protected $_token = 'ksdbvskob0vwfb8BKBKS8VSFLFFPANVVOFd1nspvpwru8r8rB72r8r928t'; 
	
	
	protected function _buildQuery()
  	{
  		$query = $this->db->getQuery(true);
  		$query->select('u.id, u.username, u.name, u.email')
  			->from($this->db->quoteName('#__users', 'u'))
  			->group('u.id');
		return $query;
	}	
	protected function _buildWhere(&$query, $val)
	{
		if(is_int($val) > 0)
		{
			$query->where('u.id = '.(int)$val);
		}
		if($this->input->get('myId', null)!=null)
		{
			$query->where('u.id = '.(int)$this->input->get('myId', null));
		}
		
		return $query;
	}
}