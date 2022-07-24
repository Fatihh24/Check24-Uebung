<?php

namespace MyBlog\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class CommentTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function getComment($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['comment_id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Kommentar konnte nicht gefunden werden %d',
                $id
            ));
        }

        return $row;
    }

    public function saveComment(Comment $comment)
    {
        $data = [
            'comment' => $comment->comment,
            'article_id' => $comment ->article_id,
            'username' => $comment ->username,
        ];

        $id = (int) $comment->comment_id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getComment($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Kommentar konnte nicht hinzugefügt werden.',
                $id
            ));
        }

        $this->tableGateway->update($data, ['comment_id' => $id]);
    }

    public function deleteBlog($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}