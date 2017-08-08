<?php
namespace App\Models;
use App\Models\DefaultModel;
use Joomla\Application;
use Joomla\Github\Package\Authorization;


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
		//$query->where('u.block = "0"');
		
		
		
		return $query;
	}

	public function getUser($id = null, $email = null, $username = null){
		
		$user = false;
		if($id!=null)
		{
			$user = $this->getItemById($id);
		}
		if(($email!=null) && ($user == false))
		{
			$user = $this->validate_user_email($email);
		}
		if(($username!=null) && ($user == false))
		{
			$user = $this->getItemByAlias($username);
		}
		
		return $user;
	}
	
	public function register(){

		if(($this->input->getMethod()=='POST') && ($this->_token == $this->input->get("apptoken", null,'string')))
		{
			$fname = $this->input->get("fname", null,'string');
			$lname = $this->input->get("lname", null,'string');
			$email = $this->input->get("email", null,'string');
			$username = $this->input->get("username", null,'string');
			$tokenID = $this->input->get("tokenID", null,'string');
			$date = date("Y-m-d H:i:s");
			$data = array(
					$fname." ".$lname,
					$username,
					$email,
					$tokenID,
					$date,
					json_encode(array('authentication'=>base64_encode($tokenID)))
			);
			if($this->getUser(null,$email,null) != false)
			{
				$obj = array("success" => false, "msg" => "user already exists");
			}
			else {
				//Save user
				$columns = array("name", "username", "email", "password", "registerDate", "params" );
				$result = $this->insert("#__users", $columns, $data);
				$obj = $result;
			}
			
		}else 
		{
			$obj = array("success" => false, "msg" => "request did not authenticate");
		}
	
		return $obj;
	}
	public function authenticate_email($user_email)
	{
		$result = array("success" => false);
		if($user = $this->validate_user_email($user_email)){
			$columns = array("user_id","token", "series", "invalid", "time", "uastring" );
			$token = $this->randStrGen(25);
			$data = array($user->id,$token,$this->randStrGen(20),0,strtotime(Date('Y-m-d H:i:s'))+(3600*24),"ddcshopbox" );
			$this->insert("#__user_keys", $columns, $data);
			$result['success'] = true;
			$result['token'] = $token;
			$result['user_id'] = $user->id;
		}
	
		return $result;
	}
	public function authenticate_token($token)
	{
		$result = array("success" => false);
		
		$query = $this->db->getQuery(true)
		->select('u.id, uk.token')
		->from('#__users as u')
		->leftjoin('#__user_keys as uk on u.id = uk.user_id')
		->where('uk.token = ' . $this->db->quote($token));
		$this->db->setQuery($query);
		$response = $this->db->loadObject();
		if($response!=null){
			$result['success'] = true;
			$result['token'] = $token;
			$result['user_id'] = $response->id;
		}

		return $result;
	}
}