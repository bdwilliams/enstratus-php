<?php
/**
* Enstratus PHP Wrapper
*
* @link https://github.com/bdwilliams
* @version 0.1-dev
*/
class Enstratus
{
	public static $api_key;
	public static $secret_key;
	public static $environment;  // dev or prod
	public static $api_endpoint;

	private static $default_user_agent = 'PHPWrapper';
	private static $signature;
	private static $timestamp;

	/**
	* Constructor
	*
	* @return void
	*/
	public function __construct()
	{
		$this->timestamp = time()*1000;
	}

	/**
	* A region is a logical sub-infrastructure within a cloud. 
	* All clouds have at least one region even if the underlying cloud provider does not define such a concept in its own API. 
	* If a cloud provider has multiple regions, they behave almost as if they were in different clouds with very limited ability 
	* to share resources between them.
	*
	* @param integer $id Region ID
	* @return response array
	*/
	public function getRegions($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-07-15/geography/Region/".$id) : $this->getRequest("/api/enstratus/2011-07-15/geography/Region");
	}
	
	/**
	* A role defines a common set of permissions that govern access into a given account. 
	* Roles are defined at the customer level and then mapped to groups at the account level. 
	* Groups may consequently have different roles in different accounts.
	*
	* @param integer $id Role ID
	* @return response array
	*/
	public function getRoles($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/admin/Role/".$id) : $this->getRequest("/api/enstratus/2011-02-24/admin/Role");
	}

	/**
	* A user within the enStratus environment. 
	* User access to various parts of enStratus is governed by the role associations with the groups to which they belong. 
	* A user belongs to a specific customer, but they may have access to accounts belonging to other customers. 
	* The ability to modify users may be limited for customers that tie enStratus into an ActiveDirectory or LDAP server.
	*
	* @param integer $id User ID
	* @return response array
	*/
	public function getUsers($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/admin/User/".$id) : $this->getRequest("/api/enstratus/2011-02-24/admin/User");
	}

	/**
	* A snapshot is a point-in-time snapshot of a volume.
	*
	* @param integer $id Snapshot ID
	* @return response array
	*/
	public function getSnapshots($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/infrastructure/Snapshot/".$id) : $this->getRequest("/api/enstratus/2011-02-24/infrastructure/Snapshot");
	}

	/**
	* A volume is a block storage device that may be mounted by servers, yet they have an existence independent of virtual servers.
	*
	* @param integer $id Volume ID
	* @return response array
	*/
	public function getVolumes($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/infrastructure/Volume/".$id) : $this->getRequest("/api/enstratus/2011-02-24/infrastructure/Volume");
	}
	
	/**
	* The customer resource represents the overarching enStratus customer account. 
	* A customer may have any number of actual accounts associated with them. 
	* Each account is billed separately, but share customer-wide resources like users, billing codes, 
	* standard networks, standard ports, and more.
	* Your API keys can see only one customer record. So there is no difference between a query on the general Customer resource 
	* and a query on your customer ID.
	*
	* @param integer $id Billing Code ID
	* @return response array
	*/
	public function getCustomers()
	{
		return $this->getRequest("/api/enstratus/2011-02-24/admin/Customer");
	}

	/**
	* A group is a collection of users. Groups map to roles that define what their access rights are for a specific account. 
	* Some operations may not be possible if your groups are being managed in LDAP or ActiveDirectory as enStratus is not the 
	* authority for that data in those instances.
	*
	* @param integer $id Group ID
	* @return response array
	*/
	public function getGroups($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/admin/Group/".$id) : $this->getRequest("/api/enstratus/2011-02-24/admin/Group");
	}
		
	/**
	* A billing code is a budget item with optional hard and soft quotas against which cloud resources may be provisioned and tracked.
	*
	* @param integer $id Billing Code ID
	* @return response array
	*/
	public function getBillingCodes($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/admin/BillingCode/".$id) : $this->getRequest("/api/enstratus/2011-02-24/admin/BillingCode");
	}

