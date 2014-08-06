<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 课程模块类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class CourseModuleTable
{
    /**
     * The Table Gateway object is intended to provide an object that 
     * represents a table in a database, and the methods of this object 
     * mirror the most common operations on a database table.
     * 
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * CourseTable类的构造函数.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 获取所有课程模块的信息.
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个CourseModule对象
     */
    public function getAllCourseModules($offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($offset, $limit) {
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }

    /**
     * 使用课程模块的唯一标识符获取课程对象.
     * @param  int $courseModuleId - 课程模块的唯一标识符
     * @return 一个课程模块对象
     */
    public function getCoureModuleUsingCourseModuleId($courseModuleId)
    {
        $rowSet = $this->tableGateway->select(array(
            'course_module_id'  => $courseModuleId
        ));
        return $rowSet->current();
    }

    /**
     * 使用课程的唯一标识符获取课程模块对象.
     * @param  int $courseId - 课程的唯一标识符
     * @return 一个ResultSet对象, 包含若干个CourseModule对象
     */
    public function getCourseModulesUsingCourseId($courseId)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($courseId) {
            $select->join('itp_course_composition', 
                          'itp_course_modules.course_module_id = itp_course_composition.course_module_id');
            $select->where->equalTo('course_id', $courseId);
        });
        return $resultSet;
    }

    /**
     * 使用课程会话的唯一标识符获取课程模块对象.
     * @param  int $lectureId - 课程会话的唯一标识符
     * @return 一个ResultSet对象, 包含若干个CourseModule对象
     */
    public function getCourseModulesUsingLectureId($lectureId)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($lectureId) {
            $select->join('itp_course_composition',
                          'itp_course_modules.course_module_id = itp_course_composition.course_module_id');
            $select->join('itp_lecture_schedule',
                          'itp_course_modules.course_module_id = itp_lecture_schedule.course_module_id');
            $select->where->equalTo('lecture_id', $lectureId);
        });
        return $resultSet;
    }

    /**
     * 使用用户的唯一标识符获取课程模块对象.
     * @param  int $teacherId - 用户的唯一标识符
     * @return 一个ResultSet对象, 包含若干个CourseModule对象
     */
    public function getCourseModulesUsingTeacherId($teacherId)
    {
        $resultSet = $this->tableGateway->select(array(
            'teacher_id'    => $teacherId
        ));
        return $resultSet;
    }
    
    /**
     * 创建一个新课程模块.
     * @param  Array $courseModule - 一个包含课程模块信息的数组
     * @return 新创建课程的课程模块唯一标识符
     */
    public function createCourseModule($courseModule)
    {
        $this->tableGateway->insert($courseModule);
        return $this->tableGateway->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();
    }

    /**
     * 更新课程模块对象的信息.
     * @param  Array $courseModule - 一个包含课程模块信息的数组
     * @return 操作是否成功完成
     */
    public function updateCourseModule($courseModule)
    {
        $this->tableGateway->update($courseModule, array(
            'course_module_id'  => $courseModule['course_module_id'],
        ));
        return true;
    }

    /**
     * 通过课程模块唯一标识符删除课程对象.
     * @param  int $courseModuleId - 课程模块的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteCourseModule($courseModuleId)
    {
        $this->tableGateway->delete(array(
            'course_module_id'  => $courseModuleId,
        ));
        return true;
    }
}