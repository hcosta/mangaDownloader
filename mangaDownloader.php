<?php

class mangaDownloader_mr
{	
    /**
     * MANGAREADER VERSION
     * Eg. $obj->download('http://www.mangareader.net/202-13447-1/hatsukoi-limited/chapter-1.html');
     * @param string $manga_url
     */
    
    function download($manga_url)
    {
    	$html = file_get_contents($manga_url);
        $dom = new domDocument;
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        
        /* PRIMERO BUSCAMOS EL NOMBRE DEL CAPITULO Y CREAMOS EL NOMBRE DE LA CARPETA */
        
	    $headings = $dom->getElementsByTagName('h1');
	
		foreach($headings as $h1) 
		{
		   echo "<br>File detected: ".$dir = str_replace(" ", "-", $h1->nodeValue);
		}
	
	  	/* LUEGO BUSCAMOS LAS PAGINAS */
		
    	$div = $dom->getElementById("selectpage");
		$pages = $div->nodeValue;
		
		/* FILTRAMOS */
		echo "<br>Total pages detected: ".$pos = substr($pages, -(strlen($pages)-1-strrpos($pages, "f")));
		
		/* BUSCAMOS LA URL DE LA IMAGEN */
    
        $img = $dom->getElementsByTagName('img');
        $i = 0;
        foreach ($img as $element)
        {
	        if ($i == 0) 
	        {
	            //guardamos la url de la imagen
	            echo "<br>Image detected: ".$imageurl = $element->getAttribute('src');
	            break;
	        }
            $i++;
        }
        
        /* AHORA TENEMOS QUE EXTRAER EL NUMERO DE LA PRIMERA IMAGEN, dandole la vuelta, extrayendo valores etc...*/

         $img_name = strrev(substr(strrev($img_name = urlParameters(5, $imageurl)), 0, strpos(strrev($img_name = urlParameters(5, $imageurl)), "-")));
         echo "<br>Id filtered: ".$id = substr($img_name, 0, strpos($img_name, ".")); //le quitamos la extension
         
         /*OWNED :)*/
         
    	for($i=0;$i<$pos;$i++)
        {
        	//http://i14.mangareader.net/hatsukoi-limited/1/hatsukoi-limited-312443.jpg
            $url = urlParameters(0, $imageurl)."/".urlParameters(1, $imageurl)."/".urlParameters(2, $imageurl)."/".urlParameters(3, $imageurl)."/".urlParameters(4, $imageurl)."/".urlParameters(3, $imageurl)."-".$id.".jpg";
            $id++;
            if (saveImg($url, $i.".jpg", $dir) == 0) $error = 1;
            set_time_limit(20);
        }
    }
}

class mangaDownloader_ax
{	
    /**
     * ANIMEXTREMIST VERSION
     * Le pasas una URL con la siguiente forma: http://www.animextremist.com/mangas-online/air/capitulo-1/airtv1.html
     * La clase descarga el capitulo automaticamente en la carpeta mangas/air-capitulo-1
     * @param string $manga_url
     */
    
    function download($manga_url)
    {
        $html = file_get_contents($manga_url);
        $dom = new domDocument;
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        
        /* PRIMERO SACAMOS LA SERIE Y EL NUMERO DE CAPITULO */
        
        $str1 = urlParameters(2, $manga_url);
        $str2 = urlParameters(3, $manga_url);
        $serie = urlParameters(4, $manga_url);
        $capi = urlParameters(5, $manga_url);
        
        /* LUEGO GUARDAMOS LA URL DE LA PRIMERA IMAGEN */
    
        $links = $dom->getElementsByTagName('img');
        $i = 0;
        foreach ($links as $link)
        {
	        if ($i == 0) 
	        {
	            //guardamos la url de la imagen
	            $imageurl = $link->getAttribute('src');
	            break;
	        }
            $i++;
        }
        //reformateamos la string pa tener la plantilla
        
        $str1 = urlParameters(2, $imageurl);
        $str2 = urlParameters(3, $imageurl);
        $rest = substr(urlParameters(6, $imageurl), 0, -4);
        /* AQUI YA TENEMOS LA URL DONDE ESTAN LAS IMAGENES: http://img2.submanga.com/pages/107/1074632bf/ 
         * EMPEZAMOS UN BUCLE QUE GUARDE LAS IMAGENES HASTA QUE DE ERROR  */  
        
        //OWNED :)
        
        $error = 0;
        for($i=0;$error == 0;$i++)
        {
            if ($i==0) 
            {
                $url = "http://".$str1."/".$str2."/".$serie."/".$capi."/".$rest.".jpg";
                echo $url."<br>";
                if (saveImg($url, $i.".jpg", $serie."-".$capi) == 0) $error = 1;
                set_time_limit(20);
            }
            else 
            {
                $url = "http://".$str1."/".$str2."/".$serie."/".$capi."/".$rest.$i.".jpg";
                if (saveImg($url, $i.".jpg", $serie."-".$capi) == 0) $error = 1;
                set_time_limit(20);
            }
        }
    }	
}

