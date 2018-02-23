<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * 课程会话计划类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class LectureScheduleTable {
    /**
     * The Table Gateway object is intended to provide an object that 
     * represents a table in a database, and the methods of this object 
     * mirror the most common operations on a database table.
     * 
     * @var TableGatewayInterface
     */
    protected $tableGateway;

    /**
     * CourseCompositionTable类的构造函数.
     * @param TableGatewayInterface $tableGateway 
     */
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function createLectureSchedule($lectureSchedule) {
        $this->tableGateway->insert($lectureSchedule);
        return true;
    }

    public function deleteLectureSchedule($lectureId) {
        $this->tableGateway->delete(array(
            'lecture_id' => $lectureId,
        ));
        return true;
    }
}