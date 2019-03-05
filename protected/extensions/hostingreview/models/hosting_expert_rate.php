<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingExpertRateModel extends \Model\BaseModel
{

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_expert_rate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['category_id', 'required'],
        ];
    }

    /**
     * @return array
     */
    public function getData($data = null)
    {
        $sql = 'SELECT t.*, c.title AS category_name   
            FROM {tablePrefix}ext_hosting_expert_rate t 
            LEFT JOIN {tablePrefix}ext_hosting_expert_review r ON r.id = t.expert_review_id
            LEFT JOIN {tablePrefix}ext_hosting_rate_category c ON c.id = t.category_id
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['review_id'])) {
                $sql .= ' AND t.expert_review_id=:review_id';
                $params['review_id'] = $data['review_id'];
            }
            if (isset($data['expert_id'])) {
                $sql .= ' AND r.expert_id=:expert_id';
                $params['expert_id'] = $data['expert_id'];
            }
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
        $sql = 'SELECT t.*, r.content, c.title AS category_name 
            FROM {tablePrefix}ext_hosting_expert_rate t 
            LEFT JOIN {tablePrefix}ext_hosting_expert_review r ON r.id = t.expert_review_id
            LEFT JOIN {tablePrefix}ext_hosting_rate_category c ON c.id = t.category_id
            WHERE t.id =:id';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, ['id'=>$id] );

        return $row;
    }

    public function getRateByReview($data) {
        $datas = self::getData($data);
        $items = [];
        foreach ($datas as $i => $dt) {
            $items[$dt['category_id']] = [ 'id' => $dt['id'], 'value' => (int)$dt['value'] ];
        }

        return $items;
    }
}