class mangaDownloader_sm
{	
    /**
     * SUBMANGAVERSION
     * Le pasas una URL con la siguiente forma: http://submanga.com/Naruto/536 es necesario el ultimo numero despues del capitulo 107463
     * La clase descarga el capitulo automaticamente en la carpeta mangas/Naruto536
     * @param string $manga_url
     */
    
    function download($manga_url)
    {
        /* parametizamos la url y sacamos todos los datos necesarios */
        $id = urlParameters(5, $manga_url);
        $serie = urlParameters(3, $manga_url);
        $capi = urlParameters(4, $manga_url);

        if ($id != ""){}
        else 
        {
            /* HASTA AQUI SE CONSIGUE LA URL DEL ULTIMO CAPITULO: http://submanga.com/c/107463 */
            $html = file_get_contents($manga_url);
            $dom = new domDocument;
            @$dom->loadHTML($html);
            $dom->preserveWhiteSpace = false;
            
            /*Intentamos conseguir el id de la carpeta */
            $inputs = $dom->getElementsByTagName('input');
            
            foreach($inputs as $element)
            {
                    if ($element->getAttribute('name') == "id")
                            $id = $element->getAttribute('value');
            }
        }
        /* HASTA AQUI SE CONSIGUE LA URL CON ID DEL CAPITULO: http://submanga.com/c/107463 */
        $url_mod = "http://submanga.com/c/".$id;
        
        $html = file_get_contents($url_mod);
        $dom = new domDocument;
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;  
        $links = $dom->getElementsByTagName('img');
        $i = 0;
        
        foreach ($links as $link)
        {
        if ($i == 2) 
        {
            //guardamos el tercer enlace que es el de la imagen
            $imageurl = $link->getAttribute('src');
            break;
        }
            $i++;
        }
        /* AQUI YA TENEMOS LA URL DONDE ESTAN LAS IMAGENES: http://img2.submanga.com/pages/107/1074632bf/ 
         * EMPEZAMOS UN BUCLE QUE GUARDE LAS IMAGENES HASTA QUE DE ERROR   */
        
        /*OWNED :)*/
        
        $error = 0;
        for($i=1;$error == 0;$i++)
        {
            $url = urlParameters(0, $imageurl)."/".urlParameters(1, $imageurl)."/".urlParameters(2, $imageurl)."/".urlParameters(3, $imageurl)."/".urlParameters(4, $imageurl)."/".urlParameters(5, $imageurl)."/".$i.".jpg";
            if (saveImg($url, $i.".jpg", $serie."-".$capi) == 0) $error = 1;
            set_time_limit(20);
        }
    }
    
    /**
     * Le pasas una URL con la siguiente forma: http://submanga.com/Naruto
     * la clase descarga el ultimo capitulo automaticamente
     * @param string $manga_url
     */
    