	/**
	* A firewall manages rules for filtering traffic passing through a virtual firewall, either ingress, egress, or both.
	*
	* @param integer $id Firewall ID
	* @return response array
	*/
	public function getFirewalls($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/network/Firewall/".$id) : $this->getRequest("/api/enstratus/2011-02-24/network/Firewall");
	}
		
	/**
	* A job is an asynchronous process resulting from a client request that resulted in a 202 ACCEPTED response. 
	* If the client cares about the ultimate result of the original request, it can query for the job returned in 
	* the initial response until the job completes.
	*
	* @param integer $id Job ID
	* @return response array
	*/
	public function getJobs($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/admin/Job/".$id) : $this->getRequest("/api/enstratus/2011-02-24/admin/Job");
	}

	/**
	* enStratus models two distinct kinds of networks as network resources:
	* • Standard networks such as an AWS VPC or Cloud.com network that represent a network as known to a cloud provider
	* • Overlay networks such as a VPNCubed, CloudSwitch, or vCider network in which the network is an overlay on top of the cloud provider’s 
	*	network enStratus works to manage these distinct networks so that other parts of your cloud infrastructure do not need to know which is which.
	* Whether or not your cloud has a “network” concept, you can always create overlay networks. The cloud must, however, 
	* specifically support standard networks in order for you to reference them in a specific cloud. Furthermore, not all clouds support 
	* dynamic creation of standard networks. enStratus provides plenty of meta-data so you can dynamically discover what you can do with 
	* different clouds.
	*
	* @param integer $id Network ID
	* @return response array
	*/
	public function getNetworks($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-07-15/network/Network/".$id) : $this->getRequest("/api/enstratus/2011-07-15/network/Network");
	}
		
	/**
	* A configuration management option is an option people configuring deployments and launching servers can select to support 
	* configuration management activities. A typical enStratus installation will have any number of configuration management 
	* systems (Chef, Puppet, etc.) installed. enStratus also supports customer-owned configuration management systems as well. 
	* A configuration management option is simply a realization of one of these systems for a specific needs. For example, you 
	* might set up two Chef repositories—one in the OpsCode platform and one inside your data center. You would therefore two 
	* configuration management options in enStratus tied to the single Chef configuration management system.
	*
	* @param integer $id Configuration Management ID
	* @return response array
	*/
	public function getConfigurationManagement($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-07-15/automation/ConfigurationManagementOption/".$id) : $this->getRequest("/api/enstratus/2011-07-15/automation/ConfigurationManagementOption");
	}
		
	/**
	* Server analytics represent the performance of an individual server over a specified period of time. 
	* A server analytics object defines a period start, end, and data capture interval and then provides data points in 
	* support for those parameters.
	*
	* @param integer $id Server ID
	* @return response array | error
	*/	
	public function getServerAnalytics($id = NULL)
	{
		return ($id !== NULL) ? $this->getRequest("/api/enstratus/2011-07-15/analytics/ServerAnalytics/".$id) : "Invalid Server ID Specified.";
	}
		
