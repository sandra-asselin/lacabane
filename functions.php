<?php

session_start();


// PARTIE UTILISATEURS

class userpdo
{
	
	private $id;
	public 	$email;
	public 	$password;
	public 	$password2;
	public  $grade;


		
function connectdb()
	{
	   
		$base = new PDO('mysql:host=localhost;dbname=LaCabane', 'root', '');
		return $base;
	}

public function register($email, $password, $password2)
    {
        $user = $this->connectdb()->query("SELECT *FROM utilisateurs WHERE email='$email'");
        $etat = $user->rowCount();
    
            if($password != $password2 || strlen($password) < 5)
            {
                if($password != $password2)
                {
                    $msg="Mots de passes différents";
                }
                if(strlen($password) < 5)
                {
                    $msg="Mot de passe trop court";
                }
            }
            else
            {
                if($etat== 0)
                { 
                    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);	
                    $requser =  $this->connectdb()->query("INSERT INTO utilisateurs VALUES(NULL, '$email','$hash','utilisateur')");
                    $msg="ok";
                }
                else
                {
                    $msg="Email déjà existant";
                }
            }
    
            return $msg;
    }
    public function connect($email, $password)
    {
        $user =  $this->connectdb()->query("SELECT *FROM utilisateurs WHERE email='$email'");
        $donnees = $user->fetch();
            
            if(password_verify($password,$donnees['password'])) 
            {
                $this->id=$donnees['id'];
                $this->email=$donnees['email'];
                $this->password=$donnees['password'];
                $this->grade=$donnees['grade'];
            
                $_SESSION['email']=$email;
                $_SESSION['password']=$password;
                $msg="ok";
            }
            else
            {
                $msg="Email ou mot de passe incorrect";	
            }
    
            return $msg;
    }
    
    public function disconnect()
    {
        unset($_SESSION['email']);
        unset($_SESSION['password']);
        session_destroy();
        header('location: index.php');
    }
    public function delete()
    {
        if(isset($_SESSION['email']))
        {
            include('connect.php');
            $email=$_SESSION['email'];
            $del =  $this->connectdb()->query("DELETE FROM utilisateurs WHERE email='$email'");
            session_destroy();
        }
    
    }

    public function update($email,$password)
    {	
    
    
        $log=$_SESSION['email'];
        if($_SESSION['email'] != $email)
        {			
            $user = $this->connectdb()->query("SELECT *FROM utilisateurs WHERE login='$login'");
            $etat = $user->rowCount();
            
            if($etat > 0)
            {
                $msg="erreur";	
            }
        }
        else 
        {
            if(strlen($password) >= 5)
            {
                $hash = sha1($password);
                $update =  $this->connectdb()->query("UPDATE utilisateurs SET email='$email', password='$hash' WHERE email='$email'");
            
            $this->email=$email;
            $this->password=$password;
    
            unset($_SESSION['login']);
            unset($_SESSION['password']);
            header('location: connexion.php');
            }
            else
            {
                $msg="erreur2";
            }
            
        }	
            return $msg;
    }
    public function getAllInfos()
    {
        if(isset($_SESSION['email']))
        {
            $tab=[];
            $email=$_SESSION['email'];
            $infos =  $this->connectdb()->query("SELECT *FROM utilisateurs WHERE email='$email'");
            
            while($parameter = $infos->fetch())
            {
                array_push($tab, $parameter);
            }
            
            return $tab;
        }
        else
        {
    
            return "Aucun utilisateur n'est connecté";
        }
    }
}        