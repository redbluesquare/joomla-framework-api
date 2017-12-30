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
  		$query->select('u.ddc_user_id, u.username, u.first_name, u.last_name, u.email, u.password')
  			->from($this->db->quoteName('#__ddc_users', 'u'))
  			->group('u.ddc_user_id');
		return $query;
	}	
	protected function _buildWhere(&$query, $val)
	{
		if(is_int($val) > 0)
		{
			$query->where('u.ddc_user_id = '.(int)$val);
		}
		if($this->input->get('myId', null)!=null)
		{
			$query->where('u.ddc_user_id = '.(int)$this->input->get('myId', null));
		}
		
		return $query;
	}

	public function register()
	{
		if(($this->input->getMethod()=='POST') && ($this->_token == $this->input->get("apptoken", null,'string')))
		{
			$fname = $this->input->get("fname", null,'string');
			$lname = $this->input->get("lname", null,'string');
			$email = $this->input->get("email", null,'string');
			$username = $this->input->get("username", null,'string');
			$password = $this->input->get("password", null, "string");
			$password1 = $this->input->get("password1", 1, "string");
			if($password != $password1)
			{
				$obj = array("success" => false, "msg" => "passwords do not match");
				return $obj;
			}
			$date = date("Y-m-d H:i:s");
			$options = [
					'cost' => 8,
			];

			$data = array(
					$fname,
					$lname,
					$username,
					$email,
					password_hash($password, PASSWORD_BCRYPT,$options),
					$date,
					json_encode(array('authentication'=>base64_encode(password_hash($password, PASSWORD_BCRYPT,$options))))
			);
			if($this->getUser(null,$email,null) != false)
			{
				$obj = array("success" => false, "msg" => "user already exists");
			}
			else {
				//Save user
				$columns = array("first_name","last_name", "username", "email", "password", "registerDate", "params" );
				$result = $this->insert("#__ddc_users", $columns, $data);
				$obj = $result;
			}
			
		}else 
		{
			$obj = array("success" => false, "msg" => "request did not authenticate");
		}
	
		return $obj;
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
		if(($username!=null) && ($email == false))
		{
			$user = $this->getItemByAlias($username);
		}
		
		return $user;
	}

	public function user_login(){
		if(($this->input->getMethod()=='POST') && ($this->_token == $this->input->get("apptoken", null,'string')))
		{
			$username = $this->input->get("username", null,'string');
			$password = $this->input->get("sk", null, "string");

			if($user = $this->getUser(null,null,$username)){
				$result = password_verify($password,$user->password);
				return $result;
			}

			return false;
		}
	}

	public function createUserToken(){
		$result = array("success"=>false);
		$username = $this->input->get("username", null,'string');
		$user = $this->getUser(null,null,$username);
		$token = $this->randStrGen(20);
		$series = $this->randStrGen(20);
		$columns = array("user_id","token", "series", "invalid", "time", "uastring" );
		$tokenTime = strtotime(Date('Y-m-d H:i:s'))+(3600*24);
		$data = array($user->ddc_user_id,$token,$this->randStrGen(20),0,$tokenTime,"ddcjfa" );
		if($this->insert("#__ddc_user_keys", $columns, $data)){
			$result["success"] = true;
			$result["user_id"]= $user->ddc_user_id;
			$result["token"]= $token;
			$result["first_name"]= $user->first_name;
			$result["last_name"]= $user->last_name;
			$result["email"]= $user->email;
			$result["tokenExpiry"]= $tokenTime;
		}

		return $result;
	}

	public function authenticateToken()
	{
		$result = array("success"=>false);
		if(($this->input->getMethod()=='POST') && ($this->_token == $this->input->get("apptoken", null,'string')))
		{
			$token = $this->input->get("token", null,'string');
			$message = $this->input->get("message", null,'string');
			
			$query = $this->db->getQuery(true);
			$query->select('uk.user_id')
				->from($this->db->quoteName('#__ddc_user_keys', 'uk'))
				->group('uk.ddc_user_key_id')
				->where('uk.token = "'. $token . '"');
			$this->db->setQuery($query);
			if($item = $this->db->loadObject()){
				$result["user_id"] = $item->user_id;
				$result["success"] = true;
			}
		}
		
		return $result;
	}
	
}