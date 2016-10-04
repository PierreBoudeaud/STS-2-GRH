<?php
	/*
	*		@author LUTAU T
	*/
	abstract class Model{
		protected $table;
		protected $pk;
		protected $attribtech = array('table', 'pk','attribtech');
		
		/**
		*		__construct - Constructeur de la classe Model
		*		$table et $pk font partis de l'objet Model.
		*
		*		@author LUTAU T
		*		@date 27/09/2016
		*/
		public function __construct(){
			$this->table = "";
			$this->pk = "";
		}
		
		/**
		*		connexion - Connexion à la base de données
		*		Charge les informations de connexion depuis un fichier configuration (.ini)
		*
		*		@return PDO La connexion à la base de donnée
		*		@author BOUDEAUD P
		*		@date 04/10/2016
		*/
		protected function connexion(){
			$ini_parse = parse_ini_file("/cfg/bdd.ini");//Fichier de configuration
			$dsn = $ini_parse['type'].":dbname=".$ini_parse['dbName'].";host=".$ini_parse['host'].";port=".$ini_parse['port'];
			try{
				$DB = new PDO($dsn, $ini_parse['pseudo'], $ini_parse['mdp']);
			}catch(PDOException $e){
				echo "Connexion échouée : ".$e->getMessage();
				$DB = null;
			}
			return $DB;
		}
		
		/**
		*		delete - Suppression un enregistrement de la base de données
		*		Supprime un enregistrement dans la base de données en fonction de l'id
		*
		*		@param String $id identifiant de l'enregistrement à supprimer
		*		@see Model::connexion()		 Connexion à la base
		*		@author LUTAU T
		*		@date 27/09/2016
		*/
		public function delete($id){
			$req1 = "DELETE FROM {$this->table} WHERE {$this->pk} = $id";
			
			$base = $this->connexion();
			
			$sql = $base->prepare($req1);
			
			$sql->execute();
			//echo $req;
		}
		
		/**
		*		read - Lire un enregistrement
		*		Lit un enregistrement en fonction de l'id
		*
		*		@param String $id identifiant de l'enregistrement à lire
		*		@see Model::connexion()		Connexion à la base
		*		@author LUTAU T
		*		@date 27/09/2016
		*/
		public function read($id){
			$req2 = "SELECT * FROM {$this->table} WHERE {$this->pk} = $id";
			
			$base = $this->connexion();
			
			//Préparation de la requête pour récupérer les infos
			$tab = $base->prepare($req2);
			
			//Exécution de la requête pour récupérer les infos
			$tab->execute();
			
			//Lecture
			$infos = $tab->fetch(PDO::FETCH_ASSOC);
			//retourne toute la table dans un tableau dans $infos donc print_r pour l'afficher
			
			foreach($infos as $cle=>$val){
				//echo $cle." : ".$val . "<br/>" ;
				$this->$cle = $val;
			}
			//return($infos);
		}
		
		/**
		*		find - trouver un enregistrement
		*		Trouve un enregistrement en fonction d'une condition
		*
		*		@param String $condition condition pour trier les enregistrements à trouver
		*		@see Model::connexion()		Connexion à la base
		*		@author LUTAU T
		*		@date 27/09/2016
		*/
		public function find($condition){
			$sql="SELECT * FROM {$this->table} WHERE $condition";
			//echo $sql;
			$connexion=$this->connexion();
			$sql=$connexion->query($sql);
			$tmp[]="";
			while ($result=$sql->fetch(PDO::FETCH_ASSOC)){;
				$tmp[] = $result;
			}
			return $tmp;
		}
		
		/**
		*		create - Créer un enregistrement dans la base de données
		*		Créer un enregistrement à partir de l'objet courant
		*
		*		@see Model::connexion()		Connexion à la base
		*		@author LUTAU T
		*		@date 27/09/2016
		*/
		public function create(){
			$req3 = "INSERT INTO {$this->table}(";
			$notFirstComma = 0;
			foreach($this as $cle=>$val){
				if(!in_array($cle,$this->attribtech) && $cle != $this->pk){
					if($notFirstComma > 0){
						$req3 = $req3.", ";
					}
					$req3 = $req3.$cle;
					$notFirstComma++;
				}
			}
			$req3="{$req3}) VALUES(";
			$notFirstComma = 0;
			foreach($this as $cle=>$val){
				if(!in_array($cle,$this->attribtech) && $cle != $this->pk){
					if($notFirstComma > 0){
						$req3 = $req3.", ";
					}
					$notFirstComma++;
					$req3 ="{$req3}'{$val}'";
				}
			}
			$req3 = $req3.")";
			echo "<br/>".$req3."<br/>";
			//$db = $this->connexion();
						
			//$db->execute($req3);
		}
		
		/**
		*		update - Modifier un enregistrement dans la base de données
		*		Modifie un enregistrement à partir de l'identifiant de l'objet courant
		*
		*		@param String $id identifiant de l'enregistrement à modifier
		*		@see Model::connexion()		Connexion à la base
		*		@author LUTAU T
		*		@date 27/09/2016
		*/
		public function update($id){
			$req3 = "UPDATE {$this->table} SET ";
			$notFirstComma = 0;
			foreach($this as $cle=>$val){
				if(!in_array($cle,$this->attribtech) && $cle != $this->pk){
					if($notFirstComma > 0){
						$req3 = $req3.", ";
					}
					$notFirstComma++;
					$req3 =" {$req3}{$cle} = '{$val}'";
				}
			}
			$req3 = $req3." WHERE {$this->pk} = {$id}";
			echo $req3;
			//$db = $this->connexion();
						
			//$db->execute($req3);
		}
	}