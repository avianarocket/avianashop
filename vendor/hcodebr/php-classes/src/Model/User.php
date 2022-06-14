<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
use Rain\Tpl\Exception;

class User extends Model {

	const SESSION = "User";
	const SECRET = "HcodePhp7_Secret";
	const SECRET_IV = "HcodePhp7_Secret_IV";
	const ERROR = "UserError";
	const ERROR_REGISTER = "UserErrorRegister";
	const SUCCESS = "UserSucesss";

	public static function getFromSession()
	{

		$user = new User();

		if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0) {

			$user->setData($_SESSION[User::SESSION]);

		}

		return $user;

	}

    public static function checkLogin($inadmin = true)
	{

		if (
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		) {
			//Não está logado
			return false;

		} else {

			if ($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true) {

				return true;

			} else if ($inadmin === false) {

				return true;

			} else {

				return false;

			}

		}

	}

    //recebendo dados do formulario de login
    public static function login($login, $password)
    {
        //verificar se os dados do ususario estao no banco mesmo
        $sql = new Sql();
        //guardando daos na variavel results apos execultar a consulta
        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson WHERE a.deslogin = :LOGIN", array(
            ":LOGIN"=>$login
           ));
        //verificar se retornou dados do login
        if (count($results) === 0) {
            //mostra erro 
            throw new Exception("Usuário o senha invádo(os)", 1);            
        }
        //pegando o dados na posição 0 do retorno e salvando em $data
        $data = $results[0];
        //verificando a senha
        if(password_verify($password, $data["despassword"]) === true)
        {
            //cria novo objeto User e salva na variavel $user
            $user = new User();
            //metodo que carrega todos os dados do banco
            $user->setData($data);
            //jatemos os dados agora criaremos uma sessao de usuario
            $_SESSION[User::SESSION] = $user->getValues();
            //retornar os dados para usar futuramente
            return $user;

        //se der errado... joga erro    
        } else {
            throw new Exception("Usuário o senha invádo(os)", 1);
            
        }
    }

    //verifica se usuario esta logado
    public static function verifyLogin($inadmin = true)
	{

		if (!User::checkLogin($inadmin)) {

			if ($inadmin) {
				header("Location: /admin/login");
			} else {
				header("Location: /login");
			}
			exit;

		}

	}

    //deslogando usuario
    public static function logout()
    {
        //linmpando a SESSION
        $_SESSION[User::SESSION] = null;
    }

    //metodo para listar todos os usuarios | unindo duas tabelas com JOIN A E B
    public static function listAll()
    {
        //chamar o classe Sql
        $sql = new Sql();
        //SELECT
        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

    }

    //metodo para salvar usuarios no banco
    public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>(int)$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

	}

    //metodo para pegar o id usuario
    public function get($iduser)
    {
        // chama select
        $sql = new Sql();
        //listar o ususario pelo id
        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
            //setando valor
            ":iduser" => $iduser
        ));

        $this->setData($results[0]);
    }

    //metodo para update / salvar usuarios no banco
    public function update()
    {
        $sql = new Sql();
        //select da procedure
        $results = $sql->select("CALL   sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":iduser"       => $this->getiduser(),
            ":desperson"    => $this->getdesperson(),
            ":deslogin"     => $this->getdeslogin(),
            ":despassword"  => User::getPasswordHash($this->getdespassword()),
            ":desemail"     => $this->getdesemail(),
            ":nrphone"      => $this->getnrphone(),
            ":inadmin"      => $this->getinadmin()
        ));

        $this->setData($results[0]);
    }
    
    //metodo deletar
    public function delete()
    {
        $sql = new Sql();
        $sql->query("CALL sp_users_delete(:iduser)", array(
            //setando dados
            ":iduser" =>$this->getiduser()
        ));
    }

    //metodo recuperar email////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function getForgot($email, $inadmin = true)
    {
        //chamando class Sql
        $sql = new Sql();
        //query da consulta
        $results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email", array(
            //passando dados para parameto
            ":email" => $email
        ));
        //verificar se encontrou email
        if (count($results) === 0) {
            throw new Exception("Não foi possivel recuperar a senha", 1);            
        } 
        else
        {
            //receber resultados acima
            $data = $results[0];
            //execultando uma procedure com CALL
            $resultsRecovery = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                //setand ovalores
                ":iduser"  => $data["iduser"],
                ":desip"   => $_SERVER["REMOTE_ADDR"]
            ));
            //VERIFICA SE HOUVE RESULTADO NOVAMENTE
            if (count($resultsRecovery) === 0 ) {
                throw new Exception("Mão foi possí vel recuperar a senha", 1);                
            }
            else
            {
                //recebe dados da recovery
                $dataRecovery = $resultsRecovery[0];
                //criptografia da senha
                $code = base64_encode(openssl_encrypt($dataRecovery["idrecovery"], "AES-128-CBC", pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV)));

                if ($inadmin === true) {

                    //link de acesso a recuperação de senha Admin
                    $link = "http://www.avianashop.com.br/admin/forgot/reset?code=$code";

                } else {
                    
                    //link de acesso a recuperação de senha Site
                    $link = "http://www.avianashop.com.br/forgot/reset?code=$code";

                }
                
                //criando o objeto email passando os danos nas variaveis
                //email, nome, assunto, nome do template e os dados que vai em uma array()
                $mailer = new Mailer($data["desemail"], $data["desperson"], utf8_decode("Redefinição de senha da Aviana Shop"), "forgot", array(
                    //setando os dados que precisam no email
                    "name" =>$data["desperson"],
                    "link" =>$link
                ));
                //enviando email que vem da classe Mailer
                $mailer->send();
                //retornar os dados da pessoa para usu futuru
                return $data;

            }

        }

    }

    //descriptografar o codigo do email e validar 
    public static function validForgotDecrypt($code)
    {
        //recebendo codigo para decodificar
        $code = base64_decode($code);
        //descriptografando....
        $idrecovery = openssl_decrypt($code, "AES-128-CBC", pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));
        //verificar no banco se o codigo é valido
        $sql = new Sql();
        $results = $sql->select("
            SELECT *
            FROM tb_userspasswordsrecoveries a
            INNER JOIN tb_users b USING(iduser)
            INNER JOIN tb_persons c USING(idperson)
            WHERE
                a.idrecovery = :idrecovery
                AND
                a.dtrecovery IS NULL
                AND
                DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
            ", array(
            //setando dados
            ":idrecovery" => $idrecovery
        ));
        //verifica se houve retoro do sql
        if (count($results) === 0) {
            throw new Exception("Não foi possivel recuperar a seha", 1);
            
        }
        else
        {
            //devolvendo dados que veio do banco
            return $results[0];
        }

    }

    //função para salvar a recuperação da senha
    public static function setForgotUsed($idrecovery)
    {
        $sql = new Sql();
        $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
            //setando variaveis
            ":idrecovery" =>$idrecovery
        ));
    }

    //salvando no banco a senha nova
    public function setPassword($password)
	{

		$sql = new Sql();

		$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
			":password"=>$password,
			":iduser"=>$this->getiduser()
		));

	}

    public static function setError($msg)
    {

        $_SESSION[User::ERROR] = $msg;

    }

    public static function getError()
    {

        $msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';
        
        User::clearError();

        return $msg;

    }

    public static function clearError()
    {

        $_SESSION[User::ERROR] = NULL;

    }

    public static function setSuccess($msg)
	{

		$_SESSION[User::SUCCESS] = $msg;

	}

	public static function getSuccess()
	{

		$msg = (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS]) ? $_SESSION[User::SUCCESS] : '';

		User::clearSuccess();

		return $msg;

	}

	public static function clearSuccess()
	{

		$_SESSION[User::SUCCESS] = NULL;

	}

    public static function setErrorRegister($msg)
    {

        $_SESSION[User::ERROR_REGISTER] = $msg;

    }

    public static function getErrorRegister()
	{

		$msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[User::ERROR_REGISTER] : '';

		User::clearErrorRegister();

		return $msg;

	}

    public static function clearErrorRegister()
	{

		$_SESSION[User::ERROR_REGISTER] = NULL;

	}

    public static function checkLoginExist($login)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :deslogin", [
			':deslogin'=>$login
		]);

		return (count($results) > 0);

	}
    
    public static function getPasswordHash($password)
	{

		return password_hash($password, PASSWORD_DEFAULT, [
			'cost'=>12
		]);

	}

    public function getOrders()
	{

		$sql = new Sql();

		$results = $sql->select("
			SELECT * 
			FROM tb_orders a 
			INNER JOIN tb_ordersstatus b USING(idstatus) 
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			WHERE a.iduser = :iduser
		", [
			':iduser'=>$this->getiduser()
		]);

		return $results;

	}


}

?>