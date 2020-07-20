<?php

namespace Pforret\Outlook;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use function PHPUnit\Framework\throwException;

class PhpOutlookSignature
{
    private string $default_template;
    private string $template_folder;    //<template>
    private string $template_file;      //<template>/<message>.htm
    private string $assets_folder;      //<template>/<message>_files/
    private string $assets_local;       //<message>_files
    private array $keywords;
    private array $included_files;

    public function __construct($folder="")
    {
        $this->default_template="__DIR__/templates/default";
        if(!file_exists($this->default_template)){
            throwException(sprintf("Default template folder [%s] does not exist", $this->default_template));
        }
        if(!$folder){
            $folder=$this->default_template;
        }
        if(!file_exists($this->template_folder)){
            throwException(sprintf("Template folder [%s] does not exist", $this->template_folder));
        }
        $this->template_folder = $folder;
        $this->check_template_files($this->template_folder);
        if(!file_exists($this->assets_folder."/filelist.xml")){
            $this->create_filelist();
        }
        $this->analyze_template_text();
        return $this;
    }

    private function check_template_files($folder)
    {
        $html_files=glob("$folder/*.htm");
        if(count($html_files) === 0){
            throwException(sprintf("Template folder [%s] does not contain a .htm file", $this->template_folder));
        }
        if(count($html_files) > 1){
            throwException(sprintf("Template folder [%s] should only contain 1 .htm file (now: %d)", $this->template_folder,count($html_files)));
        }

        $this->template_file=$html_files[0];
        $template_name=basename($this->template_file,".htm");
        $assets_folder=sprintf("%s/%s",$this->template_folder,"${template_name}_files");
        if(!is_dir($assets_folder)){
            // TODO: look for alternative folder name in template
            throwException(sprintf("Template assets folder [%s] cannot be found", $assets_folder));
            // most probable
        }
        $this->assets_folder=$assets_folder;
        $this->assets_local=basename($assets_folder);
    }

    private function analyze_template_text(){
        $this->keywords=Array();
        $this->included_files=Array();
        $text=file_get_contents($this->template_file);
        $text=str_replace($this->assets_local."/","{assets}/",$text);
        preg_match_all("|\{(\w+)\}|",$text,$matches);
        foreach($matches as $match){
            $keyword=$match[1];
            $this->keywords[$keyword]=$keyword;
        }
        preg_match_all("|({assets}/\w+\.\w+)|",$text,$matches);
        foreach($matches as $match){
            $file=$match[1];
            $this->included_files[$file]=$file;
        }
    }

    public function create_signature($output_file,$values,$ignore_errors=false){
        $output_folder=dirname($output_file);
        $output_name=pathinfo($output_file,PATHINFO_FILENAME);
        $assets_folder="$output_folder/${output_name}_files";
        if(!file_exists($output_folder)){
            mkdir($output_folder,0777,true);
        }
        if(!file_exists($assets_folder)){
            mkdir($assets_folder,0777,true);
        }
        // fill in template
        $text=file_get_contents($this->template_file);
        foreach($this->keywords as $keyword){
            if(!$ignore_errors && !isset($values[$keyword]))
                throwException("Template expects [$keyword] but none was given");
            $value=isset($values[$keyword]) ? $values[$keyword] : "";
            $text=str_replace($keyword,$value,$text);
        }
        // save files
        file_put_contents($output_file,$text);
        foreach($this->included_files as $file){
            $copy_file="$assets_folder/" . basename($file);
            copy($file,$copy_file);
        }
        $install_template=$this->template_folder."/install_signature.cmd";
        $install_script=$output_folder."/".basename($install_template);
        if(file_exists($install_template)){
            $script=file_get_contents($install_template);
            $script=str_replace("{source}","$output_folder",$script);
            $script=str_replace("{destin}","%APPDATA%\Microsoft\Signatures",$script);
            file_put_contents($install_script,$script);
        }
    }

    private function create_filelist(){
        $xml='<xml xmlns:o="urn:schemas-microsoft-com:office:office">' . PHP_EOL;
        $xml.=sprintf(' <o:MainFile HRef="../%s"/>',basename($this->template_file)) . PHP_EOL;
        $assets=glob($this->assets_folder . "/*");
        foreach($assets as $asset){
            $xml.=sprintf(' <o:File HRef="%s"/>',basename($asset)) . PHP_EOL;
        }
        $xml.='</xml>' . PHP_EOL;
        $filelist="$this->assets_folder/filelist.xml";
        file_put_contents($filelist,$xml);
        $this->included_files[$filelist]=$filelist;
    }

}