    function last($manga_url)
    {
        $html = file_get_contents($manga_url);
        $dom = new domDocument;
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        $tables = $dom->getElementsByTagName('table');
        $rows = $tables->item(0)->getElementsByTagName('tr');
        $i = 0;
        foreach ($rows as $row)
        {
            //solo queremos el primero, que es el ultimo capitulo :]
            if ($i == 2) break;
                    $i++;
            /** buscamos los enlaces dentro de la fila ***/	
            $cols = $row->getElementsByTagName('a');
                foreach ($cols as $link)
                {
                    $serie = urlParameters(3, $link->getAttribute('href'));
                    $capi = urlParameters(4, $link->getAttribute('href'));
                        $urlmanga = "http://submanga.com/c/".urlParameters(5, $link->getAttribute('href'));
                        break;
                    };
        } 
        /* HASTA AQUI SE CONSIGUE LA URL DEL ULTIMO CAPITULO: http://submanga.com/c/107463 */
            $html = file_get_contents($urlmanga);
        $dom = new domDocument;
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
    
        $links = $dom->getElementsByTagName('img');
        $i = 0;
            foreach ($links as $link)
            {
	            if ($i == 2) 
	            {
	                //guardamos el tercer enlace que es el de la imagen
	                $imageurl = $link->getAttribute('src');
	                break;
	            }
                $i++;
            }
            /* AQUI YA TENEMOS LA URL DONDE ESTAN LAS IMAGENES: http://img2.submanga.com/pages/107/1074632bf/ 
             * EMPEZAMOS UN BUCLE QUE GUARDE LAS IMAGENES HASTA QUE DE ERROR    */
            
            //OWNED :)
            
            $error = 0;
            for($i=1;$error == 0;$i++)
            {
                $url = urlParameters(0, $imageurl)."/".urlParameters(1, $imageurl)."/".urlParameters(2, $imageurl)."/".urlParameters(3, $imageurl)."/".urlParameters(4, $imageurl)."/".urlParameters(5, $imageurl)."/".$i.".jpg";
                if (saveImg($url, $i.".jpg", $serie."-".$capi) == 0) $error = 1;
                set_time_limit(20);
            }
            
            return $serie."-".$capi;
    }

    /**
     * Descarga el ultimo capitulo de una serie, lo comprime, lo envia por correo y borra los archivos
     * @param String $manga_url
     */
    
    function send_email($manga_url, $dest)
    {
        $count = 30;
        $file = $this->last($manga_url);
        $dir = "mangas/".$file;
        if(file_exists($file))
        {
            echo "$file es viejo!";
            while ($count > 0)
            {
                echo "<br>Intento: $count de 30 para finalizar";
                $count--;
                sleep(1800);
                $file = $this->last($manga_url);
                $dir = "mangas/".$file;
                if(!file_exists($file))
                {	
                    echo "$file es nuevo!";
                    system("cd mangas;tar -pczf ../".$file.".tar.gz ". $file);
                    echo "<br>cd mangas;tar -pczf ../".$file.".tar.gz ".$file;
                    correo($file.".tar.gz", $dest);
                    unlink($file.".tar.gz");
                    $chapter = explode("-",$file);
                    echo "<br>mv ".$chapter[0]."-".--$chapter[1]." ".$chapter[0]."-".++$chapter[1];
                    $chapter = explode("-",$file);
                    system("mv ".$chapter[0]."-".--$chapter[1]." ".$chapter[0]."-".++$chapter[1]);
                    @rrmdir($dir);
                    break;
                }
            }
            if ($count == 0) 
            {
                echo '<br>'.$file.' ...enviando mail de disculpas y borrando directorio temportal';
                @rrmdir($dir);
                $chapter = explode("-",$file);
                die(email_info($dest, ++$chapter[1]." de ".$chapter[0]));
            }
        }
        else 
        {	echo "$file es nuevo!";
            system("cd mangas;tar -pczf ../".$file.".tar.gz ". $file);
            echo "<br>cd mangas;tar -pczf ../".$file.".tar.gz ".$file;
            correo($file.".tar.gz", $dest);
            unlink($file.".tar.gz");
            $chapter = explode("-",$file);
            echo "<br>mv ".$chapter[0]."-".--$chapter[1]." ".$chapter[0]."-".++$chapter[1];
            $chapter = explode("-",$file);
            system("mv ".$chapter[0]."-".--$chapter[1]." ".$chapter[0]."-".++$chapter[1]);
            @rrmdir($dir);
        }
        @rrmdir($dir);
    }
}

/***** FUNCIONES VARIAS QUE USA LA CLASE ******/

/**
 * Guarda una imagen. Le pasas url, nombre y directorio. Devuelve false si no lo consigue o true
 * @param $string $imageurl
 * @param $string $name
 * @param $string $loc
 */

