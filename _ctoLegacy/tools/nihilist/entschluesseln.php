<?php
defined('_JEXEC') or die('Restricted access');

class decipher
{

    private $pw1;               //Passwort 1
    private $pw2;               //Passwort 2
    private $alphabet;          //
    private $matrix1;           //
    private $code;              //
    private $ersetzung;         //
    private $klartext;
	  private $clean;
    
    //Konstruktor mit vordefinierten daten
    

    function __construct($pw1="",$pw2="",$code="",$from="",$to="",$alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ")
    {
        $this->pw1      = $pw1;
        $this->pw2      = $pw2;
        $this->from     = $from;
        $this->to       = $to;
        $this->code     = $code;
        $this->alphabet = $alphabet;
   }
   
   
    function setAlpha($in){ $this->alphabet=strtoupper($in); 	}
  	function setPW1($in)	{ $this->pw1=strtoupper($in); 		}
  	function setPW2($in)	{ $this->pw2=strtoupper($in);		}
  	function setCode($in)	{ $this->code=$in;					}
	  function setFrom($in)	{ $this->from=$in;					}
	  function setTo($in)		{ $this->to=$in;					}
	
  	
  	function getAlpha()		{ return $this->alphabet; 	}
  	function getPW1()		  { return $this->pw1; 				}
  	function getPW2()	  	{ return $this->pw2; 				}
  	function getCode()		{ return $this->code;				}
	  function getFrom()		{ return $this->from;				}
    function getTo()	  	{ return $this->to;					}
     
        
    function vorverarb()
    {     
      $this->pw1=$this->Reinige($this->pw1,$this->alphabet);
      $this->pw1=$this->DoppeltRaus($this->pw1);
      $this->pw1=$this->entferneZeichen($from,$this->pw1);  
      $this->pw2=$this->Reinige($this->pw2,$this->alphabet);
      $this->pw2=$this->DoppeltRaus($this->pw2);    
      $this->alphabet=$this->entferneZeichen($this->from,$this->alphabet);
    }
    
    
    function decrypt()
    {
	    $this->matrix1=$this->matrix($this->pw1,$this->alphabet); // erzeugt ersetzung und verschl Matrix
      $ergarray2=$this->ersetzeHin($this->pw2);          
      $erg=$this->ersetzeBack($this->code,$ergarray2);
      return $erg;      
    }
    
    
    function entferneZeichen($from,$menge)
    {
      $menge=str_replace($from,"",$menge);
      return $menge;
    }
    
    
    function ersetzeZeichen($from,$to,$menge)
    {
      $menge=str_replace($from,$to,$menge);
      return $menge;
    }
    
    function DoppeltRaus($inp)
    {               
       $inp=str_split($inp,1);   //Konvertiert den Input-String in ein Array
       $inp=array_unique($inp);  //Entfernt doppelte Werte aus einem Array
       $inp=implode("", $inp);   //Verbindet Array-Elemente zu einem String
       return $inp;
    }
    

                                            
    function Reinige($string,$alphabet)     // alle Zeichen, die nicht in $alphabet enthalten sind, entfallen
    {                  
      $string=$this->EntUml($string);       //Umlaute werden durch entspr. 
      for($i=0;$i!=strlen($string);$i++)    //Bigramme ersetzt
      {                 
        $zeichen = substr($string,$i,1);    //�bergibt jeweils 1 zeichen an stelle $i
        if (eregi($zeichen, $alphabet))     //pr�ft, ob $zeichen in $alphabet enthalten ist
        {
            $return.=$zeichen;              //bei �bereinstimmung wird $zeichen an $return angeh�ngt
        }
      }
     
      return $return;  
    }
    
 
                                                          // ersetzt Umlaute durch die entsprechenden Bigramme
    function EntUml($string)
    {                             
      $string=strtoupper($string);                        // aus Kleinbuchstaben werden Gro�buchstaben
      $search  = array ("/�/","/�/","/�/","/�/");         // Umlaute, die ersetzt werden sollen
      $replace = array ("AE","OE","UE","SS");             // zu Bigrammen
      $return  = preg_replace($search, $replace, $string);// $search=Suchmuster, $replace=Ersatz, $string= Zeichenkette 
      return $return;                                     // gereinigte Zeichenkette wird zur�ckgegeben
    }
    
 
    //Aufbau der Matrix, $pass soll das Passwort enthalten
    function matrix($pass,$alphabet)
    {   
       $alphaidx=0;
       $pass2=$pass;                          //Kopie zum vergleich, da $pass auf L�nge 0 runtergez�hlt wird
            
       $pass=str_split(strtoupper($pass));    //Konvertiert Eingabe-String in ein Array
       $alphabet=str_split($alphabet);        //Konvertiert Alphabet in ein Array
       $alphabet=array_diff($alphabet,$pass); //alle Werte die in $pass enth. sind werden aus $alphabet entfernt
       $out = array_merge($pass,$alphabet);   //Beide Arrays werden zusammengef�hrt
       $this->hilf01=$out;
       $n=0;
       foreach($out as $b)
       {
          $x=$n%5;
          $y=floor($n/5);
          $ma[$y][$x]=$b;       
          $alf[$b]=($y+1).($x+1);
          $n++;
       }       
       $this->ersetzung = $alf;
       $this->matrix1=$ma;
       return $ma;    
          
    }
    
    function getMatrix()
    {           
      return $this->hilf01;
    } 
    
    function getErsetzung()
    {           
      return $this->ersetzung;
    }
    

    function ersetzeHin($string)
    {//$string ist der zu �bersetzende String, $ uebarray ist das �bersetzungsarray
		if($string=="")
		{ return $out=""; }     

	 $er=$this->ersetzung;
      $msg=str_split($string);      //Konvertiert Alphabet in ein Array
      foreach($msg as $b)
      {
         $out[]=$er[$b];
      }      
      return $out;
    }   
    
    function ersetzeBack($code,$pw)
    {       
      $code=explode(",",$code); //String -> Array, Komma
      $matrix=$this->ersetzung;
      $i=0;
      foreach($matrix as $k => $v) 
            {
            $ueb[$v]=$k;
            $i++;   
            }
      $len=count($pw);
      $j=0;
      for($i=0;$i!=count($code);$i++)
      {
        $zahl=$code[$i]-$pw[$j];
        $result.=$ueb[$zahl];           
        $j++;
        if($len==$j)
        {
          $j=0;
        }         
      }
      return $result;
    }
    
    
    function arrayToString($arr)
    {
      for($i=0;$i!=count($arr);$i++)
      {
        $result.=$arr[$i];
        if($i!=(count($arr)-1))
        {
           $result.=",";
        }  
      }
      return $result;
    }
    
    
    function addArrays($arr1,$arr2)
    {
      $len=count($arr2);$j=0;
      for($i=0;$i!=count($arr1);$i++)
      {
          $wert=($arr1[$i]+$arr2[$j]);
          if($wert>=100)
          {
             $wert-=100;
          }
          $result[$i]=$wert;    
          $j++;
          if($len==$j)
          {
              $j=0;
          }
      }  
      return $result;
    }
	 
	 public function setCleanflag() { $this->clean = 'checked';}

}
?>
