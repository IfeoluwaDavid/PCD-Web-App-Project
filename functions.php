<?php

//$username = "90103518b";
//validateStudentUsername($username);

function validateStudentUsername($string)
{	
	if(strlen($string) == 9 && !preg_match('/[^a-zA-Z\d]/', $string))
	{
		$splitstring = str_split($string);
		
		if(is_numeric($splitstring[0]))
		{
			if(preg_match_all( "/[0-9]/", $string) == 9)
			{
				//echo "Valid Student ID";
				return true;
				exit();
			}
			else
			{
				//echo "Not up to 9 digits";
				return false;
				exit();
			}
		}
		else if ($splitstring[0] == n || $splitstring[0] == N)
		{
			if(preg_match_all( "/[0-9]/", $string) == 8)
			{
				//echo "Valid student ID";
				return true;
				exit();
			}
			else
			{
				//echo "Not up to 8 digits";
				return false;
				exit();
			}
		}
		else
		{
			//echo "First character problems";
			return false;
			exit();
		}
	}
	else
	{
		//echo "Length problems";
		return false;
		exit();
	}
}

function validateItemName($string)
{
	if(strlen($string) < 3 || strlen($string) > 20)
	{
		return false;
		exit();
	}
	return true;
}

function validateSerialNo($string)
{
	if(!is_numeric($string) || strlen($string) != 6)
	{
		return false;
		exit();
	}	
	return true;
}

function validateAdminUsername($string)
{
	if(strlen($string) < 6 || strlen($string) > 16 || preg_match('/[^a-zA-Z\d]/', $string))
	{
		return false;
		exit();
	}	
	return true;
}

function nameValidation($string)
{
	if(strlen($string) < 2 || strlen($string) > 10 || preg_match('/[^a-zA-Z\d]/', $string) || preg_match_all( "/[0-9]/", $string) > 0)
	{
		return false;
		exit();
	}
	
	return true;
}

function passwordValidation($string)
{
	if(strlen($string) < 8 || strlen($string) > 15)
	{
		return false;
		exit();
	}
	return true;
}

?>