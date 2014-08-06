<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 课程组成类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class CourseCompositionTable
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
     * CourseCompositionTable类的构造函数.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 更新课程模块组合信息.
     * @param  int   $courseId        - 课程的唯一标识符
     * @param  Array $courseModuleIds - 课程模块的唯一标识符数组
     * @return 操作是否成功完成
     */
    public function updateCourseComposition($courseId, $courseModuleIds)
    {
        $courseModuleIdArray = split(',', $courseModuleIds);
        if ( !$this->deleteCourseComposition($courseId) ) {
            return false;
        }
        
        foreach ( $courseModuleIdArray as $courseModuleId ) {
            if ( !is_numeric($courseModuleId) ) {
                continue;
            }
            $courseComposition = array(
                'course_id'         => $courseId,
                'course_module_id'  => $courseModuleId,
            );
            $this->tableGateway->insert($courseComposition);
        }
        return true;
    }

    /**
     * 删除课程模块组合信息.
     * @param  int $courseId - 课程的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteCourseComposition($courseId)
    {
        $this->tableGateway->delete(array(
            'course_id' => $courseId,
        ));
        return true;
    }
}