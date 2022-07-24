<?php
namespace MyBlog\Model;

use RuntimeException;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Update;

class LaminasDbSqlCommand implements PostCommandInterface
{
    private $db;

    public function __construct(AdapterInterface $db)
    {
        $this->db = $db;
    }

    public function insertPost(Post $post)
    {
        $insert = new Insert('article');
        $insert->values([
            'title' => $post->getTitle(),
            'text' => $post->getText(),
        ]);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();

        if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database Error'
            );
        }

        $id = $result->getGeneratedValue();

        return new Post(
            $post->getTitle(),
            $post->getText(),
            $id
        );
    }

    public function updatePost(Post $post)
    {
        if (! $post->getId()) {
            throw new RuntimeException('Artikel konnte nicht bearbeitet werden.');
        }
    
        $update = new Update('article');
        $update->set([
                'title' => $post->getTitle(),
                'text' => $post->getText(),
        ]);
        $update->where(['id = ?' => $post->getId()]);
    
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
    
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database Error'
            );
        }
    
        return $post;
    }

    public function deletePost(Post $post)
    {
        if (! $post->getId()) {
            throw new RuntimeException('Artikel konnte nicht gelöscht werden');
        }
    
        $delete = new Delete('article');
        $delete->where(['id = ?' => $post->getId()]);
    
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($delete);
        $result = $statement->execute();
    
        if (! $result instanceof ResultInterface) {
            return false;
        }
    
        return true;
    }
}