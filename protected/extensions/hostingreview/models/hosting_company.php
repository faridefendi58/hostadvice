<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingCompanyModel extends \Model\BaseModel
{
    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';
    const STATUS_hidden = 'hidden';

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_company';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['title', 'required'],
            ['created_at', 'required', 'on'=>'create'],
        ];
    }

    /**
     * @return array
     */
    public function getData($data = null)
    {
        $sql = 'SELECT t.*, a.name AS admin_name  
            FROM {tablePrefix}ext_hosting_company t 
            LEFT JOIN {tablePrefix}admin a ON a.id = t.created_by 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['status'])) {
                $sql .= ' AND t.status =:status';
                $params['status'] = $data['status'];
            }
        }

        if (isset($data['order_by'])) {
            if ($data['order_by'] == 'rangking') {
                $sql .= ' ORDER BY t.rangking ASC';
            }
        } else {
            $sql .= ' ORDER BY t.created_at DESC';
        }

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
        $sql = 'SELECT t.*,  a.name AS created_by_name, ab.name AS updated_by_name 
            FROM {tablePrefix}ext_hosting_company t 
            LEFT JOIN {tablePrefix}admin a ON a.id = t.created_by 
            LEFT JOIN {tablePrefix}admin ab ON ab.id = t.updated_by 
            WHERE t.id =:id';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, ['id'=>$id] );

        return $row;
    }

    public function getItem($data)
    {
        $sql = 'SELECT t.* 
            FROM {tablePrefix}ext_hosting_company t 
            WHERE 1';

        $params = [];
        if (isset($data['title'])) {
            $sql.= ' AND LOWER(t.title)=:title';
            $params['title'] = $data['title'];
        }

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, $params );

        return $row;
    }

    public function getItemsByProductCategory($category_id)
    {
        $sql = 'SELECT t.* 
            FROM {tablePrefix}ext_hosting_company t 
            LEFT JOIN {tablePrefix}ext_hosting_company_product p ON p.company_id = t.id 
            WHERE p.category_id =:category_id';

        $sql .= ' GROUP BY t.id ORDER BY t.rangking ASC';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, ['category_id' => $category_id] );

        return $rows;
    }
}