	/**
	* A machine image is the baseline image or template from which virtual machines may be provisioned. 
	* Some clouds allow machine image/template sharing. In those clouds, enStratus creates multiple machine image 
	* records referencing the shared machine image object to enable users to maintain separate meta-data over those shared images.
	*
	* @param integer $regionId Region ID
	* @return response array | error
	*/	
	public function getMachineImage($regionId = NULL)
	{
		$data = ($regionId !== NULL) ? "?regionId=".$regionId : NULL;
		return ($data !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/infrastructure/MachineImage", "GET", "json", $data) : "Invalid Region ID Specified.";
	}

	/**
	* A firewall rule is an ingress or egress permission that grants the right for traffic from or to a specific network CIDR.
	*
	* @param integer $firewallId Firewall ID
	* @return response array | error
	*/	
	public function getFirewallRules($firewallId = NULL)
	{
		$data = ($firewallId !== NULL) ? "?firewallId=".$firewallId : NULL;
		return ($data !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/network/FirewallRule", "GET", "json", $data) : "Invalid Firewall ID Specified.";
	}
		
	/**
	* A server is a virtual machine running within a data center. In clouds/virtualization environments in which machine 
	* images are simply special template virtual machines, those items are excluded from any list of servers and treated 
	* by the enStratus API as a machine image.
	*
	* @param integer $regionId Region ID
	* @return response array | error
	*/	
	public function getServers($regionId = NULL)
	{
		$data = ($regionId !== NULL) ? "?regionId=".$regionId : NULL;
		return ($data !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/infrastructure/Server", "GET", "json", $data) : "Invalid Server ID Specified.";
	}

	/**
	* A blob or container in which blobs are stored in a cloud storage system.
	*
	* @param integer $regionId Region ID
	* @return response array | error
	*/	
	public function getStorageObject($regionId = NULL)
	{
		$data = ($regionId !== NULL) ? "?regionId=".$regionId : NULL;
		return ($data !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/StorageObject", "GET", "json", $data) : "Invalid Region ID Specified.";
	}

	/**
	* A data center is a part of a regional infrastructure that has some ability to share resources with other data centers in the same region. 
	* All active regions have at least one data center. Depending on the underlying cloud, a data center may be as simple as a VMware cluster 
	* or as complex as an AWS availability zone.
	*
	* @param integer $regionId Region ID
	* @return response array | error
	*/	
	public function getDatacenters($regionId = NULL)
	{
		$data = ($regionId !== NULL) ? "?regionId=".$regionId : NULL;
		return ($data !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/geography/DataCenter", "GET", "json", $data) : "Invalid Region ID Specified.";
	}
		
	/**
	* Server products represent the available options and pricing for launching a virtual machine. 
	* The current implementation of this resource in the enStratus API is a temporary implementation that will be replaced early in 2011. 
	* You’re code should thus use this API today simply to get a list of products available for a specific MachineImage or for a target 
	* Architecture. If you do that and you do not rely on the fact that the product IDs today match up with the cloud product IDs, your 
	* code will survive unchanged in the future update. In the future, product IDs will be numeric and ServerProduct instances will include 
	* pricing information and other relationships.
	*
	* @param integer $regionId Region ID
	* @return response array | error
	*/	
	public function getServerProducts($regionId = NULL)
	{
		$data = ($regionId !== NULL) ? "?regionId=".$regionId : NULL;
		return ($data !== NULL) ? $this->getRequest("/api/enstratus/2011-02-24/infrastructure/ServerProduct", "GET", "json", $data) : "Invalid Server Product ID Specified.";
	}
		
	/**
	* CURL GET request
	*
	* @param string $uri API access url
	* @param string $format JSON|XML
	* @param string $data query string
	* @return response array
	*/
	public function getRequest($uri, $method = 'GET', $format = 'json', $data = NULL)
	{
		$string = $this->api_key.":".$method.":".$uri.":".$this->timestamp.":".self::$default_user_agent;
		$this->signature = base64_encode(hash_hmac('sha256', utf8_encode($string), utf8_encode($this->secret_key), true));
		
		$c = curl_init();
		curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_HTTPHEADER, 
						array('User-Agent: '.self::$default_user_agent, 
							'Accept: application/'.$format, 
							'Host: api.enstratus.com', 
							'x-es-details: basic', 
							'x-es-with-perms: false', 
							'x-esauth-access: '.$this->api_key, 
							'x-esauth-signature: '.$this->signature, 
							'x-esauth-timestamp: '.$this->timestamp));
		curl_setopt($c, CURLOPT_URL, $this->api_endpoint.$uri.$data);
		
		$ch = curl_exec($c);
		
		if ($ch === false)
		{
			echo "Error: ".curl_error($c);
		}
		else
		{
			if ($format == 'json')
			{
				return json_decode($ch);
			}
			else
			{
				$result = simplexml_load_string($ch);
				$json = json_encode($result);
				return json_decode($json, TRUE);
			}
		}
	}
}
?>