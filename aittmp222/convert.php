<?php
/**
 * Created by JetBrains PhpStorm.
 * User: korf
 * Copy new module in var\ait_upgraid\module\ and run convert
 */

require_once('auth_ip_check.php');
require_once('auth.php');


$update = new Aitoc_Convert();
$update->start();

class Aitoc_Convert
{

    public $moduleName ;
    public $arrayFind = array();
    public $arrayFindP = array();
    public $dir = '../var/ait_upgraid/';
    public $module_dir = 'module/';
    public $update_dir = 'update/';
    public $backup_dir = 'backup/';
    public $local_dir = 'app/code/local/';

    public function start()
    {
        $arrVerMag = scandir($this->dir.$this->module_dir.$this->local_dir);

        //print_r($arrVerMag);

        foreach($arrVerMag as $ver)
        {
            $this->find_all($ver);

        }
        $this->arrayFind['app/code/local/Aitoc/Aitgroupedoptions/Model/Observer.php'] = '$rule = $ruler->getRule(\'product\');
            $aitsys->addWarning(Mage::helper(\'aitsys\')->__(
                Mage::helper(\'aitsys\')->getErrorText(\'seg_exceed_limit\'),
                $module->getLabel(),
                $rule[\'value\'],
                $ruler->getProductCount(),
                Mage::helper(\'aitsys\')->getModuleLicenseUpgradeLink($module,false)
            ));';
        $this->arrayFind['app/code/local/Aitoc/Aitdoubleproduct/Model/Observer.php'] = $this->arrayFind['app/code/local/Aitoc/Aitgroupedoptions/Model/Observer.php'];

        $this->sratrUpdate();
        //print_r($this->arrayFind);
    }


    public function sratrUpdate()
    {
        if(!file_exists($this->dir.$this->backup_dir))
            mkdir($this->dir.$this->backup_dir, 0777, true);

        if(!file_exists($this->dir.$this->update_dir))
            mkdir($this->dir.$this->update_dir, 0777, true);

        $this->viewDir($this->local_dir);
        //$arrVerMag = scandir('../'.$this->local_dir);




    }


    public function viewDir($dir)
    {
        $arrayDir = scandir('../'.$dir);

        foreach($arrayDir as $file)
        {
            if ($file == '.' || $file == '..')
                continue;
            if(is_dir('../'.$dir.$file) )
            {
                $this->viewDir($dir.$file.'/');
                continue;
            }
            if(end(explode(".", $file)) != 'php')
            {
                continue;
            }

            $find = $this->replaceInFile($dir.$file);
        }
        return $this;
    }


    public function replaceInFile($filename)
    {
        $file =  file_get_contents ('../'.$filename);
        $arr_find = array();
        $countFind = preg_match_all('/(.*?)\<\?php if\(Aitoc_Aitsys_Abstract_Service::initSource(.*?)\); \?\>(.*?)\} $/s',$file,$arr_find);
        if ($countFind > 0 || !empty($this->arrayFind[$filename]))
        {
            echo $filename.' ';
            $this->createFile($this->dir.$this->backup_dir.$filename, $file);

            if($countFind > 0)
            {
                $text_new  = $arr_find[1][0].$arr_find[3][0];
                echo ' => DELETE Aitoc_Aitsys_Abstract_Service ';
            }
            else
            {
                $text_new = $file;
            }
            if(!empty($this->arrayFind[$filename]))
            {
                foreach($this->arrayFind[$filename] as $findtext)
                {
                    /*$arr_find_aitoc_coment = array();
                    $findtextpreg = preg_quote($findtext);
                    $findtext = str_replace("/", "\/",$findtext);
                    preg_match_all('/\/\* \*\/'.$findtextpreg.'\/\* \*\//s',$text_new,$arr_find_aitoc_coment);
                    print_r($arr_find_aitoc_coment);*/
                    $text_new = str_replace('/* */'.$findtext.'/* */', '/* {#AITOC_COMMENT_END#}'.$findtext.'{#AITOC_COMMENT_START#} */',$text_new);
                    echo ' => REWRITE AITOC COMMENTS ';
                }
                //die();

            }
            $this->createFile($this->dir.$this->update_dir.$filename, $text_new);
            echo '<br>';
            //die();
            //print_r($arr_find);
            //die();
            //fopen();
            //$this->arrayFind[$filename] = $arr_find[1];
            //return true;
            //echo $filename.'<br>';
            //print_r($arr_find);die();
        }

    }

    public function createFile($filename, $text)
    {

        if(!file_exists(dirname($filename)))
            mkdir(dirname($filename), 0777, true);
        if(file_exists($filename))
        {
            unlink($filename);
        }
        $file = fopen($filename, 'x');
        fwrite ($file, $text);
        fclose($file);

    }

    public function find_all($ver)
    {
        if ($ver == '.' || $ver == '..')
            return false;

        $arrModule = scandir($this->dir.$this->module_dir.$this->local_dir.$ver);
        foreach($arrModule as $mod)
        {

            if ($mod == '.' || $mod == '..')
                continue;
            //$this->moduleName = 'var/modules_aitoc/'.$ver.'/'.$mod.'/';
            $this->find_lis_comm($this->local_dir.$ver.'/'.$mod);
        }
    }

    public function find_lis_comm($dir)
    {
        if(!file_exists($this->dir.$this->module_dir.$dir))
        {
            return false;
        }
        $arrFiles = scandir($this->dir.$this->module_dir.$dir);
        foreach($arrFiles as $file)
        {
            if ($file == '.' || $file == '..')
                continue;
            if(is_dir($this->dir.$this->module_dir.$dir.'/'.$file) )
            {
                $this->find_lis_comm($dir.'/'.$file);
                continue;
            }
            if(end(explode(".", $file)) != 'php')
            {
                continue;
            }

            $find = $this->findInFile($dir.'/'.$file);
        }
        return ;
    }

    public function findInFile($filename)
    {

        $file =  file_get_contents ($this->dir.$this->module_dir.$filename);
        $arr_find = array();
        $countFind = preg_match_all('/\{#AITOC_COMMENT_END#\}(.*?)\{#AITOC_COMMENT_START#\}/s',$file,$arr_find);
        if ($countFind > 0)
        {
            $this->arrayFind[$filename] = $arr_find[1];
            //return true;
        }
      /*  $count = 0;
        Foreach($arr_find[1] as $item)
        {
            $arr_find_tmp = array();
            $count += preg_match_all('/(.*)\$performer(.*)/',$item, $arr_find_tmp);

        }
        $arr_find2 = array();
        $countFind = preg_match_all('/(.*)\$performer(.*)/',$file,$arr_find2);
        if ($countFind <> $count)
        {
            //var_dump($arr_find2);
            $this->arrayFindP[$filename] = "$countFind <> $count";
            //return true;
        }*/

        return $this;
    }
}