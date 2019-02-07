<?php
namespace Extensions;

class HostingreviewService
{
    protected $basePath;
    protected $themeName;
    protected $adminPath;
    protected $tablePrefix;

    public function __construct($settings = null)
    {
        $this->basePath = (is_object($settings))? $settings['basePath'] : $settings['settings']['basePath'];
        $this->themeName = (is_object($settings))? $settings['theme']['name'] : $settings['settings']['theme']['name'];
        $this->adminPath = (is_object($settings))? $settings['admin']['path'] : $settings['settings']['admin']['path'];
        $this->tablePrefix = (is_object($settings))? $settings['db']['tablePrefix'] : $settings['settings']['db']['tablePrefix'];
    }
    
    public function install()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{tablePrefix}ext_hosting_company` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(64) NOT NULL,
          `description` text,
          `address` text,
          `website` varchar(128) DEFAULT NULL,
          `phone` varchar(32) DEFAULT NULL,
          `email` varchar(64) DEFAULT NULL,
          `configs` text,
          `status` varchar(16) NOT NULL DEFAULT 'enabled' COMMENT 'enabled, disabled, hidden',
          `created_at` datetime NOT NULL,
          `created_by` int(11) NOT NULL,
          `updated_at` datetime DEFAULT NULL,
          `updated_by` int(11) DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

        $sql .= "CREATE TABLE IF NOT EXISTS `{tablePrefix}ext_hosting_plan` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(128) NOT NULL,
          `description` text,
          `hosting_company_id` int(11) NOT NULL,
          `configs` text,
          `created_at` datetime NOT NULL,
          `created_by` int(11) NOT NULL,
          `updated_at` datetime DEFAULT NULL,
          `updated_by` int(11) DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

        $sql = str_replace(['{tablePrefix}'], [$this->tablePrefix], $sql);
        
        $model = new \Model\OptionsModel();
        $install = $model->installExt($sql);

        return $install;
    }

    public function uninstall()
    {
        return true;
    }

    /**
     * Blog extension available menu
     * @return array
     */
    public function getMenu()
    {
        return [
            [ 'label' => 'Daftar Layanan', 'url' => 'hosting/companies/view', 'icon' => 'fa fa-search' ],
            [ 'label' => 'Tambah Layanan Baru', 'url' => 'hosting/companies/create', 'icon' => 'fa fa-plus' ],
        ];
    }
}
