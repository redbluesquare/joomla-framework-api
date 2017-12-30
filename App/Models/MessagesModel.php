<?php
namespace App\Models;
use App\Models\DefaultModel;
use Joomla\Application;


class MessagesModel extends DefaultModel
{
	protected $_published 	= 1;
	protected $_location	= null;
	protected $_token = 'ksdbvskob0vwfb8BKBKS8VSFLFFPANVVOFd1nspvpwru8r8rB72r8r928t'; 
	
	
	protected function _buildQuery()
  	{
  		$query = $this->db->getQuery(true);
  		$query->select('p.*')
  			->from($this->db->quoteName('#__ddc_posts', 'p'))
  			->group('p.ddc_post_id');
		return $query;
	}	
	protected function _buildWhere(&$query, $p_id, $u_id)
	{
		if(is_int($p_id) > 0)
		{
			$query->where('p.ddc_post_id = '.(int)$p_id);
		}
		if($u_id !=null)
		{
			$query->where('p.created_by = '.(int)$u_id);
		}
		if($this->input->get('msgId', null)!=null)
		{
			$query->where('p.ddc_post_id = '.(int)$this->input->get('msgId', null));
		}
		
		return $query;
	}

	public function saveMessage($user_id){
		if(($this->input->getMethod()=='POST') && ($this->_token == $this->input->get("apptoken", null,'string')))
		{
			$message = $this->input->get("message", null,'string');
			$state = $this->input->get("state", null,'string');
			$date = date("Y-m-d H:i:s");
			$columns = array("category","post_parent", "message", "created_on", "created_by", "state" );
			$data = array(0,0,$message,$date,$user_id,1 );
			if($msg = $this->insert("#__ddc_posts", $columns, $data)){
				$result["success"] = true;
				$result["ddc_post_id"]= $msg->id;
			}
			return $result;
		}
	}
	
}