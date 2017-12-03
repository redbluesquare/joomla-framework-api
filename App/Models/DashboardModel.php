<?php
namespace App\Models;
use App\Models\DefaultModel;
use Joomla\Application;
use Joomla\Github\Package\Authorization;


class DashboardModel extends DefaultModel
{
	protected $_published = 1;
	
	
	protected function _buildQuery()
  	{
  		$query = $this->db->getQuery(true);
  		$query->select('c.id, c.title, c.fulltext')
  			->from($this->db->quoteName('#__content', 'c'))
  			->group('c.id');
		return $query;
	}	
	protected function _buildWhere(&$query, $val)
	{
		if($val != null)
		{
			$query->where('c.id = '.(int)$val);
		}
		if($this->_published != null)
		{
			$query->where('c.state = '.(int)$this->_published);
		}
		
		
		
		return $query;
	}

}