<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter AWS S3 Integration Library
 * 
 * @package    CodeIgniter AWS S3 Integration Library
 * @author     scriptigniter <scriptigniter@gmail.com>
 * @link       http://www.scriptigniter.com/cis3demo/
 */

class cisssintegration_lib 
{
	public function __construct()
	{
		define('AWS_ACCESS_KEY',"AKIAJSZT2KCEXO7QU2DQ");
		define('AWS_SECRET_KEY',"i1gOZHeX6KzzABV6FRzuEhA+Up4F8lED0QS2J2+2");
		
		define('BUCKET_NAME','trainapp');//The bucket name you want to use for your project
		define('AWS_URL','https://'.BUCKET_NAME.'.s3.amazonaws.com/');
		
		//check AWS access key is set or not
		if(trim(AWS_ACCESS_KEY,"{}")=="AWS_ACCESS_KEY")
		{
			exit("CI S3 Integration configuration error! Please input the AWS Access Key, AWS Secret Key and Bucket Name in applicatin/libraries/cisssintegration_lib.php file");
		}
		require_once('awssdk/sdk.class.php');	
	}

	/**
     * Delete S3 Object
     *
     * @access public
     */    
	
	function delete_s3_object($file_path)
	{
		$s3     = new AmazonS3();
		$bucket_name = BUCKET_NAME;
		$try   = 1;
		$sleep = 1;
		//Try multiple times(3 times) to Delete the file if not deleted in one go by any reason.
		do
		{
			$response = $s3->delete_object($bucket_name, $file_path);
			if($response->isOK())
			{
				return true;
			}
			sleep($sleep);
			$sleep *= 2;				
		} while (++$try < 3);
		return false;
	}
	
	function copy_s3_file($source,$destination)
	{
		$s3     = new AmazonS3();
		$try   = 1;
		$sleep = 1;
		$response = $s3->copy_object($source, $destination);
		if($response->isOK())
		{
			return true;
		}
		return false;
	}
	
	function create_bucket($bucket_name="",$region="")
	{
		$s3     = new AmazonS3();
		$try   = 1;
		$sleep = 1;
		$region = $region?$region:AmazonS3::REGION_US_STANDARD;
		$response = $s3->create_bucket($bucket_name, $region);
		if($response->isOK())
		{
			return true;
		}
		return false;
	}
	
}