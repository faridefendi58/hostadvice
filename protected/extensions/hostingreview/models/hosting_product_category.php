<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingProductCategoryModel extends \Model\BaseModel
{

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_product_category';
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
        ];
    }

    /**
     * @return array
     */
    public function getData($data = null)
    {
        $sql = 'SELECT t.*   
            FROM {tablePrefix}ext_hosting_product_category t 
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
        $sql = 'SELECT t.*  
            FROM {tablePrefix}ext_hosting_product_category t 
            WHERE t.id =:id';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, ['id'=>$id] );

        return $row;
    }

    public function getItem($data = array())
    {
        $sql = 'SELECT t.*  
            FROM {tablePrefix}ext_hosting_product_category t 
            WHERE 1';

        $params = [];
        if (isset($data['id'])) {
            $sql .= ' AND t.id =:id';
            $params['id'] = $data['id'];
        }

        if (isset($data['slug'])) {
            $sql .= ' AND t.slug =:slug';
            $params['slug'] = $data['slug'];
        }

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, $params );

        return $row;
    }

    public function getItemsWithCounter($data = null)
    {
        $sql = 'SELECT t.*, (SELECT COUNT(p.id) FROM {tablePrefix}ext_hosting_company_product p WHERE p.category_id = t.id) AS count
            FROM {tablePrefix}ext_hosting_product_category t  
            WHERE 1';

        $params = [];
        if (is_array($data)) {
        }

        $sql .= ' ORDER BY t.created_at DESC';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );

        return $rows;
    }

    public function getSitemaps($data = [])
    {
        $sql = "SELECT t.*  
        FROM {tablePrefix}ext_hosting_product_category t
        WHERE 1";

        $params = [];

        $sql .= " ORDER BY t.created_at ASC";

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );
        $items = [];
        if (count($rows) > 0) {
            $tool = new \Components\Tool();
            $url_origin = $tool->url_origin();
            $categories = [];
            foreach ($rows as $i => $row) {
                if (!in_array($row['slug'], $categories)) {
                    $items[] = [
                        'loc' => $url_origin.'/hosting-services/'.$row['slug'],
                        'lastmod' => date("c"),
                        'priority' => 0.5
                    ];
                    array_push($categories, $row['slug']);
                }
            }
        }
        return $items;
    }
}
