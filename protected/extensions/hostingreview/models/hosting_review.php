<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingReviewModel extends \Model\BaseModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_HIDDEN = 'hidden';

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_review';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['hosting_company_id', 'required'],
        ];
    }

    /**
     * @return array
     */
    public function getData($data = null)
    {
        $sql = 'SELECT t.*, r.name AS reviewer_name, r.email AS reviewer_email  
            FROM {tablePrefix}ext_hosting_review t 
            LEFT JOIN {tablePrefix}ext_hosting_reviewer r ON r.id = t.reviewer_id 
            WHERE 1';

        $params = [];
        if (is_array($data)) {

        }

        $sql .= ' ORDER BY t.created_at DESC';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );

        return $rows;
    }

    /**
     * @param $id
     * @return array
     */
    public function getDetail($id)
    {
        $sql = 'SELECT t.*, r.name AS reviewer_name, r.email AS reviewer_email 
            FROM {tablePrefix}ext_hosting_review t 
            LEFT JOIN {tablePrefix}ext_hosting_reviewer r ON r.id = t.reviewer_id 
            WHERE t.id =:id';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, ['id'=>$id] );

        return $row;
    }

    public function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_HIDDEN => 'Hidden'
        ];
    }
}
