<?php

/**
 * DataBase dump.
 *
 * Usage:
 * <pre>
 *           $dumper = new dbBackup();
 *           $dumper->getDump();
 * </pre>
 */
class dbDump{

        private $constraints;


        /**
         * Dump all tables
         * @param boolean $download - if the generated data is to be sent to browser 
         * @return file|strings 
         */
	public function getDump($download = TRUE){
		ob_start();
                //echo Yii::app()->db->tablePrefix;
                //Yii::app()->end();
		foreach($this->getTables() as $key=>$val){
                    //echo $key;
                    //echo Yii::app()->db->tablePrefix;
                    //echo strpos($key,Yii::app()->db->tablePrefix)===true;
                    //echo strpos($key,Yii::app()->db->tablePrefix);
                    if(Yii::app()->db->tablePrefix!=''){
                        if((strpos($key,Yii::app()->db->tablePrefix)===0))
                            $this->dumpTable($key);
                    }else{
                        $this->dumpTable($key);
                    }
                }
		$result = $this->setHeader();
                $result.= ob_get_contents();
                //$result.= $this->getConstraints();
                $result.= $this->setFooter();
                ob_end_clean();
                if($download){
                    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                    header("Cache-Control: no-cache");
                    header("Pragma: no-cache");
                    header("Content-type:application/sql");
                    header("Content-Disposition:attachment;filename=backup.sql");
                } 
                return $result;
	}

        /**
         * Generate constraints to all tables
         * @return string 
         */
        private function getConstraints(){
            $sql = "--\r\n-- Constraints for dumped tables\r\n--".PHP_EOL.PHP_EOL;
            $first = TRUE;
            foreach ($this->constraints as $key => $value) {
                if($first && count($value[0]) > 0){
                    $sql  .= "--\r\n-- Constraints for table `$key`\r\n--".PHP_EOL.PHP_EOL;
                    $sql .= "ALTER TABLE $key".PHP_EOL;
                }
                if(count($value[0]) > 0){
                    for($i=0; $i<count($value[0]);$i++){
                        if(strpos($value[0][$i], 'CONSTRAINT') === FALSE)
                                $sql .= preg_replace('/(FOREIGN[\s]+KEY)/', "\tADD $1", $value[0][$i]);
                        else
                                $sql .= preg_replace('/(CONSTRAINT)/', "\tADD $1", $value[0][$i]);
                        if($i==count($value[0])-1)
                            $sql .= ";".PHP_EOL;
                        if($i<count($value[0])-1)
                            $sql .=PHP_EOL;
                    }
                }
            }
            
            return $sql;            
        }

                
        /**
         * Set sql file header
         * @return string 
         */
        private function setHeader(){
            $header = PHP_EOL."--\n-- foreign key checks, autocomit and start a transaction\n--".PHP_EOL.PHP_EOL;
            $header.="SET FOREIGN_KEY_CHECKS=0;".PHP_EOL;
            $header.="SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";".PHP_EOL;
            $header.="SET AUTOCOMMIT=0;".PHP_EOL;
            $header.="START TRANSACTION;".PHP_EOL.PHP_EOL;
            $header.="/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;".PHP_EOL;
            $header.="/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;".PHP_EOL;
            $header.="/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;".PHP_EOL;
            $header.="/*!40101 SET NAMES utf8 */;".PHP_EOL;
            
            return $header;
        }
        
        
        /**
         * Set sql file footer
         * @return string 
         */
        private function setFooter() {
            $footer =PHP_EOL."SET FOREIGN_KEY_CHECKS=1;".PHP_EOL;
            $footer.="COMMIT;".PHP_EOL.PHP_EOL;
            $footer.="/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;".PHP_EOL;
            $footer.="/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;".PHP_EOL;
            $footer.="/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;".PHP_EOL;
            
            return $footer;
        }

                
	/**
	 * Create table dump
	 * @param $tableName
	 * @return mixed
	 */
	private function dumpTable($tableName){
		$db = Yii::app()->db;
		$pdo = $db->getPdoInstance();

                $newTableName=str_replace(Yii::app()->db->tablePrefix,'',$tableName);
                
		echo PHP_EOL."--\n-- Structure for table `$newTableName`\n--".PHP_EOL;
		echo PHP_EOL.'DROP TABLE IF EXISTS '.$db->quoteTableName($newTableName).';'.PHP_EOL.PHP_EOL;

		$q = $db->createCommand('SHOW CREATE TABLE '.$db->quoteTableName($tableName).';')->queryRow();
                
                $create_query = $q['Create Table'];
                //echo "\n".$create_query."\n";
                $pattern = '/CONSTRAINT.*|FOREIGN[\s]+KEY/';
                
                // constraints to $tablename
                if(preg_match_all($pattern, $create_query,$this->constraints[$tableName])){
                    $create_query=preg_replace($pattern, '', $create_query);
                    $create_query = explode(',',$create_query);
                    for($i=0;$i<count($create_query)-1;$i++){
                        echo ($i>=0 && $i<count($create_query)-2)?$create_query[$i].',':$create_query[$i];
                    }
                } else  {      
                    $create_query = explode(',',$create_query);
                    for($i=0;$i<count($create_query)-1;$i++){
                        echo $create_query[$i].',';
                    }
                }
              
               
               
                echo "\n".trim($create_query[$i]).";".PHP_EOL;
                

		$rows = $db->createCommand('SELECT * FROM '.$db->quoteTableName($tableName).';')->queryAll();

                    
		if(empty($rows))
			return;
    
		echo PHP_EOL."--\n-- Data for table `$tableName`\n--".PHP_EOL.PHP_EOL;

		$attrs = array_map(array($db, 'quoteColumnName'), array_keys($rows[0]));
		echo 'INSERT INTO '.$db->quoteTableName($newTableName).''." (", implode(', ', $attrs), ') VALUES'.PHP_EOL;
		$i=0;
		$rowsCount = count($rows);
		foreach($rows AS $row)
		{
			// Process row
			foreach($row AS $key => $value)
			{
				if($value === null)
					$row[$key] = 'NULL';
				else
					$row[$key] = $pdo->quote($value);
			}

			echo " (", implode(', ', $row), ')';
			if($i<$rowsCount-1)
				echo ',';
			else
				echo ';';
			echo PHP_EOL;
			$i++;
		}
		echo PHP_EOL;
		echo PHP_EOL;
	}



               
	/**
	 * Get mysql tables list
	 * @return array
	 */
	private function getTables()
	{
		$db = Yii::app()->db;
		return $db->getSchema()->getTables();
	}
}
