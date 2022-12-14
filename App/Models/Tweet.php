<?php

namespace App\Models;
use MF\Model\Model;

class Tweet extends Model{

    private $id;
    private $id_usuario;
    private $tweet;
    private $data;

    public function __get($atributo){
        return $this->$atributo;
    }

    public function __set($atributo, $valor){
        $this->$atributo = $valor;
    }

    //salvar
    public function salvar() {
        $query = "insert into tweets(id_usuario, tweet) values (:id, :tweet)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue("id", $this->__get('id_usuario'));
        $stmt->bindValue("tweet", $this->__get('tweet'));
        $stmt->execute();

        return $this;
    }

    //recuperar
    public function getPorPagina($limite, $deslocamento) {
        $query = "
            select 
                t.id, 
                t.id_usuario, 
                t.tweet, 
                DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data, u.nome
            from 
                tweets as t 
                left join usuarios as u on (t.id_usuario = u.id)
            where 
                t.id_usuario = :id_usuario
                or t.id_usuario in 
                (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
            order by
                t.data desc
            limit
                $limite
            offset
                $deslocamento
            ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id_usuario", $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTotalRegistros() {
        $query = "
            select 
                count(*) as total
            from 
                tweets as t 
                left join usuarios as u on (t.id_usuario = u.id)
            where 
                t.id_usuario = :id_usuario
                or t.id_usuario in 
                (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
            ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id_usuario", $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function deletarTweet() {
        $query = "
        delete from 
            tweets 
        where 
            id_usuario = :id_usuario and id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();

        return true;
    }
    
} 

?>