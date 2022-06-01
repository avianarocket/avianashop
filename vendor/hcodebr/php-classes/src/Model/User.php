<?php

namespace Hcode\Model;

use Exception;
use Hcode\DB\Sql;
use Hcode\Model;

class User extends Model{

    //constante interna da sessao
    const SESSION = "User";

    //recebendo dados do formulario de login
    public static function login($login, $password)
    {
        //verificar se os dados do ususario estao no banco mesmo
        $sql = new Sql();
        //guardando daos na variavel results apos execultar a consulta
        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGINS", array(
            ":LOGINS" => $login
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
        //primerio verificar se existe sessao
        if (
            !isset($_SESSION[User::SESSION]) //se nao existir
            || //ou
            !$_SESSION[User::SESSION] //se existir mas estiver vazia
            || //ou
            !(int)$_SESSION[User::SESSION]["iduser"] > 0 //verificar se nã é inteiro e maior que zero
            || // ou
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin // verifica se ele é admin
        ){
            header("Location: /admin/login"); //redireciona pro login
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
        //select da procedure
        $results = $sql->select("CALL   sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":desperson"    => $this->getdesperson(),
            ":deslogin"     => $this->getdeslogin(),
            ":despassword"  => $this->getdespassword(),
            ":desemail"     => $this->getdesemail(),
            ":nrphone"      => $this->getnrphone(),
            ":inadmin"      => $this->getinadmin()
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
            ":despassword"  => $this->getdespassword(),
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
}

?>