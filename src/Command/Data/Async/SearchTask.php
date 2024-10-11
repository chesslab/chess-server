<?php

namespace ChessServer\Command\Data\Async;

use Chess\Movetext\SanMovetext;
use Chess\Variant\Classical\PGN\Move;
use ChessServer\Command\AbstractDbAsyncTask;

class SearchTask extends AbstractDbAsyncTask
{
    const SQL_LIKE = [
        'Date',
        'movetext',
    ];

    const SQL_EQUAL = [
        'Event',
        'White',
        'Black',
        'ECO',
        'Result',
    ];

    public function run()
    {
        $sql = 'SELECT * FROM games WHERE ';

        $values = [];

        foreach ($this->params as $key => $val) {
            if ($val) {
                if (in_array($key, self::SQL_LIKE)) {
                    $sql .= "$key LIKE :$key AND ";
                    if ($key === 'movetext') {
                        $val = (new SanMovetext(new Move(), $this->params['movetext']))
                            ->filtered($comments = false, $nags = false);
                    }
                    $values[] = [
                        'param' => ":$key",
                        'value' => '%'.$val.'%',
                        'type' => \PDO::PARAM_STR,
                    ];
                } else if (in_array($key, self::SQL_EQUAL) && $val) {
                    $sql .= "$key = :$key AND ";
                    $values[] = [
                        'param' => ":$key",
                        'value' => $val,
                        'type' => \PDO::PARAM_STR,
                    ];
                }
            }
        }

        str_ends_with($sql, 'WHERE ')
            ? $sql = substr($sql, 0, -6)
            : $sql = substr($sql, 0, -4);

        $sql .= 'ORDER BY RAND() LIMIT 25';

        return $this->db->query($sql, $values)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