function saveImg($imageurl, $name, $loc)
{	
    if (!file_exists("mangas")) mkdir("mangas");
    if (!file_exists("mangas/$loc")) mkdir("mangas/$loc");
    $image = @file_get_contents($imageurl);
    if ($image)
    {
        if (!file_exists("mangas/$loc/$name"))
        {
            $fp = fopen("mangas/$loc/$name", 'a');
            if($fp) 
            {
                fwrite($fp, $image);
                fclose($fp);
            }
            echo "<br>".$loc." ".$name." guardada!";
            return 1;
        }
        else 
        {
            echo "<br>".$loc." ".$name." ya existe!";
            return 0;
        }
    }
    else 
    {
        echo "<br>".$loc." ".$name." error!";
        return 0;
    }
}

/**
 * Parametiza una url
 * @param $string $segment
 * @param $string $web
 */

function urlParameters($segment, $web)
{
    $navString = $web; // Agafa la URL
    $parts = explode('/', $navString); // La parteix per "/"
    return $parts[$segment];
}

/**
 * Despues de 30 intentos envia un email informando que el capi no se ha publicado esta semana
 * @param String $dest
 * @param String $dir
 */

function email_info($dest, $dir)
{
    $subject = "El capitulo $dir no ha sido publicado. ";
    $to = $dest;
    $message = "Disculpe las molestias :(. \nNo conteste este email, ha sido enviado automaticamente";
    if(@mail($to, $subject,$message, "From: mangaDownloader@robot.com")) echo '<br>mail de disculpas enviado';
}

/**
 * Envia un correo con archivo adjunto
 * @param String $file
 * @param String $user
 */

function correo($file, $user)
{
    $email_from = "mangaDownloader@robot.com"; // Who the email is from 
    $email_subject = "mangaDownloader! $file"; // The Subject of the email 
    $email_message = "$file is on Fire :)"; // Message that the email has in it 
    
    $email_to = $user; // Who the email is too 
    
    $headers = "From: ".$email_from;
    
    $semi_rand = md5(time()); 
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
    
    $headers .= "\nMIME-Version: 1.0\n" . 
     "Content-Type: multipart/mixed;\n" . 
     " boundary=\"{$mime_boundary}\""; 
    
    $email_message .= "This is a multi-part message in MIME format.\n\n" . 
     "--{$mime_boundary}\n" . 
     "Content-Type:text/html; charset=\"iso-8859-1\"\n" . 
     "Content-Transfer-Encoding: 7bit\n\n" . 
    $email_message . "\n\n"; 
    
    $fileatt = $file; // Path to the file 
    $fileatt_type = "application/octet-stream"; // File Type 
    $fileatt_name = $file; // Filename that will be used for the file as the attachment 
    
    $file = fopen($fileatt,'rb'); 
    $data = fread($file,filesize($fileatt)); 
    fclose($file); 
    
    $data = chunk_split(base64_encode($data)); 
    
    $email_message .= "--{$mime_boundary}\n" . 
     "Content-Type: {$fileatt_type};\n" . 
     " name=\"{$fileatt_name}\"\n" . 
     //"Content-Disposition: attachment;\n" . 
     //" filename=\"{$fileatt_name}\"\n" . 
     "Content-Transfer-Encoding: base64\n\n" . 
     $data . "\n\n" . 
     "--{$mime_boundary}\n"; 
    unset($data);
    unset($file);
    unset($fileatt);
    unset($fileatt_type);
    unset($fileatt_name);
    
    $ok = @mail($email_to, $email_subject, $email_message, $headers); 
    
    if($ok)
    { 
        echo "<br>Fichero enviado :)"; 
    } else
    { 
        die("<br>No se ha podido enviar!"); 
    } 
}

/**
 * Borra el contenido de un directorio de forma recursiva
 * @param String $dir
 */

function rrmdir($dir) 
{ 
    if (is_dir($dir)) 
    { 
        $objects = scandir($dir); 
            foreach ($objects as $object) 
            { 
                if ($object != "." && $object != "..") 
                { 
                    if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
                } 
            } 
        reset($objects); 
        rmdir($dir); 
	} 
}
?>